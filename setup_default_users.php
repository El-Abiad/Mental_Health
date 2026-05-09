<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/encryption.php';
require_once __DIR__ . '/models/User.php';

$users = [
    ['admin', 'admin@clinic.local', 'Admin', 1],
    ['manager', 'manager@clinic.local', 'Manager', 2],
    ['patient', 'patient@clinic.local', 'Patient', 3],
    ['therapist', 'therapist@clinic.local', 'Therapist', 4],
];

$password = '123456';
$created = [];
$skipped = [];

foreach ($users as [$username, $email, $fullname, $roleId]) {
    if (User::findByEmail($email)) {
        $skipped[] = $email;
        continue;
    }

    User::create($username, $email, Encryption::hashPassword($password), $fullname, $roleId, '');
    $created[] = $email;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Setup</title>
    <link rel="stylesheet" href="/Mental_Health/assets/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Default Users Ready</h2>
        <p>Password for all demo users: <strong><?= htmlspecialchars($password) ?></strong></p>
        <?php if ($created): ?>
            <h3>Created</h3>
            <ul>
                <?php foreach ($created as $email): ?><li><?= htmlspecialchars($email) ?></li><?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if ($skipped): ?>
            <h3>Already Exists</h3>
            <ul>
                <?php foreach ($skipped as $email): ?><li><?= htmlspecialchars($email) ?></li><?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="/Mental_Health/controllers/auth_run.php?action=login">Go to Login</a>
    </div>
</body>
</html>
