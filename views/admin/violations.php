<?php
require_once "../../controllers/AdminController.php";

if($_SERVER["REQUEST_METHOD"]==='GET' && isset($_GET["Action"])  && $_GET["Action"]==="Warn User"){
    $userid=$_GET["UserID"];
    $reportid=$_GET["ReportID"];
    if(AdminController::ChangeViolationReportStatus($reportid,"Completed")){
        AdminController::GiveWarning($userid,"Don't Do This Again");
        echo "Report Completed Successfully";
    }
    else{
        echo "Report Completed Failed";
    }
}
if($_SERVER["REQUEST_METHOD"]==='GET' && isset($_GET["Action"])  && $_GET["Action"]==="Bann"){
    $userid=$_GET["UserID"];
    $reportid=$_GET["ReportID"];
    if(AdminController::ChangeViolationReportStatus($reportid,"Completed")){
        AdminController::GiveBan($userid);
        echo "Report Completed Successfully";
    }
    else{
        echo "Report Completed Failed";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violations Reports</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <div class="nav-bar">
        <h1>Manage Users</h1>
        <nav>
            <ul>
                <li><a href="../admin/dashboard.php">Dashboard</a></li>
                <li><a href="../admin/users.php">Users</a></li>
                <li><a href="../auth/login.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="Violation-table">
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ReportID</th>
                <th>Username</th>
                <th>Reason</th>
                <th>Status</th>
                <th>ResolvedBy</th>
                <th>Actions</th>
            </tr>
            <?php
            $Reports=AdminController::GetAllViolationReports();
            if (!empty($Reports) && is_array($Reports)):
            ?>
                <?php foreach ($Reports as $Report): ?>
                    <tr>
                        <td class="ReportID"><?php  echo $Report['ReportId']; ?></td>
                        <td class="Username"><?php $user=AdminController::GetUserById(intval($Report['UserId']));
                        echo $user["Username"]; ?></td>
                        <td class="Reason"><?php echo $Report['Reason']; ?></td>
                        <td class="Status"><?php echo $Report['Status']?></td>
                        <td class="ResolvedBy"><?php echo $Report['ResolvedBy']; ?></td>
                        <?php if($Report["Status"]!="Completed"):?>
                        <td>
                            <form action="violations.php" method="get">
                                <input type="text" name="Action" value="Warn User" hidden>
                                <input type="text" name="UserID" value="<?php echo $Report["UserId"]?>"hidden>
                                <input type="text" name="ReportID" value="<?php echo $Report["ReportId"]?>"hidden>
                                <button type="submit">Warn User</button>
                            </form>
                            <form action="violations.php" method="get">
                                <input type="text" name="Action" value="Bann" hidden>
                                <input type="text" name="UserID" value="<?php echo $Report["UserId"]?>"hidden>
                                <input type="text" name="ReportID" value="<?php echo $Report["ReportId"]?>"hidden>
                                <button type="submit">Ban User</button>
                            </form>
                        </td>
                        <?php else: ?>
                            <td>The Report Is Completed</td>
                        <?php endif;?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No users found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>