<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_admin();
$normalize = function(string $s): string {
  $s = trim(mb_strtoupper($s, 'UTF-8'));
  $map = [
    'Ğ'=>'G','ğ'=>'g','İ'=>'I','ı'=>'I','Ş'=>'S','ş'=>'s','Ç'=>'C','ç'=>'c','Ö'=>'O','ö'=>'o','Ü'=>'U','ü'=>'u'
  ];
  $s = strtr($s, $map);
  $s = preg_replace('/[^A-Z0-9]/u', '', $s) ?: '';
  return $s;
};

$zonesFile = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'zones.json';
$zonesData = read_json($zonesFile, ['regions'=>[]]);
$regions = array_keys($zonesData['regions'] ?? []);
$a = $_GET['a'] ?? ($regions[0] ?? '');
$b = $_GET['b'] ?? ($regions[1] ?? '');
$zonesA = $zonesData['regions'][$a]['zones'] ?? [];
$zonesB = $zonesData['regions'][$b]['zones'] ?? [];

$file = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'zone_matrix_cross.json';
$cross = read_json($file, []);
if (!is_array($cross)) { $cross = []; }

// Build normalized maps for zones (usable by all import methods)
$aMap = [];
$bMap = [];
foreach ($zonesA as $za) { $aMap[$normalize($za)] = $za; }
foreach ($zonesB as $zb) { $bMap[$normalize($zb)] = $zb; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
  rate_limit('zone_matrix_cross_save', 60, 300);
  $a = $_POST['a'] ?? $a; $b = $_POST['b'] ?? $b;
  // Clear current pair matrix
  if (isset($_POST['action']) && $_POST['action'] === 'clear') {
    if (!isset($cross[$a])) { $cross[$a] = []; }
    $cross[$a][$b] = [];
    write_json($file, $cross);
    log_admin_action('zone_matrix_cross_clear', ['a'=>$a,'b'=>$b]);
    header('Location: /mytransfers/admin/zone_matrix_cross.php?a='.urlencode($a).'&b='.urlencode($b)); exit;
  }
  $incoming = $_POST['price'] ?? [];
  $mat = [];
  // 1) Bulk paste support
  $bulk = trim((string)($_POST['bulk'] ?? ''));
  if ($bulk !== '') {
    $lines = preg_split("/(\r\n|\n|\r)/", $bulk);
    $rIdx = 0;
    foreach ($lines as $line) {
      if ($line === '') { $rIdx++; continue; }
      if (!isset($zonesA[$rIdx])) { break; }
      $rowName = $zonesA[$rIdx];
      $cells = preg_split('/\t|;|,\s*(?=\d)/', $line); // split on tab, semicolon, or commas between numbers
      $cIdx = 0;
      foreach ($cells as $cell) {
        if (!isset($zonesB[$cIdx])) { break; }
        $num = trim($cell);
        if ($num === '') { $cIdx++; continue; }
        // Convert TR format like 1.650,00 → 1650.00
        $num = str_replace(['.', ' '], '', $num);
        $num = str_replace(',', '.', $num);
        if (is_numeric($num)) {
          if (!isset($mat[$rowName])) $mat[$rowName] = [];
          $mat[$rowName][$zonesB[$cIdx]] = (float)$num;
        }
        $cIdx++;
      }
      $rIdx++;
    }
  }
  // 2) Individual inputs (override or fill missing)
  foreach ($zonesA as $row) {
    foreach ($zonesB as $col) {
      $val = $incoming[$row][$col] ?? '';
      if ($val === '') continue;
      $num = (string)$val;
      $num = str_replace(['.', ' '], '', $num);
      $num = str_replace(',', '.', $num);
      if (!is_numeric($num)) continue;
      if (!isset($mat[$row])) $mat[$row] = [];
      $mat[$row][$col] = (float)$num;
    }
  }
  // 3) CSV upload (first row headers = B zones, first column = A zones)
  if (!empty($_FILES['csv']['tmp_name']) && is_uploaded_file($_FILES['csv']['tmp_name'])) {
    $tmp = $_FILES['csv']['tmp_name'];
    $raw = file_get_contents($tmp);
    if ($raw !== false) {
      // Detect delimiter by first line
      $firstLine = strtok($raw, "\r\n");
      $counts = [","=>substr_count($firstLine, ','), ";"=>substr_count($firstLine, ';'), "\t"=>substr_count($firstLine, "\t")];
      arsort($counts); $delim = array_key_first($counts) ?: ',';
      $fh = fopen($tmp, 'r');
      if ($fh) {
        $headers = [];
        $rowIndex = 0;
        while (($row = fgetcsv($fh, 0, $delim)) !== false) {
          if ($rowIndex === 0) {
            $headers = $row;
            $rowIndex++;
            continue;
          }
          if (count($row) === 0) { $rowIndex++; continue; }
          $rowNameRaw = (string)($row[0] ?? '');
          $aKey = $normalize($rowNameRaw);
          if ($aKey === '' || !isset($aMap[$aKey])) { $rowIndex++; continue; }
          $rowName = $aMap[$aKey];
          for ($i = 1; $i < count($row); $i++) {
            $colNameRaw = (string)($headers[$i] ?? '');
            $bKey = $normalize($colNameRaw);
            if ($bKey === '' || !isset($bMap[$bKey])) { continue; }
            $colName = $bMap[$bKey];
            $cell = trim((string)$row[$i]);
            if ($cell === '') { continue; }
            $num = str_replace(['.', ' '], '', $cell);
            $num = str_replace(',', '.', $num);
            if (!is_numeric($num)) { continue; }
            if (!isset($mat[$rowName])) $mat[$rowName] = [];
            $mat[$rowName][$colName] = (float)$num;
          }
          $rowIndex++;
        }
        fclose($fh);
      }
    }
  }
  // 4) JSON array import (like the one you pasted). First object headers, following objects rows.
  $jsonRaw = trim((string)($_POST['json'] ?? ''));
  if ($jsonRaw !== '') {
    $arr = json_decode($jsonRaw, true);
    if (is_array($arr) && count($arr) > 1) {
      $headersObj = $arr[0];
      // Build column headers (__2..)__N → zone name
      $colMap = [];
      foreach ($headersObj as $k=>$v) {
        if ($k === '' || $k === '__1') { continue; }
        $bKey = $normalize((string)$v);
        if ($bKey !== '' && isset($bMap[$bKey])) { $colMap[$k] = $bMap[$bKey]; }
      }
      // Rows
      foreach (array_slice($arr,1) as $rowObj) {
        if (!is_array($rowObj)) { continue; }
        $rowNameRaw = (string)($rowObj['__1'] ?? '');
        $aKey = $normalize($rowNameRaw);
        if ($aKey === '' || !isset($aMap[$aKey])) { continue; }
        $rowName = $aMap[$aKey];
        foreach ($colMap as $colKey=>$colName) {
          $cell = isset($rowObj[$colKey]) ? (string)$rowObj[$colKey] : '';
          if ($cell === '') { continue; }
          $num = str_replace(['.', ' '], '', $cell);
          $num = str_replace(',', '.', $num);
          if (!is_numeric($num)) { continue; }
          if (!isset($mat[$rowName])) $mat[$rowName] = [];
          $mat[$rowName][$colName] = (float)$num;
        }
      }
    }
  }
  if (!isset($cross[$a])) $cross[$a] = [];
  $cross[$a][$b] = $mat;
  write_json($file, $cross);
  log_admin_action('zone_matrix_cross_save', ['a'=>$a,'b'=>$b]);
  header('Location: /mytransfers/admin/zone_matrix_cross.php?a='.urlencode($a).'&b='.urlencode($b)); exit;
}

