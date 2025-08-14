<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';

$usersFile = $STORAGE . DIRECTORY_SEPARATOR . 'users.json';
$users = read_json($usersFile, []);

$error = '';
// Simple lockout storage
$authDir = $STORAGE . DIRECTORY_SEPARATOR . 'auth';
if (!is_dir($authDir)) { @mkdir($authDir, 0775, true); }
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$lockKey = $authDir . DIRECTORY_SEPARATOR . md5('lockout|'.$ip);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    rate_limit('login', 10, 900); // 10 attempts per 15 minutes per IP
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    // Check lockout
    $lock = ['blocked_until'=>0,'fails'=>0];
    if (is_file($lockKey)) { $lock = json_decode(file_get_contents($lockKey), true) ?: $lock; }
    if (($lock['blocked_until'] ?? 0) > time()) {
        $error = 'Too many attempts. Please try again later.';
    } else {
    $user = null;
    foreach ($users as $u) {
        if (strtolower($u['email'] ?? '') === $email) { $user = $u; break; }
    }
    if ($user && password_verify($password, $user['password'] ?? '')) {
        session_regenerate_id(true);
        $_SESSION['admin_user'] = [ 'email' => $user['email'], 'role' => $user['role'] ?? 'admin' ];
        // reset lock
        @unlink($lockKey);
        header('Location: /mytransfers/admin');
        exit;
    } else {
        $error = 'Invalid credentials';
        // increase fail count
        $fails = (int)($lock['fails'] ?? 0) + 1;
        $blockedUntil = 0;
        if ($fails >= 5) { $blockedUntil = time() + 300; $fails = 0; }
        file_put_contents($lockKey, json_encode(['fails'=>$fails,'blocked_until'=>$blockedUntil]));
    }
    }
}

// Seed default admin if file empty
if (empty($users)) {
    $users[] = [ 'email' => 'admin@local', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'admin' ];
    write_json($usersFile, $users);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="/mytransfers/admin/assets/admin.css?v=<?php echo @filemtime(__DIR__.'/assets/admin.css') ?: time(); ?>" />
    </head>
<body class="<?php echo (empty($_COOKIE['adm_theme']) || $_COOKIE['adm_theme']==='dark') ? 'theme-dark' : '';?>">
    <div style="display:flex; align-items:center; justify-content:center; height:100vh;">
        <form class="admin-card" style="width:360px" method="post">
            <h3 style="margin:0 0 8px">Admin Login</h3>
            <div class="admin-muted" style="margin-bottom:12px">Sign in to manage content and settings</div>
            <?php if ($error): ?><div style="color:#b00020; margin:8px 0;"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div><?php endif; ?>
            <input class="admin-input" type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
            <input class="admin-input" type="email" name="email" placeholder="Email" required />
            <input class="admin-input" type="password" name="password" placeholder="Password" required />
            <button class="admin-btn" type="submit" style="width:100%; margin-top:8px">Sign in</button>
            <div class="admin-muted" style="margin-top:8px">Default: admin@local / admin123</div>
        </form>
    </div>
</body>
</html>




