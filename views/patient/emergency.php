<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Contact</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .card { border: 1px solid #f44336; padding: 20px; border-radius: 8px; background: #fff5f5; }
        .card h1 { color: #b71c1c; }
        .card p { margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 20px; background: #f44336; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Emergency</h1>
        <p>If you are in immediate danger or need urgent support, please call the emergency hotline immediately.</p>
        <p><strong>Emergency Hotline:</strong> <a href="tel:+123456789" class="btn">Call Now</a></p>
        <p>If the call button doesn't work, dial the local emergency services number on your phone.</p>
    </div>

    <p><a href="/patient/dashboard">Back to Dashboard</a></p>
</body>
</html><button onclick="window.location.href='../../patient_run.php?emergency=1'">
    🚨 CRISIS CALL
</button>