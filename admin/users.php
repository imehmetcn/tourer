<?php
declare(strict_types=1);
require __DIR__.'/_bootstrap.php';
require_login();
require_admin();

$usersFile = $STORAGE . DIRECTORY_SEPARATOR . 'users.json';
$users = read_json($usersFile, []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $role = (string)($_POST['role'] ?? 'editor');
        if ($email && $password) {
            foreach ($users as $u) { if (strtolower($u['email']) === $email) { $exists = true; break; } }
            if (empty($exists)) { $users[] = [ 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT), 'role' => $role ]; write_json($usersFile, $users); log_admin_action('user.create', ['email'=>$email,'role'=>$role]); }
        }
    } elseif ($action === 'password') {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        foreach ($users as &$u) { if (strtolower($u['email']) === $email) { $u['password'] = password_hash($password, PASSWORD_DEFAULT); } }
        unset($u); write_json($usersFile, $users);
        log_admin_action('user.password', ['email'=>$email]);
    } elseif ($action === 'delete') {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $users = array_values(array_filter($users, function($u) use ($email){ return strtolower($u['email']) !== $email; }));
        write_json($usersFile, $users);
        log_admin_action('user.delete', ['email'=>$email]);
    }
    header('Location: /mytransfers/admin/users.php'); exit;
}

ob_start();
?>
<div class="admin-card">
    <h3>Users</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo htmlspecialchars($u['email'], ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars($u['role'] ?? 'editor', ENT_QUOTES); ?></td>
                <td>
                    <form class="inline" method="post" style="display:inline-flex; gap:8px; align-items:center">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
                        <input type="hidden" name="action" value="password" />
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($u['email'], ENT_QUOTES); ?>" />
                        <input class="admin-input" type="password" name="password" placeholder="New password" required />
                        <button class="admin-btn" type="submit">Change</button>
                    </form>
                    <form class="inline" method="post" onsubmit="return confirm('Delete user?')" style="display:inline">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($u['email'], ENT_QUOTES); ?>" />
                        <button class="admin-btn admin-btn--danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3 style="margin-top:24px">Create User</h3>
    <form method="post" class="admin-form-row">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>" />
        <input type="hidden" name="action" value="create" />
        <input class="admin-input" type="email" name="email" placeholder="Email" required />
        <input class="admin-input" type="password" name="password" placeholder="Password" required />
        <select class="admin-select" name="role">
            <option value="editor">editor</option>
            <option value="admin">admin</option>
        </select>
        <button class="admin-btn" type="submit">Create</button>
    </form>
</div>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/_layout.php');
echo str_replace('<!-- PAGE_CONTENT -->', $content, $layout);


