<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login(); require_admin();
require_once __DIR__.'/../api/db.php';

$pdo = get_pdo();
if (!$pdo) { echo 'DB connection failed'; exit; }

$file = $STORAGE . DIRECTORY_SEPARATOR . 'reservations' . DIRECTORY_SEPARATOR . 'reservations.json';
$items = is_file($file) ? json_decode(file_get_contents($file), true) : [];
if (!is_array($items)) { $items = []; }

$ins = $pdo->prepare("INSERT INTO reservations (booking_id,status,passengers,amount,pickup,dropoff,pickup_date,return_date,created_at) VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status), passengers=VALUES(passengers), amount=VALUES(amount)");
$n=0;
foreach ($items as $r) {
    $booking = (string)($r['booking_id'] ?? ''); if(!$booking) continue;
    $status = (string)($r['status'] ?? 'pending_payment');
    $pax = (int)($r['passengers'] ?? 0);
    $amount = (float)($r['amount']['amount'] ?? 0);
    $pickup = json_encode($r['pickup'] ?? []);
    $dropoff = json_encode($r['dropoff'] ?? []);
    $pd = $r['pickup_date'] ?? null; $rd = $r['return_date'] ?? null; $created = $r['created_at'] ?? date('c');
    $ins->execute([$booking,$status,$pax,$amount,$pickup,$dropoff,$pd,$rd,$created]);
    $n++;
}
echo "Migrated $n reservations.";


