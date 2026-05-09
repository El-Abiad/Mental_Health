<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency</title>
    <link rel="stylesheet" href="/clinic/assets/style.css">
</head>
<body>
    <h1>Emergency Alert</h1>
    <?php if (!empty($message)): ?>
        <p class="success"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <p>If this is an immediate emergency, contact local emergency services first.</p>
    <a class="btn-delete" href="/clinic/controllers/patient_run.php?action=emergency&trigger=1">Send Crisis Alert</a>
    <a href="/clinic/controllers/patient_run.php?action=dashboard">Back to Dashboard</a>
</body>
</html>
