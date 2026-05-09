<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Notes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .note { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        .note-form { margin-top: 20px; }
        textarea { width: 100%; height: 100px; }
    </style>
</head>
<body>
    <h1>Therapist Notes</h1>

    <h2>Existing Notes</h2>
    <?php foreach ($notes as $note): ?>
        <div class="note">
            <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($note['patient_id']); ?></p>
            <p><strong>Content:</strong> <?php echo htmlspecialchars($note['content']); ?></p>
            <p><strong>Timestamp:</strong> <?php echo htmlspecialchars($note['timestamp']); ?></p>
        </div>
    <?php endforeach; ?>

    <div class="note-form">
        <h2>Create New Note</h2>
        <form method="POST">
            <label for="patient_id">Patient ID:</label>
            <input type="number" id="patient_id" name="patient_id" required><br><br>

            <label for="session_note">Note Content:</label><br>
            <textarea id="session_note" name="session_note" required></textarea><br><br>

            <label>Timestamp (auto-generated):</label>
            <input type="text" value="<?php echo date('Y-m-d H:i:s'); ?>" disabled><br><br>

            <button type="submit">Lock Note</button>
        </form>
    </div>

    <a href="/therapist/dashboard">Back to Dashboard</a>
</body>
</html>