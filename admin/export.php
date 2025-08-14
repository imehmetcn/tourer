<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$type = strtolower(trim((string)($_GET['type'] ?? '')));
$filename = 'export.csv';
$rows = [];

switch ($type) {
    case 'reservations':
        $file = $STORAGE . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
        $data = is_file($file) ? json_decode(file_get_contents($file), true) : [];
        if (!is_array($data)) { $data = []; }
        $filename = 'reservations.csv';
        $rows[] = ['booking_id','status','pickup','dropoff','passengers','amount','created_at'];
        foreach ($data as $r) {
            $rows[] = [
                (string)($r['booking_id'] ?? ''),
                (string)($r['status'] ?? ''),
                (string)($r['pickup']['name'] ?? ''),
                (string)($r['dropoff']['name'] ?? ''),
                (string)($r['passengers'] ?? ''),
                (string)($r['amount']['amount'] ?? ''),
                (string)($r['created_at'] ?? ''),
            ];
        }
        break;
    case 'coupons':
        $file = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'coupons.json';
        $data = is_file($file) ? json_decode(file_get_contents($file), true) : [];
        if (!is_array($data)) { $data = []; }
        $filename = 'coupons.csv';
        $rows[] = ['code','discount_type','discount_value','valid_from','valid_to','min_amount'];
        foreach ($data as $c) {
            $rows[] = [
                (string)($c['code'] ?? ''),
                (string)($c['discount_type'] ?? ''),
                (string)($c['discount_value'] ?? ''),
                (string)($c['valid_from'] ?? ''),
                (string)($c['valid_to'] ?? ''),
                (string)($c['min_amount'] ?? ''),
            ];
        }
        break;
    case 'pricing':
        $file = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'pricing.json';
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename=pricing.json');
        echo is_file($file) ? file_get_contents($file) : json_encode(['base_per_km'=>1.2,'vehicle_multipliers'=>['Sedan'=>1,'Minivan'=>1.35],'region_multipliers'=>['default'=>1]], JSON_PRETTY_PRINT);
        exit;
    case 'destinations':
        $file = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'destinations.json';
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename=destinations.json');
        echo is_file($file) ? file_get_contents($file) : json_encode([], JSON_PRETTY_PRINT);
        exit;
    default:
        http_response_code(400);
        echo 'Unknown type';
        exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$filename);
$out = fopen('php://output', 'w');
foreach ($rows as $row) { fputcsv($out, $row); }
fclose($out);
exit;


