<?php
require_once __DIR__ . '/../../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['Action'] ?? '';
    $userId = (int)($_POST['UserID'] ?? 0);
    $reportId = (int)($_POST['ReportID'] ?? 0);

    if ($reportId > 0 && $userId > 0) {
        AdminController::ChangeViolationReportStatus($reportId, 'Completed');
        if ($action === 'Warn User') {
            AdminController::GiveWarning($userId, "Don't Do This Again");
        }
        if ($action === 'Ban') {
            AdminController::GiveBan($userId);
        }
    }

    header('Location: /Mental_Health/controllers/admin_run.php?action=violations&msg=done');
    exit;
}

$reports = AdminController::GetAllViolationReports();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violations</title>
    <link rel="stylesheet" href="/Mental_Health/assets/style.css">
</head>
<body>
    <div class="nav-bar">
        <h1>Manage Violation Reports</h1>
        <nav>
            <ul>
                <li><a href="/Mental_Health/controllers/admin_run.php?action=dashboard">Dashboard</a></li>
                <li><a href="/Mental_Health/controllers/admin_run.php?action=users">Users</a></li>
                <li><a href="/Mental_Health/controllers/admin_run.php?action=verifyIntakeForms">Verify Intake Forms</a></li>
                <li><a href="/Mental_Health/controllers/auth_run.php?action=logout">Logout</a></li>
            </ul>
        </nav>
    </div>
    <?php if (isset($_GET['msg'])): ?>
        <p class="success">Action completed.</p>
    <?php endif; ?>
    <div class="users-table">
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Report ID</th>
                <th>User</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Resolved By</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($reports as $report): ?>
                <?php $user = AdminController::GetUserById((int)$report['UserId']); ?>
                <tr>
                    <td><?= (int)$report['ReportId'] ?></td>
                    <td><?= htmlspecialchars($user['Username'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars((string)$report['Reason']) ?></td>
                    <td><?= htmlspecialchars((string)$report['Status']) ?></td>
                    <td><?= htmlspecialchars((string)($report['ResolvedBy'] ?? '')) ?></td>
                    <td>
                        <?php if (($report['Status'] ?? '') !== 'Completed'): ?>
                            <form action="/Mental_Health/controllers/admin_run.php?action=violations" method="post" style="display:inline;">
                                <input type="hidden" name="Action" value="Warn User">
                                <input type="hidden" name="UserID" value="<?= (int)$report['UserId'] ?>">
                                <input type="hidden" name="ReportID" value="<?= (int)$report['ReportId'] ?>">
                                <button type="submit">Warn</button>
                            </form>
                            <form action="/Mental_Health/controllers/admin_run.php?action=violations" method="post" style="display:inline;">
                                <input type="hidden" name="Action" value="Ban">
                                <input type="hidden" name="UserID" value="<?= (int)$report['UserId'] ?>">
                                <input type="hidden" name="ReportID" value="<?= (int)$report['ReportId'] ?>">
                                <button type="submit">Ban</button>
                            </form>
                        <?php else: ?>
                            Completed
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
