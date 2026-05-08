<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Agreement</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .agreement { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; max-height: 400px; overflow-y: scroll; }
        .signature { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Patient Agreement</h1>

    <div class="agreement">
        <h2>Terms and Conditions</h2>
        <p>By signing this agreement, you agree to the following terms:</p>
        <ul>
            <li>You will attend scheduled sessions or provide 24-hour notice for cancellations.</li>
            <li>You understand that therapy is confidential except in cases of imminent danger.</li>
            <li>You agree to provide accurate information during intake and sessions.</li>
            <li>You understand that late cancellations may incur fees.</li>
            <li>You agree to follow the treatment plan recommended by your therapist.</li>
        </ul>

        <h2>Privacy Policy</h2>
        <p>Your personal information will be kept confidential and secure. We comply with HIPAA regulations.</p>

        <h2>Emergency Procedures</h2>
        <p>In case of emergency, contact local crisis services or use the emergency button in your dashboard.</p>
    </div>

    <form method="POST" class="signature">
        <label>
            <input type="checkbox" name="agree" value="1" required>
            I have read and agree to the terms and conditions.
        </label><br><br>

        <label for="signature">Electronic Signature:</label>
        <input type="text" id="signature" name="signature" placeholder="Type your full name" required><br><br>

        <button type="submit">Sign Agreement</button>
    </form>
</body>
</html>