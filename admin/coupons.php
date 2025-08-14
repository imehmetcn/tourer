<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$path = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'coupons.json';
$curr = is_file($path) ? file_get_contents($path) : "[]";

ob_start();
?>
<div class="admin-card">
    <h3>Coupons (JSON array)</h3>
    <div style="margin-bottom:8px">
        <a class="admin-btn" href="/mytransfers/admin/export.php?type=coupons">Export CSV</a>
    </div>
    <form method="post" action="save_coupons.php">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <textarea class="admin-textarea" name="coupons"><?php echo htmlspecialchars($curr, ENT_QUOTES); ?></textarea>
        <div style="margin-top:16px"><button class="admin-btn" type="submit">Save</button></div>
    </form>
    <p class="admin-muted">Örnek:
    <pre>[{
  "code": "WELCOME10",
  "discount_type": "percent",
  "discount_value": 10,
  "valid_from": "2025-01-01",
  "valid_to": "2025-12-31",
  "min_amount": 20
}]</pre></p>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


