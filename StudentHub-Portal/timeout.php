<?php
require __DIR__ . '/auth_helpers.php';
startStudentHubSession();
$message = $_SESSION['timeout_message'] ?? 'Your session expired. Please log in again.';
unset($_SESSION['timeout_message']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Session expired - StudentHub</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card">
      <h1>Session expired</h1>
      <p class="notice error"><?= escapeAuthHtml($message) ?></p>
      <p><a class="button" href="login.php">Log in again</a></p>
    </section>
  </main>
</body>
</html>
