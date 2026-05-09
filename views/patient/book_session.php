<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Session</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        select, input { width: 100%; padding: 10px; margin-top: 8px; border: 1px solid #ccc; border-radius: 4px; }
        button { margin-top: 20px; padding: 12px 18px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>Book a Session</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="/patient/book-session">
        <label for="therapist_id">Choose Therapist</label>
        <?php if (empty($therapists)): ?>
            <p>No therapists available right now. Please try again later.</p>
        <?php else: ?>
            <select id="therapist_id" name="therapist_id" required>
                <option value="">Select a therapist</option>
                <?php foreach ($therapists as $therapist): ?>
                    <option value="<?php echo htmlspecialchars($therapist['id']); ?>"><?php echo htmlspecialchars($therapist['name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="session_date">Session Date & Time</label>
            <input type="datetime-local" id="session_date" name="session_date" required>

            <button type="submit">Book Session</button>
        <?php endif; ?>
    </form>

    <p><a href="/patient/dashboard">Back to Dashboard</a></p>
</body>
</html>