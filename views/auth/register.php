<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="/clinic/assets/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Create Account</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="/clinic/controllers/auth_run.php?action=registerPost" method="POST">
            <input type="text"  name="username" placeholder="Username" required>
            <input type="text"  name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text"  name="phone" placeholder="Phone (optional)">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="/clinic/controllers/auth_run.php?action=login">Login</a></p>
    </div>
</body>
</html>
