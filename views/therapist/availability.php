<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Availability</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .slot { margin-bottom: 10px; }
        input[type="time"] { margin-left: 10px; }
    </style>
</head>
<body>
    <h1>Manage Availability</h1>

    <form method="POST">
        <h2>Set Weekly Availability</h2>
        <div class="slot">
            <label>Monday: </label>
            <input type="time" name="slots[1][start]" value="<?php echo $availability[0]['start_time'] ?? ''; ?>">
            <input type="time" name="slots[1][end]" value="<?php echo $availability[0]['end_time'] ?? ''; ?>">
        </div>
        <div class="slot">
            <label>Tuesday: </label>
            <input type="time" name="slots[2][start]" value="<?php echo $availability[1]['start_time'] ?? ''; ?>">
            <input type="time" name="slots[2][end]" value="<?php echo $availability[1]['end_time'] ?? ''; ?>">
        </div>
        <div class="slot">
            <label>Wednesday: </label>
            <input type="time" name="slots[3][start]" value="<?php echo $availability[2]['start_time'] ?? ''; ?>">
            <input type="time" name="slots[3][end]" value="<?php echo $availability[2]['end_time'] ?? ''; ?>">
        </div>
        <div class="slot">
            <label>Thursday: </label>
            <input type="time" name="slots[4][start]" value="<?php echo $availability[3]['start_time'] ?? ''; ?>">
            <input type="time" name="slots[4][end]" value="<?php echo $availability[3]['end_time'] ?? ''; ?>">
        </div>
        <div class="slot">
            <label>Friday: </label>
            <input type="time" name="slots[5][start]" value="<?php echo $availability[4]['start_time'] ?? ''; ?>">
            <input type="time" name="slots[5][end]" value="<?php echo $availability[4]['end_time'] ?? ''; ?>">
        </div>

        <h2>Snooze Status</h2>
        <label>
            <input type="checkbox" name="is_snoozed" value="1" <?php echo ($profile['is_snoozed'] ?? 0) ? 'checked' : ''; ?>>
            Snooze (stop accepting new patients)
        </label><br><br>

        <button type="submit">Update Availability</button>
    </form>

    <a href="/therapist/dashboard">Back to Dashboard</a>
</body>
</html>