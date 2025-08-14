<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
verify_csrf();

$dir = $STORAGE . DIRECTORY_SEPARATOR . 'templates';
if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
$reservation = (string)($_POST['reservation_html'] ?? '');
file_put_contents($dir . DIRECTORY_SEPARATOR . 'reservation.html', $reservation);
log_admin_action('email_templates_save');
header('Location: /mytransfers/admin/email.php');
exit;


