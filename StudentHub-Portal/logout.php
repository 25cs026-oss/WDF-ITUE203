<?php
require __DIR__ . '/auth_helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    renderAuthPage('Logout not accepted', 'Use the Logout button to end your session.', false);
    exit;
}

startStudentHubSession();
$submittedToken = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
    ? $_POST['csrf_token']
    : '';
if (!isset($_SESSION['logout_csrf']) || !hash_equals($_SESSION['logout_csrf'], $submittedToken)) {
    http_response_code(403);
    renderAuthPage('Logout not accepted', 'The logout form expired. Return to your dashboard and try again.', false);
    exit;
}

clearStudentHubSession();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logged out - StudentHub</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card">
      <h1>Logged out successfully</h1>
      <p class="notice success">Your session has ended.</p>
      <p><a class="button" href="login.php">Return to login</a></p>
    </section>
  </main>
</body>
</html>
