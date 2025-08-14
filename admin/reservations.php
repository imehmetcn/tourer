<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

// Try DB first
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$q = trim((string)($_GET['q'] ?? ''));
$status = trim((string)($_GET['status'] ?? ''));
$method = trim((string)($_GET['method'] ?? ''));
$fz = trim((string)($_GET['from_zone'] ?? ''));
$tz = trim((string)($_GET['to_zone'] ?? ''));
$amountMin = isset($_GET['amount_min']) && $_GET['amount_min'] !== '' ? (float)$_GET['amount_min'] : null;
$amountMax = isset($_GET['amount_max']) && $_GET['amount_max'] !== '' ? (float)$_GET['amount_max'] : null;
$paxMin = isset($_GET['pax_min']) && $_GET['pax_min'] !== '' ? (int)$_GET['pax_min'] : null;
$paxMax = isset($_GET['pax_max']) && $_GET['pax_max'] !== '' ? (int)$_GET['pax_max'] : null;
$page = max(1, (int)($_GET['page'] ?? 1));
$pageSize = 20;
$sort = trim((string)($_GET['sort'] ?? 'id'));
$dir = strtolower((string)($_GET['dir'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';
$allowedSort = [
    'id' => 'id',
    'booking_id' => 'booking_id',
    'status' => 'status',
    'passengers' => 'passengers',
    'amount' => 'amount',
    'created_at' => 'created_at'
];
$orderBy = $allowedSort[$sort] ?? 'id';

$items = [];
$dbOk = false;
try {
    require_once __DIR__.'/../api/db.php';
    $pdo = get_pdo();
    if ($pdo) {
        $sql = "SELECT booking_id,status,passengers,amount,pricing_method,from_zone,to_zone,JSON_UNQUOTE(JSON_EXTRACT(pickup,'$.name')) AS pickup_name,JSON_UNQUOTE(JSON_EXTRACT(dropoff,'$.name')) AS dropoff_name,created_at FROM reservations WHERE 1=1";
        $params = [];
        if ($q !== '') { $sql .= " AND booking_id LIKE ?"; $params[] = "%$q%"; }
        if ($status !== '') { $sql .= " AND status = ?"; $params[] = $status; }
        if ($from !== '') { $sql .= " AND created_at >= ?"; $params[] = $from.' 00:00:00'; }
        if ($to !== '') { $sql .= " AND created_at <= ?"; $params[] = $to.' 23:59:59'; }
        if ($amountMin !== null) { $sql .= " AND amount >= ?"; $params[] = $amountMin; }
        if ($amountMax !== null) { $sql .= " AND amount <= ?"; $params[] = $amountMax; }
        if ($paxMin !== null) { $sql .= " AND passengers >= ?"; $params[] = $paxMin; }
        if ($paxMax !== null) { $sql .= " AND passengers <= ?"; $params[] = $paxMax; }
        if ($method !== '') { $sql .= " AND pricing_method = ?"; $params[] = $method; }
        if ($fz !== '') { $sql .= " AND from_zone = ?"; $params[] = $fz; }
        if ($tz !== '') { $sql .= " AND to_zone = ?"; $params[] = $tz; }
        $sql .= " ORDER BY $orderBy $dir LIMIT " . ($pageSize + 1) . " OFFSET " . (($page-1)*$pageSize);
        $stmt = $pdo->prepare($sql); $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $hasNext = count($rows) > $pageSize;
        $items = array_slice($rows, 0, $pageSize);
        $dbOk = true;
    }
} catch (Throwable $e) { $dbOk = false; }

// Fallback to JSON
if (!$dbOk) {
    $file = $STORAGE . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
    $all = is_file($file) ? json_decode(file_get_contents($file), true) : [];
    if (!is_array($all)) { $all = []; }
    $items = array_filter($all, function($r) use ($q,$status,$from,$to,$amountMin,$amountMax,$paxMin,$paxMax,$method,$fz,$tz){
        if ($q !== '' && stripos((string)($r['booking_id'] ?? ''), $q) === false) return false;
        if ($status !== '' && (string)($r['status'] ?? '') !== $status) return false;
        $c = (string)($r['created_at'] ?? '');
        if ($from !== '' && strcmp(substr($c,0,10), $from) < 0) return false;
        if ($to !== '' && strcmp(substr($c,0,10), $to) > 0) return false;
        if ($method !== '' && (string)($r['pricing_method'] ?? '') !== $method) return false;
        if ($fz !== '' && (string)($r['from_zone'] ?? '') !== $fz) return false;
        if ($tz !== '' && (string)($r['to_zone'] ?? '') !== $tz) return false;
        $amt = isset($r['amount']['amount']) ? (float)$r['amount']['amount'] : (float)($r['amount'] ?? 0);
        if ($amountMin !== null && $amt < $amountMin) return false;
        if ($amountMax !== null && $amt > $amountMax) return false;
        $pax = (int)($r['passengers'] ?? 0);
        if ($paxMin !== null && $pax < $paxMin) return false;
        if ($paxMax !== null && $pax > $paxMax) return false;
        return true;
    });
    usort($items, function($a,$b) use($orderBy,$dir){
        $va = $a[$orderBy] ?? ($orderBy==='amount' ? ($a['amount']['amount'] ?? 0) : '');
        $vb = $b[$orderBy] ?? ($orderBy==='amount' ? ($b['amount']['amount'] ?? 0) : '');
        if ($va == $vb) return 0;
        $cmp = ($va < $vb) ? -1 : 1;
        return $dir==='ASC' ? $cmp : -$cmp;
    });
    $hasNext = count($items) > $page*$pageSize;
    $items = array_slice($items, ($page-1)*$pageSize, $pageSize);
}

ob_start();
?>
<div class="admin-card">
    <h3>Reservations</h3>
    <form method="get" class="admin-form-row" style="margin-bottom:8px">
        <input class="admin-input" type="text" name="q" placeholder="Booking ID" value="<?php echo htmlspecialchars($q, ENT_QUOTES); ?>" />
        <select class="admin-select" name="status">
            <option value="">All statuses</option>
            <?php foreach (['pending_payment','paid','canceled','cancelled'] as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $status===$s?'selected':''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
        </select>
        <select class="admin-select" name="method">
            <option value="">Any method</option>
            <?php foreach (['matrix','distance'] as $m): ?>
                <option value="<?php echo $m; ?>" <?php echo $method===$m?'selected':''; ?>><?php echo $m; ?></option>
            <?php endforeach; ?>
        </select>
        <input class="admin-input" type="text" name="from_zone" placeholder="From zone" value="<?php echo htmlspecialchars($fz, ENT_QUOTES); ?>" />
        <input class="admin-input" type="text" name="to_zone" placeholder="To zone" value="<?php echo htmlspecialchars($tz, ENT_QUOTES); ?>" />
        <input class="admin-input" type="date" name="from" value="<?php echo htmlspecialchars($from, ENT_QUOTES); ?>" />
        <input class="admin-input" type="date" name="to" value="<?php echo htmlspecialchars($to, ENT_QUOTES); ?>" />
        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES); ?>" />
        <input type="hidden" name="dir" value="<?php echo htmlspecialchars(strtolower($dir), ENT_QUOTES); ?>" />
        <button class="admin-btn" type="submit">Filter</button>
        <a class="admin-btn" href="/mytransfers/admin/export.php?type=reservations">Export CSV</a>
    </form>
    <form method="post" action="/mytransfers/admin/reservation_action.php" onsubmit="return confirm('Are you sure?')">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
    <div class="admin-form-row" style="margin-bottom:8px">
        <select class="admin-select" name="action">
            <option value="mark_paid">Mark paid</option>
            <option value="cancel">Cancel</option>
            <option value="resend_mail">Resend email</option>
        </select>
        <button class="admin-btn" type="submit">Apply to selected</button>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th><input type="checkbox" onclick="document.querySelectorAll('.sel').forEach(c=>c.checked=this.checked)" /></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'booking_id','dir'=>($sort==='booking_id'&&$dir==='ASC')?'desc':'asc','page'=>1])); ?>">Booking ID</a></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'status','dir'=>($sort==='status'&&$dir==='ASC')?'desc':'asc','page'=>1])); ?>">Status</a></th>
                <th>Pickup</th>
                <th>Dropoff</th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'passengers','dir'=>($sort==='passengers'&&$dir==='ASC')?'desc':'asc','page'=>1])); ?>">Passengers</a></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'amount','dir'=>($sort==='amount'&&$dir==='ASC')?'desc':'asc','page'=>1])); ?>">Amount</a></th>
                <th><a href="?<?php echo http_build_query(array_merge($_GET,['sort'=>'created_at','dir'=>($sort==='created_at'&&$dir==='ASC')?'desc':'asc','page'=>1])); ?>">Created</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($dbOk): foreach ($items as $r): ?>
            <tr>
                <td><input class="sel" type="checkbox" name="booking_ids[]" value="<?php echo htmlspecialchars($r['booking_id'] ?? '', ENT_QUOTES); ?>" /></td>
                <td><?php echo htmlspecialchars($r['booking_id'] ?? '', ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars($r['status'] ?? '', ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars($r['pickup_name'] ?? '', ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars($r['dropoff_name'] ?? '', ENT_QUOTES); ?></td>
                <td><?php echo (int)($r['passengers'] ?? 0); ?></td>
                <td><?php echo htmlspecialchars(number_format((float)($r['amount'] ?? 0),2), ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES); ?></td>
                <td>
                    <a class="admin-btn" href="/mytransfers/admin/reservation_action.php?act=mark_paid&booking_id=<?php echo urlencode($r['booking_id'] ?? ''); ?>&csrf=<?php echo urlencode(csrf_token()); ?>">Paid</a>
                    <a class="admin-btn" href="/mytransfers/admin/reservation_action.php?act=resend_mail&booking_id=<?php echo urlencode($r['booking_id'] ?? ''); ?>&csrf=<?php echo urlencode(csrf_token()); ?>">Resend</a>
                    <a class="admin-btn" href="/mytransfers/admin/reservation_action.php?act=open_voucher&booking_id=<?php echo urlencode($r['booking_id'] ?? ''); ?>&csrf=<?php echo urlencode(csrf_token()); ?>">Voucher</a>
                </td>
            </tr>
            <?php endforeach; else: foreach ($items as $r): ?>
            <tr>
                <td><input class="sel" type="checkbox" name="booking_ids[]" value="<?php echo htmlspecialchars($r['booking_id'] ?? '', ENT_QUOTES); ?>" /></td>
                <td><?php echo htmlspecialchars($r['booking_id'] ?? '', ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars($r['status'] ?? '', ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars(($r['pickup']['name'] ?? ''), ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars(($r['dropoff']['name'] ?? ''), ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars((string)($r['passengers'] ?? ''), ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars(number_format((float)($r['amount']['amount'] ?? 0),2), ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES); ?></td>
                <td>
                    <a class="admin-btn" href="/mytransfers/admin/reservation_action.php?act=mark_paid&booking_id=<?php echo urlencode($r['booking_id'] ?? ''); ?>&csrf=<?php echo urlencode(csrf_token()); ?>">Paid</a>
                    <a class="admin-btn" href="/mytransfers/admin/reservation_action.php?act=resend_mail&booking_id=<?php echo urlencode($r['booking_id'] ?? ''); ?>&csrf=<?php echo urlencode(csrf_token()); ?>">Resend</a>
                    <a class="admin-btn" href="/mytransfers/admin/reservation_action.php?act=open_voucher&booking_id=<?php echo urlencode($r['booking_id'] ?? ''); ?>&csrf=<?php echo urlencode(csrf_token()); ?>">Voucher</a>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
    </form>
    <div class="admin-form-row" style="justify-content: flex-end; margin-top:8px">
        <?php $qsPrev = $_GET; $qsPrev['page'] = max(1,$page-1); $qsNext = $_GET; $qsNext['page'] = $page+1; ?>
        <a class="admin-btn" href="?<?php echo http_build_query($qsPrev); ?>" <?php echo $page<=1?'style="pointer-events:none;opacity:.6"':''; ?>>Prev</a>
        <a class="admin-btn" href="?<?php echo http_build_query($qsNext); ?>" <?php echo empty($hasNext)?'style="pointer-events:none;opacity:.6"':''; ?>>Next</a>
    </div>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


