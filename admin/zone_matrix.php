<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_admin();

$zonesFile = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'zones.json';
$zonesData = read_json($zonesFile, []);
// Backward compatibility: if legacy {"zones": [...]} exists
if (isset($zonesData['zones']) && is_array($zonesData['zones']) && !isset($zonesData['regions'])) {
    $zonesData = ['regions' => ['DEFAULT' => ['keywords' => [], 'zones' => $zonesData['zones']]]];
}
$regions = $zonesData['regions'] ?? [];
$currentRegion = $_GET['region'] ?? (array_key_first($regions) ?: '');
$zones = $currentRegion && isset($regions[$currentRegion]) ? ($regions[$currentRegion]['zones'] ?? []) : [];

$matrixFile = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'zone_matrix.json';
$matrix = read_json($matrixFile, []);
if (!is_array($matrix)) { $matrix = []; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    rate_limit('zone_matrix_save', 60, 300);
    // Expect fields like price[A][B]
    $incoming = $_POST['price'] ?? [];
    $currentRegion = $_POST['region'] ?? $currentRegion;
    $out = [];
    foreach ($zones as $row) {
        foreach ($zones as $col) {
            $val = $incoming[$row][$col] ?? '';
            if ($val === '') { continue; }
            $num = (string)$val;
            $num = str_replace(['.', ' '], '', $num);
            $num = str_replace(',', '.', $num);
            if (!is_numeric($num)) { continue; }
            if (!isset($out[$row])) { $out[$row] = []; }
            $out[$row][$col] = (float)$num;
        }
    }
    $full = read_json($matrixFile, []);
    if (!is_array($full)) { $full = []; }
    if (!isset($full[$currentRegion])) { $full[$currentRegion] = []; }
    $full[$currentRegion] = $out;
    write_json($matrixFile, $full);
    log_admin_action('zone_matrix_save');
    header('Location: /mytransfers/admin/zone_matrix.php'); exit;
}

ob_start();
?>
<div class="admin-card">
  <h3>Zone Matrix</h3>
  <form method="get" class="admin-form-row" style="margin:8px 0">
    <select class="admin-select" name="region">
      <?php foreach (array_keys($regions) as $r): ?>
        <option value="<?php echo htmlspecialchars($r, ENT_QUOTES); ?>" <?php echo $r===$currentRegion?'selected':''; ?>><?php echo htmlspecialchars($r, ENT_QUOTES); ?></option>
      <?php endforeach; ?>
    </select>
    <button class="admin-btn" type="submit">Load</button>
  </form>
  <?php if (!$zones): ?>
    <div class="admin-muted">Seçili bölgede (<?php echo htmlspecialchars($currentRegion, ENT_QUOTES); ?>) zone yok. Zones sayfasından ekleyin.</div>
  <?php else: ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
    <input type="hidden" name="region" value="<?php echo htmlspecialchars($currentRegion, ENT_QUOTES); ?>" />
    <div class="matrix-wrap">
      <table class="matrix-table">
        <thead>
          <tr>
            <th class="sticky-col zone-name"></th>
            <?php foreach ($zones as $z): ?><th><?php echo htmlspecialchars($z, ENT_QUOTES); ?></th><?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($zones as $r): ?>
          <tr>
            <th class="sticky-col zone-name"><?php echo htmlspecialchars($r, ENT_QUOTES); ?></th>
            <?php foreach ($zones as $c): $v = ($matrix[$currentRegion][$r][$c] ?? ''); ?>
              <td><input class="matrix-input admin-input" name="price[<?php echo htmlspecialchars($r, ENT_QUOTES); ?>][<?php echo htmlspecialchars($c, ENT_QUOTES); ?>]" value="<?php echo htmlspecialchars((string)$v, ENT_QUOTES); ?>" placeholder="" /></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div style="margin-top:12px"><button class="admin-btn" type="submit">Save Matrix</button></div>
  </form>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


