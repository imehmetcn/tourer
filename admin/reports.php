<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$file = $STORAGE . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
$items = is_file($file) ? json_decode(file_get_contents($file), true) : [];
if (!is_array($items)) { $items = []; }

$couponUsage = [];
$topRoutes = [];
$statusCount = [];
foreach ($items as $r) {
    $statusCount[$r['status'] ?? ''] = ($statusCount[$r['status'] ?? ''] ?? 0) + 1;
    $code = strtoupper((string)($r['coupon_code'] ?? ''));
    if ($code !== '') { $couponUsage[$code] = ($couponUsage[$code] ?? 0) + 1; }
    $p = trim((string)($r['pickup']['name'] ?? ''));
    $d = trim((string)($r['dropoff']['name'] ?? ''));
    if ($p && $d) { $key = $p.' → '.$d; $topRoutes[$key] = ($topRoutes[$key] ?? 0) + 1; }
}
arsort($couponUsage); $couponUsage = array_slice($couponUsage, 0, 20, true);
arsort($topRoutes); $topRoutes = array_slice($topRoutes, 0, 20, true);

ob_start();
?>
<div class="admin-card">
  <h3>Coupon usage (top)</h3>
  <table class="admin-table">
    <thead><tr><th>Code</th><th>Count</th></tr></thead>
    <tbody>
    <?php foreach ($couponUsage as $c=>$n): ?>
      <tr><td><?php echo htmlspecialchars($c, ENT_QUOTES); ?></td><td><?php echo (int)$n; ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <a class="admin-btn" href="/mytransfers/admin/export.php?type=coupons">Export coupons CSV</a>
</div>

<div class="admin-card">
  <h3>Top routes</h3>
  <table class="admin-table">
    <thead><tr><th>Route</th><th>Count</th></tr></thead>
    <tbody>
    <?php foreach ($topRoutes as $k=>$n): ?>
      <tr><td><?php echo htmlspecialchars($k, ENT_QUOTES); ?></td><td><?php echo (int)$n; ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="admin-card">
  <h3>Status summary</h3>
  <table class="admin-table">
    <thead><tr><th>Status</th><th>Count</th></tr></thead>
    <tbody>
    <?php foreach ($statusCount as $k=>$n): ?>
      <tr><td><?php echo htmlspecialchars($k, ENT_QUOTES); ?></td><td><?php echo (int)$n; ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


