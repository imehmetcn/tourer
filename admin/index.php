<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();

$configFile = $STORAGE . DIRECTORY_SEPARATOR . 'config.json';
$config = read_json($configFile, [
    'google_api_key' => getenv('GOOGLE_API_KEY') ?: '',
    'maintenance' => false,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $config['google_api_key'] = trim((string)($_POST['google_api_key'] ?? ''));
    $config['maintenance'] = isset($_POST['maintenance']) && $_POST['maintenance'] === '1';
    write_json($configFile, $config);
    if ($config['google_api_key']) {
        $apiCfg = __DIR__ . '/../api/config.php';
        $tpl = "<?php\n// Configuration for API integration\n\n// Prefer environment variable if set (Windows: setx GOOGLE_API_KEY \"your-key\")\n$".
               "envKey = getenv('GOOGLE_API_KEY');\n".
               "define('GOOGLE_API_KEY', $envKey !== false ? $envKey : '".addslashes($config['google_api_key'])."');\n";
        file_put_contents($apiCfg, $tpl);
    }
    header('Location: /mytransfers/admin');
    exit;
}

ob_start();
?>
<div class="admin-card">
    <h3>Settings</h3>
    <form method="post">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <label>Google API Key</label>
        <input class="admin-input" type="text" name="google_api_key" value="<?php echo htmlspecialchars($config['google_api_key'], ENT_QUOTES); ?>" placeholder="AIza..." />
        <label style="display:flex; align-items:center; gap:8px; margin-top:12px"><input type="checkbox" name="maintenance" value="1" <?php echo $config['maintenance']?'checked':''; ?> /> Maintenance mode</label>
        <div style="margin-top:16px"><button class="admin-btn" type="submit">Save</button></div>
    </form>
</div>

<div class="admin-card" style="margin-top:16px">
    <h3>Data: Countries</h3>
    <form method="post" action="save_countries.php">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <div class="admin-form-row">
            <div>
                <textarea class="admin-textarea" name="countries"><?php 
                    $cf = $STORAGE . '/data/countries.json';
                    $curr = is_file($cf) ? file_get_contents($cf) : "";
                    echo htmlspecialchars($curr, ENT_QUOTES);
                ?></textarea>
            </div>
        </div>
        <div style="margin-top:16px"><button class="admin-btn" type="submit">Save Countries</button></div>
    </form>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);




