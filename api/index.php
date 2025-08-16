<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
// CORS: allow origin from env/config
$allowedOrigin = getenv('CORS_ORIGIN');
if (!$allowedOrigin) {
    $cfgPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config.json';
    if (is_file($cfgPath)) { $cfg = json_decode(file_get_contents($cfgPath), true) ?: []; $allowedOrigin = $cfg['cors_origin'] ?? 'http://localhost'; }
}
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && $allowedOrigin && strcasecmp($origin, $allowedOrigin) === 0) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: ' . ($allowedOrigin ?: 'http://localhost'));
}
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

// Support paths like /api/..., /mytransfers/api/..., /anything/api/...
$apiIndex = array_search('api', $segments, true);
if ($apiIndex === false) {
    respond(404, ['error' => 'Not Found']);
}
$endpoint = $segments[$apiIndex + 1] ?? '';

$method = $_SERVER['REQUEST_METHOD'];
$authToken = getBearerToken();

// Proxy/capture/replay controls
$USE_ORIGIN = (getenv('MYT_PROXY_ORIGIN') === '1') || (isset($_GET['origin']) && $_GET['origin'] === '1');
$CAPTURE = isset($_GET['capture']) && $_GET['capture'] === '1';
$REPLAY = isset($_GET['replay']) && $_GET['replay'] === '1';
$CAPTURE_SCHEMA = isset($_GET['capture_schema']) && $_GET['capture_schema'] === '1';
$ORIGIN_API_BASE = 'https://www.mytransfers.com/api';

// Compute sub-path to proxy or identify capture key (e.g., "search", "provinces/municipalities")
$subPath = implode('/', array_slice($segments, 1));

// Maintenance mode (from storage/config.json)
$CONFIG_PATH = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config.json';
$MAINTENANCE = false;
if (is_file($CONFIG_PATH)) {
    $cfgRaw = file_get_contents($CONFIG_PATH);
    $cfg = json_decode($cfgRaw, true);
    if (is_array($cfg)) { $MAINTENANCE = !empty($cfg['maintenance']); }
}
if ($MAINTENANCE && !in_array($endpoint, ['health'], true)) {
    http_response_code(503);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Service under maintenance']);
    exit;
}

// REPLAY mode: serve previously captured JSON if available
if ($REPLAY) {
    $key = computeCaptureKey($method, '/' . $subPath, currentQueryParams());
    $cached = loadCapture($key);
    if ($cached !== null) {
        http_response_code($cached['status'] ?? 200);
        header('Content-Type: ' . ($cached['content_type'] ?? 'application/json; charset=utf-8'));
        echo $cached['body'] ?? '';
        exit;
    }
    respond(404, ['error' => 'Replay not found', 'key' => $key]);
}

if ($USE_ORIGIN) {
    // Proxy entire subpath after /api to the real upstream
    [$status, $contentType, $body] = proxyToOriginResponse('/' . $subPath, $ORIGIN_API_BASE, $method);
    if ($CAPTURE) {
        $key = computeCaptureKey($method, '/' . $subPath, currentQueryParams());
        saveCapture($key, $status, $contentType, $body);
    }
    if ($CAPTURE_SCHEMA && $status >= 200 && $status < 300) {
        saveSchema($subPath, $body);
    }
    http_response_code($status);
    header('Content-Type: ' . $contentType);
    echo $body;
    exit;
}

