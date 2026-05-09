<?php
require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../config/encryption.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = Encryption::hashPassword((string)($_POST['password'] ?? ''));
        $fullname = trim($_POST['fullname'] ?? '');
        $roleId = (int)($_POST['role'] ?? 3);
        $phone = trim($_POST['phone'] ?? '');

        if ($username && $email && $fullname) {
            AdminController::CreateUser($username, $email, $password, $fullname, $roleId, $phone);
            header('Location: /clinic/controllers/admin_run.php?action=users&msg=added');
            exit;
        }
    }

    if ($action === 'delete') {
        AdminController::DeleteUser((int)($_POST['id'] ?? 0));
        header('Location: /clinic/controllers/admin_run.php?action=users&msg=deleted');
        exit;
    }
}

$users = AdminController::GetAllUsers();
$roles = AdminController::GetAllRoles();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/clinic/assets/style.css">
    <title>Manage Users</title>
</head>
<body>
    <div class="nav-bar">
        <h1>Manage Users</h1>
        <nav>
            <ul>
                <li><a href="/clinic/controllers/admin_run.php?action=dashboard">Dashboard</a></li>
                <li><a href="/clinic/controllers/admin_run.php?action=verifyIntakeForms">Verify Intake Forms</a></li>
                <li><a href="/clinic/controllers/admin_run.php?action=violations">Violations</a></li>
                <li><a href="/clinic/controllers/auth_run.php?action=logout">Logout</a></li>
            </ul>
        </nav>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <p class="success"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>

    <div class="users-table">
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone</th>
                <th>IsActive</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$user['Id']) ?></td>
                    <td><?= htmlspecialchars((string)$user['Username']) ?></td>
                    <td><?= htmlspecialchars((string)$user['Email']) ?></td>
                    <td><?= htmlspecialchars((string)($user['RoleName'] ?? '')) ?></td>
                    <td><?= htmlspecialchars((string)($user['Phone'] ?? '')) ?></td>
                    <td><?= ((int)$user['IsActive'] === 1) ? 'Yes' : 'No' ?></td>
                    <td>
                        <a href="/clinic/controllers/admin_run.php?action=updateUser&id=<?= (int)$user['Id'] ?>" class="btn-edit">Update</a>
                        <form action="/clinic/controllers/admin_run.php?action=users" method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$user['Id'] ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="add-user">
        <form action="/clinic/controllers/admin_run.php?action=users" method="post">
            <h2>Add New User</h2>
            <input type="hidden" name="action" value="add">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= (int)$role['RoleId'] ?>"><?= htmlspecialchars($role['RoleName']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Add User</button>
        </form>
    </div>
</body>
</html>
