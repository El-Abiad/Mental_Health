<?php
require_once '../../controllers/AdminController.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'Verify') {
    $formId = intval($_POST['id']);

    if (AdminController::VerifyForm($formId, true)) {
        header("Location: VerifyIntakeForms.php?msg=success");
    } else {
        header("Location: VerifyIntakeForms.php?msg=error");
    }
    exit();
} ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Intake Forms</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>

<body>
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'success'): ?>
            <p style="color:green;">Form verified successfully.</p>
        <?php else: ?>
            <p style="color:red;">Error verifying form.</p>
        <?php endif; ?>
    <?php endif; ?>
    <div class="nav-bar">
        <h1>Manage Intake Forms</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="violations.php">Violations</a></li>
                <li><a href="../auth/login.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="users-table">
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Form ID</th>
                <th>Patient Name</th>
                <th>Responses</th>
                <th>Submitted At</th>
                <th>IsVerified</th>
                <th>Action</th>
            </tr>
            <?php
            $forms = AdminController::GetAllIntakeForms();
            if (!empty($forms) && is_array($forms)):
            ?>
                <?php foreach ($forms as $form): ?>
                    <tr>
                        <td class="id"><?php echo $form['FormId']; ?></td>
                        <td class="Username"><?php echo AdminController::GetPatientName($form['PatientId']); ?></td>
                        <td class="resp"><?php echo $form['Responses']; ?></td>
                        <td class="sa"><?php echo $form['SubmittedAt']; ?></td>
                        <td class="isve"><?php echo $form['isVerified'] ? "Yes" : "No"; ?></td>
                        <?php if (!$form['isVerified']): ?>
                            <td>
                                <form action="VerifyIntakeForms.php" method="POST" style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to Verify this Form?')">
                                    <input type="hidden" name="action" value="Verify">
                                    <input type="hidden" name="id" value="<?php echo $form['FormId']; ?>">
                                    <button type="submit" class="btn-Verify">Verify Form</button>
                                </form>
                            </td>
                        <?php else: ?>
                            <td> The Form Is Verified.</td>
                        <?php endif ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No Forms found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>