<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$path = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'pricing.json';
$curr = is_file($path) ? file_get_contents($path) : "";

ob_start();
?>
<div class="admin-card">
    <h3>Pricing config (JSON)</h3>
    <form method="post" action="save_pricing.php">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <textarea class="admin-textarea" name="pricing"><?php echo htmlspecialchars($curr, ENT_QUOTES); ?></textarea>
        <div style="margin-top:16px"><button class="admin-btn" type="submit">Save</button></div>
    </form>
    <p class="admin-muted">Örnek:
    <pre>{
  "base_per_km": 1.2,
  "vehicle_multipliers": {"Sedan": 1.0, "Minivan": 1.4},
  "region_multipliers": {"default": 1.0}
}</pre></p>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


