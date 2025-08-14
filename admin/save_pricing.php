<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_role(['admin','editor']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

verify_csrf();
rate_limit('save_pricing', 30, 300);

$raw = (string)($_POST['pricing'] ?? '');
$json = json_decode($raw, true);
if (!is_array($json)) {
    http_response_code(400);
    echo 'Invalid JSON';
    exit;
}

$path = $STORAGE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'pricing.json';
write_json($path, $json);

log_admin_action('pricing.save', []);

header('Location: /mytransfers/admin/pricing.php');
exit;


