<?php
require __DIR__ . '/auth_helpers.php';
requireUserRole('Student');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard - StudentHub</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="dashboard-shell">
    <section class="dashboard-card">
      <p class="eyebrow">Student role</p>
      <h1>Student Dashboard</h1>
      <p>Welcome, <?= escapeAuthHtml($_SESSION['full_name']) ?>.</p>
      <p class="notice success">Successful student login. Session ID was regenerated after authentication.</p>
      <p>Signed in as <?= escapeAuthHtml($_SESSION['email']) ?>.</p>
      <form method="post" action="logout.php">
        <input type="hidden" name="csrf_token" value="<?= escapeAuthHtml(logoutCsrfToken()) ?>">
        <button type="submit">Logout</button>
      </form>
    </section>
  </main>
</body>
</html>
