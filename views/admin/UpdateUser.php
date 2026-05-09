<?php
require_once __DIR__ . '/../../controllers/AdminController.php';

$roles = AdminController::GetAllRoles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    AdminController::UpdateUser(
        $id,
        trim($_POST['username'] ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['FullName'] ?? ''),
        trim($_POST['phone'] ?? ''),
        (int)($_POST['role'] ?? 3)
    );
    header('Location: /clinic/controllers/admin_run.php?action=users&msg=updated');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$user = AdminController::GetUserById($id);
if (!$user) {
    echo 'User not found.';
    exit;
}
$currentUserRoleName = AdminController::GetUserRole((int)$user['Id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="/clinic/assets/style.css">
</head>
<body>
    <div class="edit-user">
        <form action="/clinic/controllers/admin_run.php?action=updateUser" method="post">
            <input type="hidden" name="id" value="<?= (int)$user['Id'] ?>">
            <label for="edit-username">Username:</label>
            <input type="text" id="edit-username" name="username" value="<?= htmlspecialchars($user['Username'] ?? '') ?>" required>
            <label for="edit-email">Email:</label>
            <input type="email" id="edit-email" name="email" value="<?= htmlspecialchars($user['Email'] ?? '') ?>" required>
            <label for="edit-phone">Phone:</label>
            <input type="text" id="edit-phone" name="phone" value="<?= htmlspecialchars($user['Phone'] ?? '') ?>">
            <label for="edit-fullname">Full Name:</label>
            <input type="text" id="edit-fullname" name="FullName" value="<?= htmlspecialchars($user['FullName'] ?? '') ?>" required>
            <label for="edit-role">Role:</label>
            <select id="edit-role" name="role">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= (int)$role['RoleId'] ?>" <?= ($currentUserRoleName === $role['RoleName']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['RoleName']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Update User</button>
            <a href="/clinic/controllers/admin_run.php?action=users">Cancel</a>
        </form>
    </div>
</body>
</html>
