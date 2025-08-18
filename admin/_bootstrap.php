<?php
declare(strict_types=1);

// Session hardening - only if session not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    // SameSite and Secure flags (Secure only effective on HTTPS)
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

$ROOT = dirname(__DIR__);
$STORAGE = $ROOT . DIRECTORY_SEPARATOR . 'storage';
$DATA_DIR = $STORAGE . DIRECTORY_SEPARATOR . 'data';

if (!is_dir($STORAGE)) { @mkdir($STORAGE, 0775, true); }
if (!is_dir($DATA_DIR)) { @mkdir($DATA_DIR, 0775, true); }

// Security headers for admin UI
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer-when-downgrade');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
// CSP: allow self + minimal CDNs used in admin (boxicons, chart.js, google fonts)
$csp = "default-src 'self'; script-src 'self' https://cdn.jsdelivr.net https://unpkg.com 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://unpkg.com https://fonts.googleapis.com; img-src 'self' data:; font-src 'self' https://unpkg.com https://fonts.gstatic.com data:; connect-src 'self'; frame-ancestors 'none'";
header('Content-Security-Policy: ' . $csp);

if (!function_exists('read_json')) {
    function read_json(string $path, $default) {
        if (!is_file($path)) { return $default; }
        $raw = file_get_contents($path);
        $data = json_decode($raw, true);
        return is_array($data) || is_object($data) ? $data : $default;
    }
}

if (!function_exists('write_json')) {
    function write_json(string $path, $data): void {
        $dir = dirname($path);
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array {
        return $_SESSION['admin_user'] ?? null;
    }
}

if (!function_exists('require_login')) {
    function require_login(): void {
        if (!current_user()) {
            header('Location: /mytransfers/admin/login.php');
            exit;
        }
    }
}

if (!function_exists('require_admin')) {
    function require_admin(): void {
        $u = current_user();
        if (!$u || ($u['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}

if (!function_exists('require_role')) {
    function require_role(array $roles): void {
        $u = current_user();
        $role = $u['role'] ?? '';
        if (!$u || !in_array($role, $roles, true)) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
}

if (!function_exists('log_admin_action')) {
    function log_admin_action(string $action, array $details = []): void {
        $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
        $file = $logDir . DIRECTORY_SEPARATOR . 'admin.log';
        $entry = [
            'ts' => date('c'),
            'user' => current_user()['email'] ?? 'anonymous',
            'action' => $action,
            'details' => $details,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        @file_put_contents($file, json_encode($entry, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['csrf'];
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): void {
        $token = $_POST['csrf'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            exit;
        }
    }
}

// Simple in-memory (file-based) rate limiter for admin actions
if (!function_exists('rate_limit')) {
    function rate_limit(string $bucket, int $limit, int $windowSeconds): void {
        $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'ratelimit';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = $dir . DIRECTORY_SEPARATOR . md5($bucket.'|'.$ip);
        $now = time();
        $data = ['ts'=>$now,'count'=>0];
        if (is_file($key)) { $data = json_decode(file_get_contents($key), true) ?: $data; }
        if (($now - ($data['ts'] ?? 0)) > $windowSeconds) { $data = ['ts'=>$now,'count'=>0]; }
        $data['count']++;
        file_put_contents($key, json_encode($data));
        if ($data['count'] > $limit) {
            http_response_code(429);
            header('Retry-After: '.max(1, $windowSeconds - ($now - ($data['ts'] ?? 0))));
            echo 'Too Many Requests';
            exit;
        }
    }
}




