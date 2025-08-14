<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_admin();

$cfgFile = $STORAGE . DIRECTORY_SEPARATOR . 'config.json';
$cfg = read_json($cfgFile, []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$cfg['email_from'] = trim((string)($_POST['email_from'] ?? '')) ?: 'no-reply@localhost';
	write_json($cfgFile, $cfg);
	log_admin_action('email_settings_save');
	header('Location: /mytransfers/admin/email.php');
	exit;
}

if (isset($_GET['test']) && $_GET['test']==='1') {
	require_once __DIR__.'/../api/mail.php';
	$to = trim((string)($_GET['to'] ?? '')); if ($to===''){ $to='test@example.com'; }
	@send_system_mail($to, 'Test mail', '<b>Hello</b>');
	header('Location: /mytransfers/admin/email.php');
	exit;
}

ob_start();
?>
<div class="admin-card">
	<h3>Email Settings</h3>
	<form method="post">
		<input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
		<label>From</label>
		<input class="admin-input" type="text" name="email_from" value="<?php echo htmlspecialchars($cfg['email_from'] ?? 'no-reply@localhost', ENT_QUOTES); ?>" />
		<div style="margin-top:16px"><button class="admin-btn" type="submit">Save</button></div>
	</form>
</div>

<div class="admin-card" style="margin-top:16px">
    <h3>Templates</h3>
    <form method="post" action="email_templates_save.php">
        <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <label>Reservation (reservation.html)</label>
        <textarea class="admin-textarea" name="reservation_html"><?php
            $dir = $STORAGE . '/templates'; if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
            $f = $dir.'/reservation.html'; echo htmlspecialchars(is_file($f)? file_get_contents($f) : '<h3>Booking {{booking_id}}</h3><p>Status: {{status}}</p><p>Amount: {{amount}}</p><p><a href="{{voucher_url}}">Voucher</a></p>', ENT_QUOTES);
        ?></textarea>
        <div style="margin-top:8px"><button class="admin-btn" type="submit">Save Templates</button></div>
    </form>
</div>

<div class="admin-card" style="margin-top:16px">
    <h3>Send Test</h3>
    <form method="get">
        <input type="hidden" name="test" value="1" />
        <input class="admin-input" type="email" name="to" placeholder="you@example.com" />
        <div style="margin-top:8px"><button class="admin-btn" type="submit">Send</button></div>
    </form>
    <small class="admin-muted">Gönderimler storage/logs/mail.log'a yazılır.</small>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


