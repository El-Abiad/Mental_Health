<?php
session_start();
require_once '../../controllers/AdminController.php';
require_once '../../config/db.php';
require_once '../../config/encryption.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $db = Database::getConnection();
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = encryption::hashPassword($_POST['password']);
    $fullname = $_POST['fullname'];
    $roleId = $_POST['role'];
    $phone = $_POST['phone'];
    if (AdminController::CreateUser($username, $email, $password, $fullname, $roleId, $phone) > 0) {
        $_SESSION['msg']      = 'User added successfully!';
        $_SESSION['msg_type'] = 'success';
    } else {
        $_SESSION['msg']      = 'Error adding user.';
        $_SESSION['msg_type'] = 'error';
    }

    header('Location: users.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $userId = intval($_POST['id']);
    if (AdminController::DeleteUser($userId)) {
        echo "User Deleting Successfully";
    } else {
        echo  'Error deleting user.';
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/style.css">
    <title>Manage Users</title>
</head>

<body>
    <div class="nav-bar">
        <h1>Manage Users</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="VerifyIntakeForms.php">Verify Intake Forms</a></li>
                <li><a href="violations.php">Violations</a></li>
                <li><a href="../auth/login.php">Logout</a></li>

            </ul>
        </nav>
    </div>
    <div class="users-table">
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Actions</th>
                <th>IsActive</th>
            </tr>
            <?php
            $users = AdminController::GetAllUsers();
            $admin = new AdminController();
            $roles = AdminController::GetAllRoles();
            if (!empty($users) && is_array($users)):
            ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="user-id"><?php echo $user['Id']; ?></td>
                        <td class="Username"><?php echo $user['Username']; ?></td>
                        <td class="Email"><?php echo $user['Email']; ?></td>
                        <td class="Role"><?php echo $admin->GetUserRole($user['Id']); ?></td>
                        <td class="Phone"><?php echo $user['Phone']; ?></td>
                        <td>
                            <a href="UpdateUser.php?id=<?php echo htmlspecialchars($user['Id']); ?>" class="btn-edit">Update User</a>
                            <form action="users.php" method="POST" style="display:inline;"
                                onsubmit="return confirm('Are you sure you want to delete this user?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $user['Id']; ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </td>
                        <td class="IsActive"><?php echo $user["IsActive"] == 1 ? "Yes" : "No" ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="add-user">
        <form action="users.php" method="post">
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
                <?php
                $roles = $admin->GetAllRoles();
                foreach ($roles as $role) {
                    echo "<option value='{$role['RoleId']}'>{$role['RoleName']}</option>";
                }
                ?>
            </select><br><br>
            <button type="submit">Add User</button>
        </form>
    </div>


    <script src="../../assets/app.js"></script>
</body>

</html>