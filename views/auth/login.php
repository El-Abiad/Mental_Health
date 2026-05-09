<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/Mental_Health/assets/style.css">
</head>

<body>
    <div class="form-container">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="/Mental_Health/controllers/auth_run.php?action=loginPost" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="/Mental_Health/controllers/auth_run.php?action=register">Register</a></p>
    </div>
</body>

</html>