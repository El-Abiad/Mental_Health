<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin-bottom: 30px; }
        .appointment, .session { border: 1px solid #ccc; padding: 10px; margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Therapist Dashboard</h1>

    <div class="section">
        <h2>Upcoming Appointments</h2>
        <?php foreach ($appointments as $appointment): ?>
            <div class="appointment">
                <p>Patient: <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
                <p>Date: <?php echo htmlspecialchars($appointment['date']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section">
        <h2>Today's Sessions</h2>
        <?php foreach ($sessions as $session): ?>
            <div class="session">
                <p>Patient: <?php echo htmlspecialchars($session['patient_name']); ?></p>
                <p>Time: <?php echo htmlspecialchars($session['time']); ?></p>
                <a href="/therapist/session/<?php echo $session['id']; ?>">View Session</a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section">
        <h2>Missed High-Risk Patients</h2>
        <?php foreach ($missedHighRisk as $patient): ?>
            <div class="appointment">
                <p>Patient: <?php echo htmlspecialchars($patient['name']); ?></p>
                <p>Last Session: <?php echo htmlspecialchars($patient['last_session']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section">
        <h2>Weekly Mood Reports</h2>
        <?php foreach ($weeklyMoodReports as $report): ?>
            <div class="appointment">
                <p>Patient: <?php echo htmlspecialchars($report['patient_name']); ?></p>
                <p>Mood: <?php echo htmlspecialchars($report['mood']); ?></p>
                <p>Date: <?php echo htmlspecialchars($report['date']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="/therapist/availability">Manage Availability</a>
    <a href="/therapist/notes">View Notes</a>
</body>
</html>