ob_start();
?>
<div class="admin-card">
  <h3>Cross Region Matrix</h3>
  <form method="get" class="admin-form-row" style="margin:8px 0">
    <select class="admin-select" name="a">
      <?php foreach ($regions as $r): ?><option value="<?php echo htmlspecialchars($r, ENT_QUOTES); ?>" <?php echo $r===$a?'selected':''; ?>><?php echo htmlspecialchars($r, ENT_QUOTES); ?></option><?php endforeach; ?>
    </select>
    <select class="admin-select" name="b">
      <?php foreach ($regions as $r): ?><option value="<?php echo htmlspecialchars($r, ENT_QUOTES); ?>" <?php echo $r===$b?'selected':''; ?>><?php echo htmlspecialchars($r, ENT_QUOTES); ?></option><?php endforeach; ?>
    </select>
    <button class="admin-btn" type="submit">Load</button>
  </form>

  <?php if (!$zonesA || !$zonesB): ?>
    <div class="admin-muted">Seçilen bölgelerde zone tanımı yok.</div>
  <?php else: ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
    <input type="hidden" name="a" value="<?php echo htmlspecialchars($a, ENT_QUOTES); ?>" />
    <input type="hidden" name="b" value="<?php echo htmlspecialchars($b, ENT_QUOTES); ?>" />
    
    <div class="admin-toolbar" style="justify-content: flex-end; margin:8px 0">
      <button class="admin-btn admin-btn--danger" type="submit" name="action" value="clear" onclick="return confirm('Bu il çifti için tüm hücreler silinsin mi?');">Clear current matrix</button>
    </div>
    <div class="admin-card" style="margin:8px 0">
      <h4>JSON import (optional)</h4>
      <p class="admin-muted">Yukarıdaki formatta JSON array yapıştırabilirsiniz (ilk obje başlıklar, devamı satırlar).</p>
      <textarea name="json" class="admin-textarea" style="min-height:120px"></textarea>
    </div>
    <div class="matrix-wrap">
      <table class="matrix-table">
        <thead>
          <tr>
            <th class="sticky-col zone-name"><?php echo htmlspecialchars($a, ENT_QUOTES); ?> → <?php echo htmlspecialchars($b, ENT_QUOTES); ?></th>
            <?php foreach ($zonesB as $z): ?><th><?php echo htmlspecialchars($z, ENT_QUOTES); ?></th><?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($zonesA as $r): ?>
          <tr>
            <th class="sticky-col zone-name"><?php echo htmlspecialchars($r, ENT_QUOTES); ?></th>
            <?php foreach ($zonesB as $c): $v = $cross[$a][$b][$r][$c] ?? ''; ?>
              <td><input class="matrix-input admin-input" name="price[<?php echo htmlspecialchars($r, ENT_QUOTES); ?>][<?php echo htmlspecialchars($c, ENT_QUOTES); ?>]" value="<?php echo htmlspecialchars((string)$v, ENT_QUOTES); ?>" /></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div style="margin-top:12px"><button class="admin-btn" type="submit">Save Cross Matrix</button></div>
  </form>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


