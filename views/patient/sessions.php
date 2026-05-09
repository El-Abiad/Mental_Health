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

    <?php if (empty($sessions)): ?>
        <p>No sessions or appointments found yet.</p>
    <?php else: ?>
        <?php foreach ($sessions as $session): ?>
            <?php $status = $session['status'] ?? 'Scheduled'; ?>
            <div class="session <?php echo strtolower($status); ?>">
                <h3>Session with <?php echo htmlspecialchars($session['therapist_name']); ?></h3>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($session['date']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
                <p><strong>Notes:</strong> <?php echo htmlspecialchars($session['notes'] ?? ''); ?></p>
                <?php if ($status === 'Scheduled' && isset($session['id'])): ?>
                    <a href="/patient/session/<?php echo $session['id']; ?>/cancel">Cancel Session</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($_GET['booked']) && $_GET['booked'] == '1'): ?>
        <p style="color: green; font-weight: bold;">Session booked successfully.</p>
    <?php endif; ?>
    <a href="/patient/book-session">Book New Session</a> | 
    <a href="/patient/dashboard">Back to Dashboard</a>
</body>
</html>