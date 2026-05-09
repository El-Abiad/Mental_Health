<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session View</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .session-details { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Session Details</h1>

    <div class="session-details">
        <h2>Session #<?php echo $session['id']; ?></h2>
        <p><strong>Patient:</strong> <?php echo htmlspecialchars($session['patient_name']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($session['date']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($session['status']); ?></p>
        <p><strong>Notes:</strong> <?php echo htmlspecialchars($session['notes'] ?? ''); ?></p>
    </div>

    <form method="POST" action="/therapist/session/<?php echo $session['id']; ?>/update">
        <label for="notes">Add Notes:</label><br>
        <textarea id="notes" name="notes" rows="4" cols="50"></textarea><br>
        <button type="submit">Update Session</button>
    </form>

    <a href="/therapist/dashboard">Back to Dashboard</a>
</body>
</html>