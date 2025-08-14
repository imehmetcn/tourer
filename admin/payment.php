<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_admin();

$cfgFile = $STORAGE . DIRECTORY_SEPARATOR . 'config.json';
$cfg = read_json($cfgFile, []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$cfg['payment_provider'] = trim((string)($_POST['payment_provider'] ?? 'mock')) ?: 'mock';
	$cfg['currency'] = strtoupper(trim((string)($_POST['currency'] ?? 'EUR')) ?: 'EUR');
	$cfg['iyzico_api_key'] = trim((string)($_POST['iyzico_api_key'] ?? ''));
	$cfg['iyzico_secret'] = trim((string)($_POST['iyzico_secret'] ?? ''));
	$cfg['iyzico_base_url'] = trim((string)($_POST['iyzico_base_url'] ?? 'https://sandbox-api.iyzipay.com')) ?: 'https://sandbox-api.iyzipay.com';
	$cfg['iyzico_callback_url'] = trim((string)($_POST['iyzico_callback_url'] ?? ''));
	write_json($cfgFile, $cfg);
	log_admin_action('payment_settings_save');
	header('Location: /mytransfers/admin/payment.php');
	exit;
}

ob_start();
?>
<div class="admin-card">
	<h3>Payment Settings</h3>
	<form method="post">
		<input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
		<label>Provider</label>
		<select class="admin-select" name="payment_provider">
			<?php foreach (['mock','iyzico','stripe','adyen','checkout'] as $p): ?>
			<option value="<?php echo $p; ?>" <?php echo ($cfg['payment_provider'] ?? 'mock')===$p?'selected':''; ?>><?php echo strtoupper($p); ?></option>
			<?php endforeach; ?>
		</select>
		<label style="margin-top:8px">Currency</label>
		<input class="admin-input" type="text" name="currency" value="<?php echo htmlspecialchars($cfg['currency'] ?? 'EUR', ENT_QUOTES); ?>" placeholder="EUR" />

		<h4 style="margin-top:16px">iyzico</h4>
		<label>API Key</label>
		<input class="admin-input" type="text" name="iyzico_api_key" value="<?php echo htmlspecialchars($cfg['iyzico_api_key'] ?? '', ENT_QUOTES); ?>" />
		<label>Secret</label>
		<input class="admin-input" type="text" name="iyzico_secret" value="<?php echo htmlspecialchars($cfg['iyzico_secret'] ?? '', ENT_QUOTES); ?>" />
		<label>Base URL</label>
		<input class="admin-input" type="text" name="iyzico_base_url" value="<?php echo htmlspecialchars($cfg['iyzico_base_url'] ?? 'https://sandbox-api.iyzipay.com', ENT_QUOTES); ?>" />
		<label>Callback URL</label>
		<input class="admin-input" type="text" name="iyzico_callback_url" value="<?php echo htmlspecialchars($cfg['iyzico_callback_url'] ?? '', ENT_QUOTES); ?>" placeholder="http://yourdomain/api/payment/webhook" />

		<div style="margin-top:16px"><button class="admin-btn" type="submit">Save</button></div>
	</form>
</div>

<div class="admin-card" style="margin-top:16px">
	<h3>Webhook Log (tail)</h3>
	<pre class="admin-pre"><?php
		$log = $STORAGE . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'webhook.log';
		echo htmlspecialchars(is_file($log)? implode("\n", array_slice(file($log, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[], -50)) : 'No logs yet', ENT_QUOTES);
	?></pre>
</div>

<div class="admin-card" style="margin-top:16px">
	<h3>Test Connection</h3>
	<div class="admin-form-row">
		<button class="admin-btn" type="button" onclick="testPayment()">Run test</button>
		<pre id="ptest" class="admin-pre" style="min-height:80px"></pre>
	</div>
</div>
<script>
async function testPayment(){
	const el = document.getElementById('ptest'); el.textContent='Testing...';
	try{ const r = await fetch('/mytransfers/api/payment/test'); const j = await r.json(); el.textContent = JSON.stringify(j,null,2); }
	catch(e){ el.textContent = String(e); }
}
</script>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


