<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin-bottom: 30px; }
        .session { border: 1px solid #ccc; padding: 10px; margin: 5px 0; }
        .mood-log { border: 1px solid #ddd; padding: 10px; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Patient Dashboard</h1>

    <div class="section">
        <h2>Upcoming Sessions</h2>
        <?php foreach ($upcomingSessions as $session): ?>
            <div class="session">
                <p>Therapist: <?php echo htmlspecialchars($session['therapist_name']); ?></p>
                <p>Date: <?php echo htmlspecialchars($session['date']); ?></p>
                <p>Status: <?php echo htmlspecialchars($session['status']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section">
        <h2>Mood Logs</h2>
        <?php foreach ($moodLogs as $log): ?>
            <div class="mood-log">
                <p>Mood: <?php echo htmlspecialchars($log['mood']); ?></p>
                <p>Notes: <?php echo htmlspecialchars($log['notes']); ?></p>
                <p>Date: <?php echo htmlspecialchars($log['date']); ?></p>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="/patient/mood-log">
            <label for="mood">Current Mood:</label>
            <select id="mood" name="mood">
                <option value="happy">Happy</option>
                <option value="sad">Sad</option>
                <option value="anxious">Anxious</option>
                <option value="calm">Calm</option>
            </select><br>
            <label for="notes">Notes:</label><br>
            <textarea id="notes" name="notes" rows="3" cols="50"></textarea><br>
            <button type="submit">Log Mood</button>
        </form>
    </div>

    <div class="section">
        <a href="/patient/sessions">View All Sessions</a> |
        <a href="/patient/favorites">Manage Favorites</a> |
        <a href="/patient/emergency">Emergency</a>
    </div>
</body>
</html>