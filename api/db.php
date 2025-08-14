<?php
declare(strict_types=1);

function get_pdo(): ?PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) { return $pdo; }
    // Load from env or storage/config.json fallback
    $cfgFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'config.json';
    $cfg = [];
    if (is_file($cfgFile)) {
        $raw = file_get_contents($cfgFile);
        $tmp = json_decode($raw, true);
        if (is_array($tmp)) { $cfg = $tmp; }
    }
    $host = getenv('DB_HOST') ?: ($cfg['db_host'] ?? '127.0.0.1');
    $port = getenv('DB_PORT') ?: ($cfg['db_port'] ?? '3306');
    $name = getenv('DB_NAME') ?: ($cfg['db_name'] ?? 'mytransfers');
    $user = getenv('DB_USER') ?: ($cfg['db_user'] ?? 'root');
    $pass = getenv('DB_PASS') ?: ($cfg['db_pass'] ?? '');
    $charset = 'utf8mb4';
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (Throwable $e) {
        // Log error
        $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
        @file_put_contents($logDir . DIRECTORY_SEPARATOR . 'db_error.log', date('c')." ".$e->getMessage()."\n", FILE_APPEND);
        return null;
    }
}