switch ($endpoint) {
    case 'search':
        if ($method !== 'GET') {
            respond(405, ['error' => 'Method Not Allowed']);
        }
        $lang = $_GET['lang'] ?? 'en';
        $query = trim((string)($_GET['query'] ?? ''));
        if ($query === '') {
            respond(400, ['error' => 'Missing query parameter']);
        }
        $results = demoSearch($query, $lang);
        $predictions = [];
        // Prefer Google Places for richer predictions if key is configured
        $useGoogle = loadGoogleKey() !== null;
        if ($useGoogle) {
            $gPreds = googlePlacesAutocomplete($query, $lang);
            foreach ($gPreds as $p) {
                $desc = $p['description'] ?? $query;
                $main = $desc;
                if (strpos($desc, ',') !== false) { $parts = explode(',', $desc); $main = trim($parts[0]); }
                $type = (stripos($desc, 'airport') !== false || stripos($desc, 'havaliman') !== false) ? 'airport' : 'address';
                $predictions[] = [
                    'description' => $desc,
                    'place_id' => $p['place_id'] ?? '',
                    'types' => [$type],
                    'structured_formatting' => [ 'main_text' => $main ]
                ];
            }
        }
        // Fallback/additional demo predictions to ensure something appears
        if (count($predictions) === 0) {
            foreach ($results as $r) {
                $desc = ($r['pickup']['name'] ?? $query);
                $main = $r['pickup']['name'] ?? $query;
                $predictions[] = [
                    'description' => $desc,
                    'place_id' => $r['id'],
                    'types' => ['address'],
                    'structured_formatting' => [ 'main_text' => $main ]
                ];
            }
        }
        respondSchema('search', [
            'code' => 200,
            'response' => [
                'predictions' => $predictions,
                'destinations' => []
            ]
        ]);
        break;

    case 'book':
        if ($method !== 'POST') {
            respond(405, ['error' => 'Method Not Allowed']);
        }
        $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
        // strict validation
        $required = ['pickup_date', 'return_date', 'passengers', 'pickup', 'dropoff'];
        foreach ($required as $f) {
            if (!array_key_exists($f, $data)) { respond(400, ['error' => "Missing field: {$f}"]); }
        }
        // dates
        $pd = (string)$data['pickup_date'];
        $rd = (string)$data['return_date'];
        if (strtotime($pd) === false || strtotime($rd) === false) {
            respond(400, ['error' => 'Invalid date format']);
        }
        // passengers
        $pax = (int)$data['passengers'];
        if ($pax <= 0 || $pax > 50) { respond(400, ['error' => 'Invalid passengers']); }
        // pickup/dropoff structure
        foreach (['pickup','dropoff'] as $k) {
            if (!is_array($data[$k]) || !isset($data[$k]['name'])) { respond(400, ['error'=>"Invalid {$k}"]); }
            if (isset($data[$k]['lat']) && $data[$k]['lat'] !== null && !is_numeric($data[$k]['lat'])) { respond(400, ['error'=>"Invalid {$k}.lat"]); }
            if (isset($data[$k]['lng']) && $data[$k]['lng'] !== null && !is_numeric($data[$k]['lng'])) { respond(400, ['error'=>"Invalid {$k}.lng"]); }
        }

        if ($authToken && $authToken !== 'demo-secret-token') {
            respond(401, ['error' => 'Invalid token']);
        }

        $bookingId = 'bk_' . bin2hex(random_bytes(6));
        $computed = computePriceFromData($data);
        $amount = ['amount' => $computed['final_amount'], 'currency' => 'EUR'];

        $reservation = [
            'booking_id' => $bookingId,
            'status' => 'pending_payment',
            'amount' => $amount,
            'payment' => [
                'type' => 'redirect',
                'url' => '/mytransfers/public/payment.html?booking_id=' . $bookingId
            ],
            'pickup_date' => $data['pickup_date'],
            'return_date' => $data['return_date'],
            'passengers' => $data['passengers'],
            'pickup' => $data['pickup'],
            'dropoff' => $data['dropoff'],
            'offer_id' => $data['offer_id'] ?? 'offer_1',
            'email' => $data['email'] ?? null,
            'coupon_code' => isset($data['coupon_code']) ? (string)$data['coupon_code'] : null,
            'pricing_method' => $computed['pricing_method'] ?? 'distance',
            'from_zone' => $computed['from_zone'] ?? null,
            'to_zone' => $computed['to_zone'] ?? null,
            'created_at' => date('c')
        ];
        saveReservation($reservation);

        // Send email (best-effort)
        if (!empty($reservation['email'])) {
            require_once __DIR__.'/mail.php';
            $token = computeVoucherToken($reservation['booking_id']);
            $voucherUrl = '/mytransfers/public/voucher.html?booking_id=' . rawurlencode($reservation['booking_id']) . '&token=' . rawurlencode($token);
            $vars = [
                'booking_id' => $reservation['booking_id'],
                'status' => $reservation['status'],
                'amount' => number_format((float)$reservation['amount']['amount'], 2) . ' ' . ($reservation['amount']['currency'] ?? 'EUR'),
                'voucher_url' => $voucherUrl,
            ];
            @send_system_mail_template((string)$reservation['email'], 'Your booking '.$reservation['booking_id'], 'reservation.html', $vars);
        }

        respondSchema('reservation', $reservation);
        break;

    case 'reservation':
        // Alias of book
        if ($method !== 'POST') {
            respond(405, ['error' => 'Method Not Allowed']);
        }
        $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
        $data['offer_id'] = $data['offer_id'] ?? ($data['id'] ?? 'offer_1');
        $_POST_RAW = json_encode($data);
        // Reuse logic by faking call
        $bookingId = 'bk_' . bin2hex(random_bytes(6));
        $computed = computePriceFromData($data);
        $reservation = [
            'booking_id' => $bookingId,
            'status' => 'pending_payment',
            'amount' => ['amount' => $computed['final_amount'], 'currency' => 'EUR'],
            'pickup_date' => $data['pickup_date'] ?? null,
            'return_date' => $data['return_date'] ?? null,
            'passengers' => $data['passengers'] ?? null,
            'pickup' => $data['pickup'] ?? null,
            'dropoff' => $data['dropoff'] ?? null,
            'email' => $data['email'] ?? null,
            'pricing_method' => $computed['pricing_method'] ?? 'distance',
            'from_zone' => $computed['from_zone'] ?? null,
            'to_zone' => $computed['to_zone'] ?? null,
            'created_at' => date('c')
        ];
        saveReservation($reservation);
        respond(200, $reservation);
        break;

    case 'booking':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $bookingId = trim((string)($_GET['booking_id'] ?? ''));
        $email = trim((string)($_GET['email'] ?? ''));
        if ($bookingId === '') { respond(400, ['error' => 'Missing booking_id']); }
        // Try DB
        try {
            require_once __DIR__.'/db.php';
            $pdo = get_pdo();
            if ($pdo) {
                if ($email !== '') {
                    $stmt = $pdo->prepare("SELECT booking_id,status,passengers,amount,pickup,dropoff,pickup_date,return_date,created_at, email FROM reservations WHERE booking_id=? AND (email=? OR email IS NULL)");
                    $stmt->execute([$bookingId,$email]);
                } else {
                    $stmt = $pdo->prepare("SELECT booking_id,status,passengers,amount,pickup,dropoff,pickup_date,return_date,created_at, email FROM reservations WHERE booking_id=?");
                    $stmt->execute([$bookingId]);
                }
                $row = $stmt->fetch();
                if ($row) {
                    $res = [
                        'booking_id'=>$row['booking_id'],
                        'status'=>$row['status'],
                        'amount'=>['amount'=>(float)$row['amount'],'currency'=>'EUR'],
                        'pickup_date'=>$row['pickup_date'],
                        'return_date'=>$row['return_date'],
                        'passengers'=>(int)$row['passengers'],
                        'pickup'=>json_decode($row['pickup'], true),
                        'dropoff'=>json_decode($row['dropoff'], true),
                        'email'=>$row['email'],
                        'created_at'=>$row['created_at'],
                    ];
                    respond(200, $res);
                }
            }
        } catch (Throwable $e) { /* ignore */ }
        // Fallback JSON
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
        if (is_file($file)) {
            $items = json_decode(file_get_contents($file), true) ?: [];
            foreach ($items as $r) {
                if (($r['booking_id'] ?? '') === $bookingId) {
                    if ($email!=='' && isset($r['email']) && $r['email']!==$email) continue;
                    respond(200, $r);
                }
            }
        }
        respond(404, ['error'=>'Not found']);
        break;

    case 'voucher':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $bookingId = trim((string)($_GET['booking_id'] ?? ''));
        $token = trim((string)($_GET['token'] ?? ''));
        if ($bookingId === '' || $token === '') { respond(400, ['error' => 'Missing booking_id or token']); }
        if (!hash_equals(computeVoucherToken($bookingId), $token)) {
            respond(403, ['error' => 'Invalid token']);
        }
        // Reuse booking lookup (without email restriction)
        try {
            require_once __DIR__.'/db.php';
            $pdo = get_pdo();
            if ($pdo) {
                $stmt = $pdo->prepare("SELECT booking_id,status,passengers,amount,pickup,dropoff,pickup_date,return_date,created_at, email FROM reservations WHERE booking_id=?");
                $stmt->execute([$bookingId]);
                $row = $stmt->fetch();
                if ($row) {
                    $res = [
                        'booking_id'=>$row['booking_id'],
                        'status'=>$row['status'],
                        'amount'=>['amount'=>(float)$row['amount'],'currency'=>'EUR'],
                        'pickup_date'=>$row['pickup_date'],
                        'return_date'=>$row['return_date'],
                        'passengers'=>(int)$row['passengers'],
                        'pickup'=>json_decode($row['pickup'], true),
                        'dropoff'=>json_decode($row['dropoff'], true),
                        'email'=>$row['email'],
                        'created_at'=>$row['created_at'],
                    ];
                    respond(200, $res);
                }
            }
        } catch (Throwable $e) { /* ignore */ }
        // Fallback JSON
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
        if (is_file($file)) {
            $items = json_decode(file_get_contents($file), true) ?: [];
            foreach ($items as $r) {
                if (($r['booking_id'] ?? '') === $bookingId) {
                    respond(200, $r);
                }
            }
        }
        respond(404, ['error'=>'Not found']);
        break;

    case 'list':
        if ($method !== 'GET') {
            respond(405, ['error' => 'Method Not Allowed']);
        }
        // Support MyTransfers style query params from /en/search/?...
        // arrival_* represents FROM on original site sometimes; we compute distance either way
        $lat1 = isset($_GET['arrival_lat']) ? (float)$_GET['arrival_lat'] : (float)($_GET['lat1'] ?? 0);
        $lng1 = isset($_GET['arrival_lng']) ? (float)$_GET['arrival_lng'] : (float)($_GET['lng1'] ?? 0);
        $lat2 = isset($_GET['departure_lat']) ? (float)$_GET['departure_lat'] : (float)($_GET['lat2'] ?? 0);
        $lng2 = isset($_GET['departure_lng']) ? (float)$_GET['departure_lng'] : (float)($_GET['lng2'] ?? 0);
        $km = 20.0;
        if ($lat1 && $lng1 && $lat2 && $lng2) {
            $km = haversine($lat1, $lng1, $lat2, $lng2);
        }
        $adults = (int)($_GET['adults'] ?? $_GET['passengers'] ?? 2);
        $vehicleTypes = [
            ['name' => 'Sedan', 'min' => 1, 'max' => 3, 'bags' => 2, 'mult' => 1.00],
            ['name' => 'Minivan', 'min' => 1, 'max' => 6, 'bags' => 6, 'mult' => 1.35],
            ['name' => 'Minibus', 'min' => 7, 'max' => 12, 'bags' => 10, 'mult' => 1.85],
            ['name' => 'Bus', 'min' => 13, 'max' => 50, 'bags' => 40, 'mult' => 2.60],
        ];
        $pricingPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'pricing.json';
        $pricing = is_file($pricingPath) ? json_decode(file_get_contents($pricingPath), true) : [];
        $basePerKm = is_array($pricing) && isset($pricing['base_per_km']) ? (float)$pricing['base_per_km'] : 1.2;
        $includes = [['name' => 'Private transfer'], ['name' => 'Meet & Greet'], ['name' => 'Door to door']];
        $results = [];
        foreach ($vehicleTypes as $v) {
            if ($adults > $v['max']) { continue; }
            $vm = is_array($pricing) && isset($pricing['vehicle_multipliers'][$v['name']]) ? (float)$pricing['vehicle_multipliers'][$v['name']] : (float)$v['mult'];
            $amount = max(10.0, round($km * $basePerKm * $vm, 2));
            $results[] = [
                'transportName' => $v['name'],
                'minPassengers' => $v['min'],
                'maxPassengers' => $v['max'],
                'suitcases' => $v['bags'],
                'price' => $amount,
                'breakdown' => [
                    'label' => 'Final price',
                    'price_old' => null
                ],
                'includes' => $includes,
                'route' => [ 'distance_km' => round($km, 1) ]
            ];
        }
        if (!$results) {
            // fallback at least one
            $results[] = [ 'transportName' => 'Sedan', 'minPassengers'=>1, 'maxPassengers'=>3, 'suitcases'=>2, 'price'=>max(10.0, round($km * $basePerKm,2)), 'includes'=>$includes, 'route'=>['distance_km'=>round($km,1)], 'breakdown'=>['label'=>'Final price','price_old'=>null] ];
        }
        respondSchema('list', [ 'results' => $results, 'distance' => ['km' => round($km, 2)] ]);
        break;

    case 'checkout':
        if ($method !== 'POST') {
            respond(405, ['error' => 'Method Not Allowed']);
        }
        $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
        $bid = $data['booking_id'] ?? ('bk_' . bin2hex(random_bytes(4)));
        respondSchema('checkout', [
            'status' => 'ok',
            'payment' => [
                'type' => 'redirect',
                'url' => '/mytransfers/public/payment.html?booking_id=' . $bid
            ]
        ]);
        break;

    case 'payment/create':
        // Create payment session based on configured provider
        if ($method !== 'POST') { respond(405, ['error' => 'Method Not Allowed']); }
        $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
        $bookingId = trim((string)($data['booking_id'] ?? ''));
        if ($bookingId === '') { respond(400, ['error' => 'Missing booking_id']); }
        $amountMinor = (int)round(((float)($data['amount'] ?? 0)) * 100);
        $currency = strtoupper((string)($data['currency'] ?? loadCurrency()));
        $provider = loadPaymentProvider();
        if ($provider === 'mock') {
            $sessionId = 'ps_' . bin2hex(random_bytes(6));
            respond(200, [
                'provider' => 'mock',
                'session_id' => $sessionId,
                'checkout_url' => '/mytransfers/public/payment.html?booking_id=' . rawurlencode($bookingId) . '&session_id=' . rawurlencode($sessionId)
            ]);
        }
        if ($provider === 'iyzico') {
            $res = iyzicoCreateCheckout($bookingId, $amountMinor, $currency);
            if (!empty($res['error'])) { respond(400, $res); }
            respond(200, [
                'provider' => 'iyzico',
                'session_id' => $res['token'] ?? '',
                'checkout_url' => $res['paymentPageUrl'] ?? ''
            ]);
        }
        // Placeholder for future providers (stripe, adyen, checkout)
        respond(400, ['error' => 'Provider not configured', 'provider' => $provider, 'currency' => $currency, 'amount_minor' => $amountMinor]);
        break;

    case 'payment/test':
        // Simple provider connectivity test
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $provider = loadPaymentProvider();
        if ($provider === 'mock') {
            respond(200, ['ok' => true, 'provider' => 'mock']);
        }
        if ($provider === 'iyzico') {
            $bid = 'test_' . bin2hex(random_bytes(4));
            $res = iyzicoCreateCheckout($bid, 100, loadCurrency()); // 1.00 currency
            if (!empty($res['error'])) { respond(200, ['ok' => false, 'provider' => 'iyzico', 'detail' => $res]); }
            respond(200, ['ok' => true, 'provider' => 'iyzico', 'detail' => ['hasUrl' => !empty($res['paymentPageUrl'])]]);
        }
        respond(200, ['ok' => false, 'provider' => $provider, 'detail' => 'not implemented']);
        break;

    case 'payment/confirm':
        // Called by the front-end after mock payment succeeds
        if ($method !== 'POST') { respond(405, ['error' => 'Method Not Allowed']); }
        $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
        $bookingId = trim((string)($data['booking_id'] ?? ''));
        if ($bookingId === '') { respond(400, ['error' => 'Missing booking_id']); }
        updateReservationStatus($bookingId, 'paid');
        // email notification with voucher link (best-effort)
        try {
            $res = getReservationById($bookingId);
            if ($res && !empty($res['email'])) {
                require_once __DIR__.'/mail.php';
                $token = computeVoucherToken($bookingId);
                $voucherUrl = '/mytransfers/public/voucher.html?booking_id=' . rawurlencode($bookingId) . '&token=' . rawurlencode($token);
                $vars = [
                    'booking_id' => $bookingId,
                    'status' => 'paid',
                    'amount' => isset($res['amount']['amount']) ? (number_format((float)$res['amount']['amount'], 2) . ' EUR') : '',
                    'voucher_url' => $voucherUrl,
                ];
                @send_system_mail_template((string)$res['email'], 'Payment received '.$bookingId, 'reservation.html', $vars);
            }
        } catch (Throwable $e) { /* ignore */ }
        respond(200, ['ok' => true]);
        break;

    case 'payment/cancel':
        if ($method !== 'POST') { respond(405, ['error' => 'Method Not Allowed']); }
        $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
        $bookingId = trim((string)($data['booking_id'] ?? ''));
        if ($bookingId === '') { respond(400, ['error' => 'Missing booking_id']); }
        updateReservationStatus($bookingId, 'canceled');
        respond(200, ['ok' => true]);
        break;

    case 'payment/webhook':
        // iyzico callback/webhook endpoint (GET or POST by provider)
        // We only need booking_id and status (paid/failure) confirmation
        $bookingId = $_POST['bookingId'] ?? $_GET['bookingId'] ?? '';
        $status = $_POST['status'] ?? $_GET['status'] ?? '';
        // Log webhook payload
        try {
            $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
            if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
            $payload = json_encode(['get'=>$_GET,'post'=>$_POST], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            @file_put_contents($logDir.DIRECTORY_SEPARATOR.'webhook.log', date('c')." bookingId=".$bookingId." status=".$status." payload=".$payload."\n", FILE_APPEND);
        } catch (Throwable $e) { /* ignore */ }
        if ($bookingId === '') { respond(400, ['error' => 'Missing bookingId']); }
        // For real use, parse iyzico callback payload and verify via iyzico API (RetrieveCheckoutForm)
        if (strtolower((string)$status) === 'success' || strtolower((string)$status) === 'paid') {
            updateReservationStatus((string)$bookingId, 'paid');
        } else {
            updateReservationStatus((string)$bookingId, 'canceled');
        }
        respond(200, ['ok' => true]);
        break;

    case 'voucher/token':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $bookingId = trim((string)($_GET['booking_id'] ?? ''));
        if ($bookingId === '') { respond(400, ['error' => 'Missing booking_id']); }
        $token = computeVoucherToken($bookingId);
        respond(200, ['token' => $token]);
        break;

    case 'countries':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $custom = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'countries.json';
        if (is_file($custom)) {
            $raw = file_get_contents($custom);
            $arr = json_decode($raw, true);
            if (is_array($arr)) {
                respondSchema('countries', $arr);
            }
        }
        respondSchema('countries', [
            ['code' => 'ES', 'name' => 'Spain'],
            ['code' => 'FR', 'name' => 'France'],
            ['code' => 'DE', 'name' => 'Germany'],
            ['code' => 'GB', 'name' => 'United Kingdom']
        ]);
        break;

    case 'destinations':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'destinations.json';
        if (is_file($path)) {
            $raw = file_get_contents($path);
            $arr = json_decode($raw, true);
            if (is_array($arr)) {
                respondSchema('destinations', $arr);
            }
        }
        respondSchema('destinations', [
            [
                'name' => 'Madrid Airport',
                'image' => '/assets/mytransfersweb/prod/images/airports/1095.jpg',
                'url' => '/en/destination/spain/madrid-airport-barajas-mad/'
            ],
            [
                'name' => 'Barcelona Airport',
                'image' => '/assets/mytransfersweb/prod/images/airports/799.jpg',
                'url' => '/en/destination/spain/barcelona-airport-bcn/'
            ]
        ]);
        break;

    case 'predictions':
        if ($method === 'POST') {
            $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
            respondSchema('predictions_save', ['ok' => true, 'received' => $data]);
        }
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        // If local Google key configured, use real Places Autocomplete; else keep stub
        $q = trim((string)($_GET['q'] ?? $_GET['query'] ?? ''));
        if ($q === '') { respond(400, ['error' => 'Missing q or query']); }
        $useGoogle = loadGoogleKey() !== null;
        if ($useGoogle) {
            $results = googlePlacesAutocomplete($q, (string)($_GET['lang'] ?? 'en'));
            respondSchema('predictions', $results);
        }
        respondSchema('predictions', [
            ['description' => $q . ' Airport', 'place_id' => 'pl_1'],
            ['description' => $q . ' City Center', 'place_id' => 'pl_2']
        ]);
        break;

    case 'prediction':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $placeId = trim((string)($_GET['place_id'] ?? ''));
        if ($placeId === '') { respond(400, ['error' => 'Missing place_id']); }
        $useGoogle = loadGoogleKey() !== null;
        if ($useGoogle) {
            $coords = googlePlaceDetails($placeId);
            respondSchema('prediction', $coords);
        }
        respondSchema('prediction', [ 'place_id' => $placeId, 'lat' => 40.4168, 'lng' => -3.7038 ]);
        break;

    case 'provinces':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        respondSchema('provinces', [
            ['code' => 'MD', 'name' => 'Madrid'],
            ['code' => 'CT', 'name' => 'Catalonia']
        ]);
        break;

    case 'provinces/municipalities':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        respondSchema('provinces/municipalities', [
            ['code' => 'MAD', 'name' => 'Madrid'],
            ['code' => 'BCN', 'name' => 'Barcelona']
        ]);
        break;

    case 'distance':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $lat1 = (float)($_GET['lat1'] ?? 40.472);
        $lng1 = (float)($_GET['lng1'] ?? -3.56);
        $lat2 = (float)($_GET['lat2'] ?? 40.4168);
        $lng2 = (float)($_GET['lng2'] ?? -3.7038);
        $km = haversine($lat1, $lng1, $lat2, $lng2);
        respondSchema('distance', ['distance_km' => round($km, 2)]);
        break;

    case 'url':
        if ($method !== 'POST') { respond(405, ['error' => 'Method Not Allowed']); }
        respondSchema('url', ['short' => '/s/' . bin2hex(random_bytes(3))]);
        break;

    case 'viesCheck':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        respondSchema('viesCheck', ['valid' => false]);
        break;

    case 'requote':
        if ($method !== 'POST') { respond(405, ['error' => 'Method Not Allowed']); }
        $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
        respondSchema('requote', ['price' => ['amount' => 49.90, 'currency' => 'EUR'], 'echo' => $data]);
        break;

    case 'coupons':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'coupons.json';
        if (is_file($path)) {
            $raw = file_get_contents($path);
            $arr = json_decode($raw, true);
            if (is_array($arr)) { respondSchema('coupons', $arr); }
        }
        respondSchema('coupons', []);
        break;

    case 'coupon':
        // validate coupon: POST { code }
        if ($method !== 'POST') { respond(405, ['error' => 'Method Not Allowed']); }
        $data = json_decode((string)file_get_contents('php://input'), true) ?? [];
        $code = strtoupper(trim((string)($data['code'] ?? '')));
        if ($code === '') { respond(400, ['error' => 'Missing code']); }
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'coupons.json';
        $list = is_file($path) ? json_decode(file_get_contents($path), true) : [];
        if (!is_array($list)) { $list = []; }
        $found = null;
        foreach ($list as $c) {
            if (strtoupper((string)($c['code'] ?? '')) === $code) { $found = $c; break; }
        }
        if (!$found) { respond(404, ['error' => 'Invalid coupon']); }
        respondSchema('coupon', $found);
        break;

    case 'pricing':
        if ($method !== 'GET') { respond(405, ['error' => 'Method Not Allowed']); }
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'pricing.json';
        if (is_file($path)) {
            $raw = file_get_contents($path);
            $arr = json_decode($raw, true);
            if (is_array($arr)) { respondSchema('pricing', $arr); }
        }
        respondSchema('pricing', [
            'base_per_km' => 1.2,
            'vehicle_multipliers' => [ 'Sedan' => 1.0, 'Minivan' => 1.4 ],
            'region_multipliers' => [ 'default' => 1.0 ]
        ]);
        break;

    case 'language':
        if ($method === 'POST') {
            // Set language preference
            $data = json_decode(file_get_contents('php://input'), true) ?: [];
            $lang = $data['language'] ?? '';
            
            // Validate language
            $valid_languages = ['en', 'tr', 'de', 'fr', 'es'];
            if (!in_array($lang, $valid_languages)) {
                respond(400, ['error' => 'Invalid language']);
            }
            
            // Set cookie
            $expires = time() + (365 * 24 * 60 * 60); // 1 year
            setcookie('site_language', $lang, $expires, '/');
            
            // Start session if not started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['site_language'] = $lang;
            
            respond(200, ['language' => $lang, 'status' => 'success']);
        } elseif ($method === 'GET') {
            // Get current language preference
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $current_lang = $_COOKIE['site_language'] ?? $_SESSION['site_language'] ?? 'en';
            respond(200, ['language' => $current_lang]);
        } else {
            respond(405, ['error' => 'Method Not Allowed']);
        }
        break;

    default:
        respond(404, ['error' => 'Unknown endpoint']);
}

function respond(int $code, array $payload): void {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function respondSchema(string $name, $payload, int $code = 200): void {
    // If we have captured schema for this name, attempt to align keys order/shape later (future work)
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    if (!is_array($payload)) {
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    exit;
}

function getBearerToken(): ?string {
    $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (stripos($hdr, 'Bearer ') === 0) {
        return trim(substr($hdr, 7));
    }
    return null;
}

function demoSearch(string $query, string $lang): array {
    return [
        [
            'id' => 'offer_1',
            'pickup' => ['name' => $query . ' Airport', 'lat' => 40.472, 'lng' => -3.560],
            'dropoff' => ['name' => 'City Center', 'lat' => 40.416, 'lng' => -3.703],
            'distance_km' => 25.4,
            'duration_sec' => 1800,
            'vehicle' => ['type' => 'Sedan', 'capacity' => 3, 'bags' => 2],
            'price' => ['amount' => 35.00, 'currency' => 'EUR'],
            'supplier' => ['name' => 'Demo Supplier']
        ],
        [
            'id' => 'offer_2',
            'pickup' => ['name' => $query . ' Airport', 'lat' => 40.472, 'lng' => -3.560],
            'dropoff' => ['name' => 'City Center', 'lat' => 40.416, 'lng' => -3.703],
            'distance_km' => 25.4,
            'duration_sec' => 1800,
            'vehicle' => ['type' => 'Minivan', 'capacity' => 6, 'bags' => 6],
            'price' => ['amount' => 52.00, 'currency' => 'EUR'],
            'supplier' => ['name' => 'Demo Supplier']
        ]
    ];
}

function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float {
    $earthRadius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

/**
 * Reverse-proxy the request to the real MyTransfers API and stream back the response unchanged.
 */
function proxyToOriginResponse(string $path, string $originBase, string $method): array {
    $originBase = rtrim($originBase, '/');
    $path = '/' . ltrim($path, '/');

    // Build query string, filter out our control param 'origin'
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    parse_str($qs, $queryParams);
    unset($queryParams['origin'], $queryParams['capture'], $queryParams['replay']);
    $queryString = http_build_query($queryParams);

    $url = $originBase . $path . ($queryString !== '' ? ('?' . $queryString) : '');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $headers = [];
    $incomingAuth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if ($incomingAuth !== '') {
        $headers[] = 'Authorization: ' . $incomingAuth;
    }
    $headers[] = 'Accept: application/json, text/plain, */*';
    $headers[] = 'X-Requested-With: XMLHttpRequest';
    $headers[] = 'Referer: https://www.mytransfers.com/en/';
    $headers[] = 'Origin: https://www.mytransfers.com';
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $headers[] = 'Accept-Language: ' . $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        $rawBody = file_get_contents('php://input') ?: '';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawBody);
        $contentType = $_SERVER['CONTENT_TYPE'] ?? 'application/json';
        $headers[] = 'Content-Type: ' . $contentType;
    }

    // User-Agent and cookies to mimic browser
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36');
    // Persist cookies across requests in a jar, also perform warmup to set CF/app cookies
    $cookieDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cookies';
    if (!is_dir($cookieDir)) { @mkdir($cookieDir, 0775, true); }
    $cookieJar = $cookieDir . DIRECTORY_SEPARATOR . 'jar.txt';
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Warmup: fetch homepage once to obtain cookies if jar is empty or small
    if (!file_exists($cookieJar) || filesize($cookieJar) < 10) {
        $warm = curl_init('https://www.mytransfers.com/en/');
        curl_setopt($warm, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($warm, CURLOPT_HEADER, false);
        curl_setopt($warm, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($warm, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0');
        curl_setopt($warm, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($warm, CURLOPT_COOKIEFILE, $cookieJar);
        @curl_exec($warm);
        curl_close($warm);
    }

    $response = curl_exec($ch);
    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return [502, 'application/json; charset=utf-8', json_encode(['error' => 'Upstream error', 'detail' => $err])];
    }

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    $rawHeaders = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    // Pass through essential headers
    $contentType = 'application/json; charset=utf-8';
    foreach (explode("\r\n", $rawHeaders) as $hdrLine) {
        if (stripos($hdrLine, 'Content-Type:') === 0) {
            $contentType = trim(substr($hdrLine, strlen('Content-Type:')));
            break;
        }
    }

    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        proxyLog(sprintf("%s %s -> %d %s", $method, $url, $status, $contentType));
    }
    return [$status, $contentType, $body];
}

/** Capture helpers **/
function getCaptureDir(): string {
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'captures';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

function currentQueryParams(): array {
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    parse_str($qs, $queryParams);
    unset($queryParams['origin'], $queryParams['capture'], $queryParams['replay']);
    ksort($queryParams);
    return $queryParams;
}

function computeCaptureKey(string $method, string $path, array $queryParams): string {
    $base = strtoupper($method) . ' ' . $path . '?' . http_build_query($queryParams);
    return sha1($base);
}

function saveCapture(string $key, int $status, string $contentType, string $body): void {
    $file = getCaptureDir() . DIRECTORY_SEPARATOR . $key . '.json';
    $meta = [
        'saved_at' => date('c'),
        'status' => $status,
        'content_type' => $contentType,
        'body' => $body,
    ];
    file_put_contents($file, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function loadCapture(string $key): ?array {
    $file = getCaptureDir() . DIRECTORY_SEPARATOR . $key . '.json';
    if (!is_file($file)) {
        return null;
    }
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
}

function saveSchema(string $subPath, string $body): void {
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'schemas';
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    // Use subPath as file name, sanitize slashes
    $file = $dir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], '_', $subPath) . '.json';
    @file_put_contents($file, $body);
}

function proxyLog(string $line): void {
    $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
    $file = $logDir . DIRECTORY_SEPARATOR . 'proxy.log';
    // mask emails and bearer tokens
    $line = preg_replace('/Bearer\s+[A-Za-z0-9\-\._~\+\/=]+/i', 'Bearer ***', $line);
    $line = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '***@***', $line);
    @file_put_contents($file, date('c') . ' ' . $line . "\n", FILE_APPEND);
}

function computeVoucherToken(string $bookingId): string {
    // Simple HMAC based on a secret from storage/config.json or fallback
    $secret = 'local-secret';
    $cfgPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config.json';
    if (is_file($cfgPath)) {
        $cfg = json_decode(file_get_contents($cfgPath), true) ?: [];
        if (!empty($cfg['voucher_secret'])) { $secret = (string)$cfg['voucher_secret']; }
    }
    return hash_hmac('sha256', $bookingId, $secret);
}

function loadPaymentProvider(): string {
    $cfgPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config.json';
    if (is_file($cfgPath)) {
        $cfg = json_decode(file_get_contents($cfgPath), true) ?: [];
        if (!empty($cfg['payment_provider'])) { return (string)$cfg['payment_provider']; }
    }
    return 'mock';
}

function loadCurrency(): string {
    $cfgPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config.json';
    if (is_file($cfgPath)) {
        $cfg = json_decode(file_get_contents($cfgPath), true) ?: [];
        if (!empty($cfg['currency'])) { return strtoupper((string)$cfg['currency']); }
    }
    return 'EUR';
}

function iyzicoConfig(): array {
    $cfgPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config.json';
    $cfg = is_file($cfgPath) ? (json_decode(file_get_contents($cfgPath), true) ?: []) : [];
    return [
        'api_key' => (string)($cfg['iyzico_api_key'] ?? ''),
        'secret' => (string)($cfg['iyzico_secret'] ?? ''),
        'base_url' => (string)($cfg['iyzico_base_url'] ?? 'https://sandbox-api.iyzipay.com'),
        'callback' => (string)($cfg['iyzico_callback_url'] ?? ''),
    ];
}

function iyzicoCreateCheckout(string $bookingId, int $amountMinor, string $currency): array {
    $c = iyzicoConfig();
    if ($c['api_key'] === '' || $c['secret'] === '') {
        return ['error' => 'Missing iyzico credentials'];
    }
    // Minimal create checkout form request (hosted payment page)
    $price = number_format($amountMinor / 100, 2, '.', '');
    $payload = [
        'locale' => 'en',
        'conversationId' => $bookingId,
        'price' => $price,
        'paidPrice' => $price,
        'currency' => $currency,
        'basketId' => $bookingId,
        'callbackUrl' => $c['callback'] ?: (($_SERVER['REQUEST_SCHEME'] ?? 'http').'://'.($_SERVER['HTTP_HOST'] ?? 'localhost').'/mytransfers/api/payment/webhook?bookingId='.$bookingId),
        'buyer' => [
            'id' => 'BYR'.$bookingId,
            'name' => 'Name', 'surname' => 'Surname',
            'identityNumber' => '11111111111',
            'email' => 'noreply@example.com',
            'registrationAddress' => 'Address',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'country' => 'TR'
        ],
        'shippingAddress' => [ 'contactName' => 'Name', 'address' => 'Address', 'country' => 'TR', 'city' => 'Istanbul' ],
        'billingAddress' => [ 'contactName' => 'Name', 'address' => 'Address', 'country' => 'TR', 'city' => 'Istanbul' ],
        'basketItems' => [ [ 'id' => 'srv', 'name' => 'Transfer', 'category1' => 'Transport', 'itemType' => 'VIRTUAL', 'price' => $price ] ]
    ];
    // iyzico REST with auth headers
    $url = rtrim($c['base_url'], '/').'/payment/iyzipos/checkoutform/initialize/auth/ecom';
    $headers = [
        'Content-Type: application/json',
        'Authorization: IYZWS ' . $c['api_key'] . ':' . base64_encode(hash_hmac('sha1', json_encode($payload), $c['secret'], true)),
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $raw = curl_exec($ch);
    if ($raw === false) {
        $err = curl_error($ch); curl_close($ch);
        return ['error' => 'iyzico error', 'detail' => $err];
    }
    curl_close($ch);
    $resp = json_decode($raw, true) ?: [];
    // Expected keys (sandbox): status, checkoutFormContent (HTML), token, paymentPageUrl (if exists)
    if (($resp['status'] ?? '') !== 'success') {
        return ['error' => 'iyzico failed', 'response' => $resp];
    }
    // If only HTML form provided, we return a simple relay page or data URL; prefer paymentPageUrl if present
    if (!empty($resp['paymentPageUrl'])) {
        return ['token' => $resp['token'] ?? '', 'paymentPageUrl' => $resp['paymentPageUrl']];
    }
    return ['token' => $resp['token'] ?? '', 'paymentPageUrl' => ''];
}

// --- Reservations storage ---
function saveReservation(array $reservation): void {
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'reservations';
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    $file = $dir . DIRECTORY_SEPARATOR . 'reservations.json';
    $items = [];
    if (is_file($file)) {
        $raw = file_get_contents($file);
        $items = json_decode($raw, true) ?: [];
    }
    array_unshift($items, $reservation);
    file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    // Also try to persist into DB (best-effort)
    saveReservationDb($reservation);
}

function saveReservationDb(array $r): void {
    require_once __DIR__ . '/db.php';
    $pdo = get_pdo();
    if (!$pdo) { return; }
    try {
        $stmt = $pdo->prepare("INSERT INTO reservations (booking_id,status,passengers,amount,pickup,dropoff,pickup_date,return_date,pricing_method,from_zone,to_zone,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $booking = (string)($r['booking_id'] ?? '');
        $status = (string)($r['status'] ?? 'pending_payment');
        $pax = (int)($r['passengers'] ?? 0);
        $amount = (float)($r['amount']['amount'] ?? 0);
        $pickup = json_encode($r['pickup'] ?? []);
        $dropoff = json_encode($r['dropoff'] ?? []);
        $pd = $r['pickup_date'] ?? null;
        $rd = $r['return_date'] ?? null;
        $pm = $r['pricing_method'] ?? null;
        $fz = $r['from_zone'] ?? null;
        $tz = $r['to_zone'] ?? null;
        $created = $r['created_at'] ?? date('c');
        $stmt->execute([$booking,$status,$pax,$amount,$pickup,$dropoff,$pd,$rd,$pm,$fz,$tz,$created]);
    } catch (Throwable $e) {
        // ignore DB errors silently
    }
}

function getReservationById(string $bookingId): ?array {
    // Try DB first
    try {
        require_once __DIR__.'/db.php';
        $pdo = get_pdo();
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT booking_id,status,passengers,amount,pickup,dropoff,pickup_date,return_date,created_at,email FROM reservations WHERE booking_id=?");
            $stmt->execute([$bookingId]);
            $row = $stmt->fetch();
            if ($row) {
                return [
                    'booking_id'=>$row['booking_id'],
                    'status'=>$row['status'],
                    'amount'=>['amount'=>(float)$row['amount'],'currency'=>'EUR'],
                    'pickup_date'=>$row['pickup_date'],
                    'return_date'=>$row['return_date'],
                    'passengers'=>(int)$row['passengers'],
                    'pickup'=>json_decode($row['pickup'], true),
                    'dropoff'=>json_decode($row['dropoff'], true),
                    'email'=>$row['email'],
                    'created_at'=>$row['created_at'],
                ];
            }
        }
    } catch (Throwable $e) { /* ignore */ }
    // Fallback JSON
    $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
    if (is_file($file)) {
        $items = json_decode(file_get_contents($file), true) ?: [];
        foreach ($items as $r) {
            if (($r['booking_id'] ?? '') === $bookingId) { return $r; }
        }
    }
    return null;
}

function updateReservationStatus(string $bookingId, string $status): void {
    // Update JSON
    $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
    if (is_file($file)) {
        $items = json_decode(file_get_contents($file), true) ?: [];
        foreach ($items as &$it) {
            if (($it['booking_id'] ?? '') === $bookingId) {
                $it['status'] = $status;
                break;
            }
        }
        file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
    // Update DB
    try {
        require_once __DIR__.'/db.php';
        $pdo = get_pdo();
        if ($pdo) {
            $stmt = $pdo->prepare("UPDATE reservations SET status=? WHERE booking_id=?");
            $stmt->execute([$status, $bookingId]);
        }
    } catch (Throwable $e) { /* ignore */ }
}

// --- Pricing & coupons ---
function computePriceFromData(array $data): array {
    $amount = 49.90;
    $pricingMethod = 'distance';
    $fromZone = null;
    $toZone = null;
    // Try zone matrix first
    $zoneHit = lookupZoneMatrixPrice($data);
    if ($zoneHit !== null) {
        $amount = (float)$zoneHit['price'];
        $pricingMethod = 'matrix';
        $fromZone = $zoneHit['from_zone'];
        $toZone = $zoneHit['to_zone'];
    }
    $pickupLat = $data['pickup']['lat'] ?? null;
    $pickupLng = $data['pickup']['lng'] ?? null;
    $dropLat = $data['dropoff']['lat'] ?? null;
    $dropLng = $data['dropoff']['lng'] ?? null;
    if ($zoneHit === null && $pickupLat !== null && $pickupLng !== null && $dropLat !== null && $dropLng !== null) {
        $km = haversine((float)$pickupLat, (float)$pickupLng, (float)$dropLat, (float)$dropLng);
        $pricingPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'pricing.json';
        $pricing = is_file($pricingPath) ? json_decode(file_get_contents($pricingPath), true) : [];
        $basePerKm = is_array($pricing) && isset($pricing['base_per_km']) ? (float)$pricing['base_per_km'] : 1.2;
        $vehicleType = (string)($data['vehicle']['type'] ?? 'Sedan');
        $vm = is_array($pricing) && isset($pricing['vehicle_multipliers'][$vehicleType]) ? (float)$pricing['vehicle_multipliers'][$vehicleType] : 1.0;
        $rm = is_array($pricing) && isset($pricing['region_multipliers']['default']) ? (float)$pricing['region_multipliers']['default'] : 1.0;
        $amount = max(10.0, round($km * $basePerKm * $vm * $rm, 2));
    }
    // Apply coupon if present
    $code = strtoupper(trim((string)($data['coupon_code'] ?? '')));
    if ($code !== '') {
        $couponsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'coupons.json';
        $list = is_file($couponsPath) ? json_decode(file_get_contents($couponsPath), true) : [];
        if (is_array($list)) {
            foreach ($list as $c) {
                if (strtoupper((string)($c['code'] ?? '')) === $code) {
                    if (($c['discount_type'] ?? '') === 'percent') {
                        $amount = round($amount * (1 - (float)$c['discount_value'] / 100), 2);
                    } elseif (($c['discount_type'] ?? '') === 'fixed') {
                        $amount = max(0, round($amount - (float)$c['discount_value'], 2));
                    }
                    break;
                }
            }
        }
    }
    return [
        'final_amount' => $amount,
        'pricing_method' => $pricingMethod,
        'from_zone' => $fromZone,
        'to_zone' => $toZone,
    ];
}

// --- Google Places helpers ---
function loadGoogleKey(): ?string {
    $cfg = __DIR__ . '/config.php';
    if (is_file($cfg)) {
        require_once $cfg;
        if (defined('GOOGLE_API_KEY') && GOOGLE_API_KEY && GOOGLE_API_KEY !== 'REPLACE_WITH_YOUR_GOOGLE_API_KEY') {
            return GOOGLE_API_KEY;
        }
    }
    $env = getenv('GOOGLE_API_KEY');
    return $env ? $env : null;
}

function googlePlacesAutocomplete(string $input, string $lang): array {
    $key = loadGoogleKey();
    if (!$key) { return []; }
    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input=' . rawurlencode($input) . '&language=' . rawurlencode($lang) . '&key=' . rawurlencode($key);
    $resp = httpGetJson($url);
    $preds = $resp['predictions'] ?? [];
    $out = [];
    foreach ($preds as $p) {
        $out[] = [
            'description' => $p['description'] ?? ($p['structured_formatting']['main_text'] ?? ''),
            'place_id' => $p['place_id'] ?? '',
        ];
    }
    return $out;
}

function googlePlaceDetails(string $placeId): array {
    $key = loadGoogleKey();
    if (!$key) { return ['place_id' => $placeId]; }
    $url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . rawurlencode($placeId) . '&key=' . rawurlencode($key) . '&fields=geometry/location,place_id';
    $resp = httpGetJson($url);
    $loc = $resp['result']['geometry']['location'] ?? ['lat' => null, 'lng' => null];
    return [
        'place_id' => $resp['result']['place_id'] ?? $placeId,
        'lat' => $loc['lat'],
        'lng' => $loc['lng'],
    ];
}

function httpGetJson(string $url): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $raw = curl_exec($ch);
    if ($raw === false) {
        curl_close($ch);
        return [];
    }
    curl_close($ch);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}


// --- Zone matrix helpers ---
function lookupZoneMatrixPrice(array $data): ?array {
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data';
    $zonesPath = $dir . DIRECTORY_SEPARATOR . 'zones.json';
    $matrixPath = $dir . DIRECTORY_SEPARATOR . 'zone_matrix.json';
    $crossPath = $dir . DIRECTORY_SEPARATOR . 'zone_matrix_cross.json';
    if (!is_file($zonesPath) || !is_file($matrixPath)) { return null; }
    $zones = json_decode(file_get_contents($zonesPath), true);
    $matrix = json_decode(file_get_contents($matrixPath), true);
    $cross = is_file($crossPath) ? (json_decode(file_get_contents($crossPath), true) ?: []) : [];
    if (!is_array($zones) || !isset($zones['regions']) || !is_array($matrix)) { return null; }
    $pickupName = (string)($data['pickup']['name'] ?? '');
    $dropName = (string)($data['dropoff']['name'] ?? '');
    if ($pickupName === '' || $dropName === '') { return null; }
    foreach ($zones['regions'] as $regionName => $regionData) {
        $zoneList = $regionData['zones'] ?? [];
        $aliases = $regionData['aliases'] ?? [];
        $fromZone = matchZoneName($pickupName, $zoneList, $aliases);
        $toZone = matchZoneName($dropName, $zoneList, $aliases);
        if ($fromZone && $toZone) {
            if (isset($matrix[$regionName][$fromZone][$toZone]) && is_numeric($matrix[$regionName][$fromZone][$toZone])) {
                return ['price'=>(float)$matrix[$regionName][$fromZone][$toZone], 'from_zone'=>$fromZone, 'to_zone'=>$toZone];
            }
            if (isset($matrix[$regionName][$toZone][$fromZone]) && is_numeric($matrix[$regionName][$toZone][$fromZone])) {
                return ['price'=>(float)$matrix[$regionName][$toZone][$fromZone], 'from_zone'=>$toZone, 'to_zone'=>$fromZone];
            }
        }
    }
    // Cross-region lookup (A regions vs B regions)
    foreach ($zones['regions'] as $ra => $da) {
        $fromZone = matchZoneName($pickupName, $da['zones'] ?? []);
        if (!$fromZone) continue;
        foreach ($zones['regions'] as $rb => $db) {
            $toZone = matchZoneName($dropName, $db['zones'] ?? []);
            if (!$toZone) continue;
            if (isset($cross[$ra][$rb][$fromZone][$toZone]) && is_numeric($cross[$ra][$rb][$fromZone][$toZone])) {
                return ['price'=>(float)$cross[$ra][$rb][$fromZone][$toZone], 'from_zone'=>$fromZone, 'to_zone'=>$toZone];
            }
            if (isset($cross[$rb][$ra][$toZone][$fromZone]) && is_numeric($cross[$rb][$ra][$toZone][$fromZone])) {
                return ['price'=>(float)$cross[$rb][$ra][$toZone][$fromZone], 'from_zone'=>$toZone, 'to_zone'=>$fromZone];
            }
        }
    }
    return null;
}

function matchZoneName(string $placeName, array $zones, array $aliases = []): ?string {
    $hay = mb_strtoupper($placeName, 'UTF-8');
    foreach ($zones as $z) {
        $needle = mb_strtoupper((string)$z, 'UTF-8');
        if (mb_strpos($hay, $needle) !== false) { return (string)$z; }
        // alias list
        if (isset($aliases[$z]) && is_array($aliases[$z])) {
            foreach ($aliases[$z] as $al) {
                $a = mb_strtoupper((string)$al, 'UTF-8');
                if ($a !== '' && mb_strpos($hay, $a) !== false) { return (string)$z; }
            }
        }
    }
    return null;
}


