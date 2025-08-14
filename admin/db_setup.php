<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_admin();
require_once __DIR__.'/../api/db.php';

$pdo = get_pdo();
if (!$pdo) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "DB connection failed.\n\nPlease configure either:\n- Environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASS)\n- or storage/config.json with keys: db_host, db_name, db_user, db_pass\n\nCurrent config.json:\n";
    @readfile($STORAGE . DIRECTORY_SEPARATOR . 'config.json');
    exit;
}

$sql = [];
$sql[] = "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(190) UNIQUE, password VARCHAR(255), role VARCHAR(20) DEFAULT 'editor', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$sql[] = "CREATE TABLE IF NOT EXISTS reservations (id INT AUTO_INCREMENT PRIMARY KEY, booking_id VARCHAR(64), status VARCHAR(40), passengers INT, amount DECIMAL(10,2), pickup JSON, dropoff JSON, pickup_date DATETIME NULL, return_date DATETIME NULL, pricing_method VARCHAR(16) DEFAULT NULL, from_zone VARCHAR(64) DEFAULT NULL, to_zone VARCHAR(64) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX (booking_id), INDEX (pricing_method), INDEX (from_zone), INDEX (to_zone)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$sql[] = "CREATE TABLE IF NOT EXISTS coupons (id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(64) UNIQUE, discount_type VARCHAR(16), discount_value DECIMAL(10,2), valid_from DATE NULL, valid_to DATE NULL, min_amount DECIMAL(10,2) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$sql[] = "CREATE TABLE IF NOT EXISTS pricing (id INT AUTO_INCREMENT PRIMARY KEY, base_per_km DECIMAL(10,2) DEFAULT 1.20, vehicle_multipliers JSON, region_multipliers JSON, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

try {
    foreach ($sql as $q) { $pdo->exec($q); }
    echo 'DB schema created/ensured.';
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Error: '.$e->getMessage();
}


