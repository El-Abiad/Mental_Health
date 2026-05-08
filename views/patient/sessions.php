<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .session { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        .upcoming { border-color: #4CAF50; }
        .completed { border-color: #2196F3; }
        .cancelled { border-color: #f44336; }
    </style>
</head>
<body>
    <h1>My Sessions</h1>

    <?php foreach ($sessions as $session): ?>
        <div class="session <?php echo strtolower($session['status']); ?>">
            <h3>Session with <?php echo htmlspecialchars($session['therapist_name']); ?></h3>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($session['date']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($session['status']); ?></p>
            <p><strong>Notes:</strong> <?php echo htmlspecialchars($session['notes'] ?? ''); ?></p>
            <?php if ($session['status'] === 'Scheduled'): ?>
                <a href="/patient/session/<?php echo $session['id']; ?>/cancel">Cancel Session</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <a href="/patient/dashboard">Back to Dashboard</a>
</body>
</html>