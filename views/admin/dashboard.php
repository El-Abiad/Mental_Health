<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/clinic/assets/style.css">
    <title>Admin Dashboard</title>
</head>
<body class="admin-dashboard">
    <div class="nav-bar">
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="/clinic/controllers/admin_run.php?action=users">Manage Users</a></li>
                <li><a href="/clinic/controllers/admin_run.php?action=violations">Violations</a></li>
                <li><a href="/clinic/controllers/admin_run.php?action=verifyIntakeForms">Verify Intake Forms</a></li>
                <li><a href="/clinic/controllers/auth_run.php?action=logout">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="dashboard-content">
        <h2>Welcome, Admin</h2>
        <p>Use the navigation to manage users, reports, and patient intake status.</p>
    </div>
</body>
</html>
