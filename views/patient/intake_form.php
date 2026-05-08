<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intake Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 600px; }
        label { display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 20px; }
    </style>
</head>
<body>
    <h1>Patient Intake Form</h1>

    <form method="POST">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required>

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="emergency_contact">Emergency Contact:</label>
        <input type="text" id="emergency_contact" name="emergency_contact" required>

        <label for="medical_history">Medical History:</label>
        <textarea id="medical_history" name="medical_history" rows="4"></textarea>

        <label for="current_medications">Current Medications:</label>
        <textarea id="current_medications" name="current_medications" rows="3"></textarea>

        <label for="insurance_provider">Insurance Provider:</label>
        <input type="text" id="insurance_provider" name="insurance_provider">

        <label for="preferred_therapist">Preferred Therapist Preferences:</label>
        <textarea id="preferred_therapist" name="preferred_therapist" rows="3" placeholder="Religion, gender, specialties, etc."></textarea>

        <button type="submit">Submit Intake Form</button>
    </form>
</body>
</html>