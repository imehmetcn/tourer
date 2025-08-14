<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_role(['admin','editor']);
rate_limit('reservation_action', 60, 300);

$act = $_POST['action'] ?? ($_GET['act'] ?? '');
$ids = $_POST['booking_ids'] ?? [];
$one = $_GET['booking_id'] ?? '';
$csrf = $_POST['csrf'] ?? ($_GET['csrf'] ?? '');
if (!$csrf || !hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
    http_response_code(400); echo 'Invalid CSRF'; exit;
}

if ($one) { $ids = [$one]; }

require_once __DIR__.'/../api/index.php'; // for helper functions

foreach ($ids as $bid) {
    $bid = (string)$bid;
    if ($act === 'mark_paid') {
        updateReservationStatus($bid, 'paid');
    } elseif ($act === 'cancel') {
        updateReservationStatus($bid, 'canceled');
    } elseif ($act === 'resend_mail') {
        $res = getReservationById($bid);
        if ($res && !empty($res['email'])) {
            require_once __DIR__.'/../api/mail.php';
            $token = computeVoucherToken($bid);
            $voucherUrl = '/mytransfers/public/voucher.html?booking_id=' . rawurlencode($bid) . '&token=' . rawurlencode($token);
            $html = '<h3>Your booking</h3><p>Booking ID: <b>'.$bid.'</b></p><p>Status: '.($res['status'] ?? '').'</p><p>Voucher: <a href="'.$voucherUrl.'">'.$voucherUrl.'</a></p>';
            @send_system_mail((string)$res['email'], 'Your booking '.$bid, $html);
        }
    } elseif ($act === 'open_voucher') {
        $token = computeVoucherToken($bid);
        header('Location: /mytransfers/public/voucher.html?booking_id=' . rawurlencode($bid) . '&token=' . rawurlencode($token));
        exit;
    }
}

header('Location: /mytransfers/admin/reservations.php');
exit;


