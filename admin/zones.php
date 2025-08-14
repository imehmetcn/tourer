<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_admin();

$zonesFile = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'zones.json';
$zones = read_json($zonesFile, [
    'regions' => [
        'ANTALYA' => [ 'keywords' => ['Antalya','AYT'], 'zones' => ['AYT','MURATPAŞA/  KONYAALTI / KEPEZ','LARA','KUNDU','BELEK','ÇOLAKLI','SİDE'], 'aliases' => [ 'MURATPAŞA/  KONYAALTI / KEPEZ' => ['MURATPAŞA','KONYAALTI','KEPEZ'] ] ],
        'MUĞLA' => [ 'keywords' => ['Muğla','Bodrum','Dalaman','DLM'], 'zones' => [], 'aliases' => [] ],
        'AYDIN' => [ 'keywords' => ['Aydın','Kuşadası','Didim'], 'zones' => [], 'aliases' => [] ],
        'İZMİR' => [ 'keywords' => ['İzmir','ADB'], 'zones' => [], 'aliases' => [] ]
    ]
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $raw = trim((string)($_POST['zones'] ?? ''));
    $list = json_decode($raw, true);
    if (is_array($list) && isset($list['zones']) && is_array($list['zones'])) {
        write_json($zonesFile, $list);
        log_admin_action('zones_save');
        header('Location: /mytransfers/admin/zones.php'); exit;
    }
}

ob_start();
?>
<div class="admin-card">
  <h3>Zones</h3>
  <form method="post">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
    <p class="admin-muted">JSON format. Örnek:
    {"regions":{"ANTALYA":{"keywords":["Antalya","AYT"],"zones":["AYT","LARA"]},"MUĞLA":{"keywords":["Muğla"],"zones":[]}}}
    </p>
    <textarea class="admin-textarea" name="zones"><?php echo htmlspecialchars(json_encode($zones, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?></textarea>
    <div style="margin-top:12px"><button class="admin-btn" type="submit">Save</button></div>
  </form>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


