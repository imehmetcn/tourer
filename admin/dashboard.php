<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$reservationsFile = $STORAGE . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
$reservations = is_file($reservationsFile) ? json_decode(file_get_contents($reservationsFile), true) : [];
if (!is_array($reservations)) { $reservations = []; }

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$today = date('Y-m-d');
$fromDate = $from !== '' ? $from : date('Y-m-d', strtotime('-6 days'));
$toDate = $to !== '' ? $to : $today;
$fromTs = strtotime($fromDate . ' 00:00:00');
$toTs = strtotime($toDate . ' 23:59:59');

$total = count($reservations);
$last24 = 0; $last7 = 0; $last30 = 0; $now = time();
$byStatus = [];
$byDay = [];
$revenueByDay = [];
$totalRevenue = 0.0;
$topPickup = [];
$topDropoff = [];
foreach ($reservations as $r) {
    $st = (string)($r['status'] ?? '');
    $byStatus[$st] = ($byStatus[$st] ?? 0) + 1;
    $day = substr((string)($r['created_at'] ?? ''), 0, 10);
    $ts = strtotime((string)($r['created_at'] ?? '')) ?: 0;
    if ($ts && $ts >= $fromTs && $ts <= $toTs) {
        if ($day) { $byDay[$day] = ($byDay[$day] ?? 0) + 1; }
        // revenue considers paid only
        if ($st === 'paid') {
            $amt = 0.0;
            if (isset($r['amount']['amount'])) { $amt = (float)$r['amount']['amount']; }
            elseif (isset($r['amount'])) { $amt = (float)$r['amount']; }
            $totalRevenue += $amt;
            $revenueByDay[$day] = ($revenueByDay[$day] ?? 0) + $amt;
        }
    }
    $ts = strtotime((string)($r['created_at'] ?? '')) ?: 0;
    if ($ts && ($now - $ts) <= 86400) { $last24++; }
    if ($ts && ($now - $ts) <= 7*86400) { $last7++; }
    if ($ts && ($now - $ts) <= 30*86400) { $last30++; }
    $p = trim((string)($r['pickup']['name'] ?? ''));
    $d = trim((string)($r['dropoff']['name'] ?? ''));
    if ($p) { $topPickup[$p] = ($topPickup[$p] ?? 0) + 1; }
    if ($d) { $topDropoff[$d] = ($topDropoff[$d] ?? 0) + 1; }
}
$days = [];
$revenueDays = [];
for ($t = strtotime($fromDate); $t <= strtotime($toDate); $t += 86400) {
    $k = date('Y-m-d', $t);
    $days[$k] = $byDay[$k] ?? 0;
    $revenueDays[$k] = round(($revenueByDay[$k] ?? 0), 2);
}
$countriesCount = 0; $destinationsCount = 0; $couponsCount = 0;
$countriesPath = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'countries.json';
if (is_file($countriesPath)) { $arr = json_decode(file_get_contents($countriesPath), true); $countriesCount = is_array($arr) ? count($arr) : 0; }
$destPath = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'destinations.json';
if (is_file($destPath)) { $arr = json_decode(file_get_contents($destPath), true); $destinationsCount = is_array($arr) ? count($arr) : 0; }
$couponsPath = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'coupons.json';
if (is_file($couponsPath)) { $arr = json_decode(file_get_contents($couponsPath), true); $couponsCount = is_array($arr) ? count($arr) : 0; }
$proxyLog = $STORAGE . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'proxy.log';
$logTail = [];
if (is_file($proxyLog)) { $lines = file($proxyLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []; $logTail = array_slice($lines, -20); }
$cfgPath = $STORAGE . DIRECTORY_SEPARATOR . 'config.json';
$googleKey = '';
if (is_file($cfgPath)) { $cfg = json_decode(file_get_contents($cfgPath), true); $googleKey = is_array($cfg) ? ($cfg['google_api_key'] ?? '') : ''; }

ob_start();
?>
<div class="admin-cards">
    <div class="admin-card"><h3><?php echo $total; ?></h3><div class="admin-muted">Total Reservations</div></div>
    <div class="admin-card"><h3><?php echo $last24; ?></h3><div class="admin-muted">Last 24h</div></div>
    <div class="admin-card"><h3><?php echo $last7; ?></h3><div class="admin-muted">Last 7 days</div></div>
    <div class="admin-card"><h3><?php echo $last30; ?></h3><div class="admin-muted">Last 30 days</div></div>
</div>

<div class="admin-card">
    <h3>Reservations · last 7 days</h3>
    <form method="get" class="admin-form-row" style="margin:8px 0">
        <input class="admin-input" type="date" name="from" value="<?php echo htmlspecialchars($fromDate, ENT_QUOTES); ?>" />
        <input class="admin-input" type="date" name="to" value="<?php echo htmlspecialchars($toDate, ENT_QUOTES); ?>" />
        <button class="admin-btn" type="submit">Apply</button>
        <?php
            $f7 = date('Y-m-d', strtotime('-6 days'));
            $f30 = date('Y-m-d', strtotime('-29 days'));
            $qs7 = http_build_query(['from'=>$f7,'to'=>$today]);
            $qs30 = http_build_query(['from'=>$f30,'to'=>$today]);
        ?>
        <a class="admin-btn" href="?<?php echo $qs7; ?>">7d</a>
        <a class="admin-btn" href="?<?php echo $qs30; ?>">30d</a>
    </form>
    <div class="chart-wrap-md"><canvas id="chartDays"></canvas></div>
</div>

<div class="admin-card">
    <h3>Revenue (paid) · selected range · Total: <?php echo number_format($totalRevenue,2); ?> EUR</h3>
    <div class="chart-wrap-md"><canvas id="chartRevenue"></canvas></div>
</div>

<div class="admin-card">
    <h3>Status distribution</h3>
    <div class="chart-wrap-sm"><canvas id="chartStatus"></canvas></div>
</div>

<div class="admin-card">
    <h3>Top 5 Pickup / Dropoff</h3>
    <div style="display:flex; gap:16px">
        <div style="flex:1">
            <div class="chart-wrap-sm"><canvas id="chartPickup"></canvas></div>
        </div>
        <div style="flex:1">
            <div class="chart-wrap-sm"><canvas id="chartDropoff"></canvas></div>
        </div>
    </div>
</div>



<?php if ($logTail): ?>
<div class="admin-card">
    <h3>Proxy Log (tail)</h3>
    <pre class="admin-pre"><?php echo htmlspecialchars(implode("\n", $logTail), ENT_QUOTES); ?></pre>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const daysLabels = <?php echo json_encode(array_keys($days)); ?>;
const daysData = <?php echo json_encode(array_values($days)); ?>;
new Chart(document.getElementById('chartDays'), { type: 'bar', data: { labels: daysLabels, datasets: [{ label: 'Reservations', data: daysData, backgroundColor: '#09AFEE' }] }, options: { responsive: true, scales: { y: { beginAtZero: true }}}});

const revenueLabels = <?php echo json_encode(array_keys($revenueDays)); ?>;
const revenueData = <?php echo json_encode(array_values($revenueDays)); ?>;
new Chart(document.getElementById('chartRevenue'), { type: 'line', data: { labels: revenueLabels, datasets: [{ label: 'Revenue (EUR)', data: revenueData, borderColor: '#4CAF50', backgroundColor: 'rgba(76,175,80,.15)', tension:.2 }] }, options: { responsive:true, scales:{ y:{ beginAtZero:true }}}});

const statusLabels = <?php echo json_encode(array_keys($byStatus)); ?>;
const statusData = <?php echo json_encode(array_values($byStatus)); ?>;
new Chart(document.getElementById('chartStatus'), { type: 'doughnut', data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: ['#09AFEE','#4CAF50','#FFC107','#E91E63','#8E44AD'] }] }, options: { responsive: true }});

function topEntries(obj, n){
  const entries = Object.entries(obj).sort((a,b)=>b[1]-a[1]).slice(0,n);
  return { labels: entries.map(e=>e[0]), data: entries.map(e=>e[1]) };
}
const pickupData = topEntries(<?php echo json_encode($topPickup); ?>, 5);
const dropoffData = topEntries(<?php echo json_encode($topDropoff); ?>, 5);
new Chart(document.getElementById('chartPickup'), { type:'bar', data:{ labels: pickupData.labels, datasets:[{label:'Pickup', data: pickupData.data, backgroundColor:'#0898cf'}] }, options:{ indexAxis:'y', responsive:true, scales:{ x:{beginAtZero:true}} }});
new Chart(document.getElementById('chartDropoff'), { type:'bar', data:{ labels: dropoffData.labels, datasets:[{label:'Dropoff', data: dropoffData.data, backgroundColor:'#4CAF50'}] }, options:{ indexAxis:'y', responsive:true, scales:{ x:{beginAtZero:true}} }});


</script>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);