<?php
require __DIR__ . '/auth_helpers.php';
startStudentHubSession();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$message = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_error']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - StudentHub</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card">
      <h1>StudentHub Login</h1>
      <p>Sign in with your registered email and password.</p>
      <?php if ($message !== ''): ?>
        <p class="notice error" role="alert"><?= escapeAuthHtml($message) ?></p>
      <?php endif; ?>
      <form method="post" action="authenticate.php">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="username" maxlength="254" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <button type="submit">Login</button>
      </form>
      <p>New student? <a href="pages/register.html">Register here</a>.</p>
    </section>
  </main>
</body>
</html>
