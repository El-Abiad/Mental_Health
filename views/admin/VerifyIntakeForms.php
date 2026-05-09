<?php
require_once __DIR__ . '/../../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'Verify') {
    AdminController::VerifyForm((int)($_POST['id'] ?? 0), true);
    header('Location: /clinic/controllers/admin_run.php?action=verifyIntakeForms&msg=success');
    exit;
}

$forms = AdminController::GetAllIntakeForms();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Intake Forms</title>
    <link rel="stylesheet" href="/clinic/assets/style.css">
</head>
<body>
    <div class="nav-bar">
        <h1>Manage Intake Forms</h1>
        <nav>
            <ul>
                <li><a href="/clinic/controllers/admin_run.php?action=dashboard">Dashboard</a></li>
                <li><a href="/clinic/controllers/admin_run.php?action=users">Users</a></li>
                <li><a href="/clinic/controllers/admin_run.php?action=violations">Violations</a></li>
                <li><a href="/clinic/controllers/auth_run.php?action=logout">Logout</a></li>
            </ul>
        </nav>
    </div>
    <?php if (isset($_GET['msg'])): ?>
        <p class="success">Form verified successfully.</p>
    <?php endif; ?>
    <div class="users-table">
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Form ID</th>
                <th>Patient Name</th>
                <th>Responses</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($forms as $form): ?>
                <tr>
                    <td><?= (int)$form['FormId'] ?></td>
                    <td><?= htmlspecialchars(AdminController::GetPatientName((int)$form['PatientId'])) ?></td>
                    <td><?= htmlspecialchars((string)$form['Responses']) ?></td>
                    <td><?= $form['isVerified'] ? 'Verified' : 'Pending' ?></td>
                    <td>
                        <?php if (!$form['isVerified']): ?>
                            <form action="/clinic/controllers/admin_run.php?action=verifyIntakeForms" method="POST" style="display:inline;" onsubmit="return confirm('Verify this form?')">
                                <input type="hidden" name="action" value="Verify">
                                <input type="hidden" name="id" value="<?= (int)$form['FormId'] ?>">
                                <button type="submit" class="btn-Verify">Verify Form</button>
                            </form>
                        <?php else: ?>
                            Done
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
