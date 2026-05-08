<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Therapists</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .therapist { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        .add-form { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>My Favorite Therapists</h1>

    <?php foreach ($favorites as $therapist): ?>
        <div class="therapist">
            <h3><?php echo htmlspecialchars($therapist['name']); ?></h3>
            <p><strong>Specialties:</strong> <?php echo htmlspecialchars($therapist['specialties'] ?? ''); ?></p>
            <p><strong>License Verified:</strong> <?php echo $therapist['license_verified'] ? 'Yes' : 'No'; ?></p>
            <p><strong>Available:</strong> <?php echo $therapist['is_snoozed'] ? 'Currently unavailable' : 'Available'; ?></p>
        </div>
    <?php endforeach; ?>

    <div class="add-form">
        <h2>Add Therapist to Favorites</h2>
        <form method="POST">
            <label for="therapist_id">Therapist ID:</label>
            <input type="number" id="therapist_id" name="therapist_id" required><br><br>
            <button type="submit">Add to Favorites</button>
        </form>
    </div>

    <a href="/patient/dashboard">Back to Dashboard</a>
</body>
</html>