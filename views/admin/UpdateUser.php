<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>

<body>
    <?php
    require_once "../../models/Admin.php";
    require_once "../../config/db.php";
    $roles = AdminController::GetAllRoles(Database::getConnection());
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $db = Database::getConnection();
        $id = intval($_GET['id']);
        $user = AdminController::GetUserById($db, $id);
        if (!$user) {
            echo "User not found.";
            exit();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db       = Database::getConnection();
        $id       = intval($_POST['id']);
        $username = strval($_POST['username']);
        $email    = strval($_POST['email']);
        $fullname = strval($_POST['FullName']);
        $phone    = strval($_POST['phone'] ?? '');
        $roleId   = intval($_POST['role']);
        if (AdminController::UpdateUser($db, $id, $username, $email, $fullname, $phone, $roleId)) {
            echo "User updated successfully!";
        } else {
            echo "Error updating user.";
        }
        header('Location: users.php');
        exit();
    }

    ?>
    <div class="edit-user">
        <form action="UpdateUser.php" method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit-id" value="<?php echo $user['Id']; ?>">
            <label for="edit-username">Username:</label>
            <input type="text" id="edit-username" name="username" value="<?php echo htmlspecialchars($user['Username'] ?? ''); ?>" required>

            <label for="edit-email">Email:</label>
            <input type="email" id="edit-email" name="email" value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>" required>

            <label for="edit-phone">Phone:</label>
            <input type="text" id="edit-phone" name="phone" value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>">

            <label for="edit-fullname">Full Name:</label>
            <input type="text" id="edit-fullname" name="FullName" value="<?php echo htmlspecialchars($user['FullName'] ?? ''); ?>" required>
            <label for="edit-role">Role:</label>
            <select id="edit-role" name="role">
                <?php
                $currentUserRoleName = AdminController::GetUserRole(Database::getConnection(), $user['Id']);
                foreach ($roles as $role) {
                    $selected = ($currentUserRoleName == $role['RoleName']) ? 'selected' : '';
                    echo "<option value='{$role['RoleId']}' " . $selected . "  >{$role['RoleName']}</option>";
                }
                ?>
            </select><br><br>
            <button type="submit">Update User</button>
        </form>
    </div>
</body>

</html>