<?php

function startStudentHubSession()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function escapeAuthHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sessionTimeoutSeconds()
{
    $configuredTimeout = getenv('STUDENTHUB_SESSION_TIMEOUT');
    if ($configuredTimeout === false || !ctype_digit($configuredTimeout) || (int) $configuredTimeout < 1) {
        return 900;
    }

    return (int) $configuredTimeout;
}

function requireAuthenticatedUser()
{
    startStudentHubSession();

    if (!isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['last_activity'])) {
        $_SESSION['auth_error'] = 'Please log in to access the dashboard.';
        header('Location: login.php');
        exit;
    }

    if (time() - (int) $_SESSION['last_activity'] >= sessionTimeoutSeconds()) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['timeout_message'] = 'Your session expired due to inactivity. Please log in again.';
        header('Location: timeout.php');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function requireUserRole($requiredRole)
{
    requireAuthenticatedUser();

    if ($_SESSION['role'] !== $requiredRole) {
        http_response_code(403);
        renderAuthPage(
            'Unauthorized Access',
            'This dashboard is available only to ' . $requiredRole . ' users.',
            false
        );
        exit;
    }
}

function recordAuditLog(mysqli $mysqli, string $action, string $entityType, ?int $entityId, string $details): void
{
    $actorId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    $actorName = isset($_SESSION['full_name']) ? (string) $_SESSION['full_name'] : 'Unknown user';
    $actorRole = isset($_SESSION['role']) ? (string) $_SESSION['role'] : 'Unknown';
    $statement = $mysqli->prepare(
        'INSERT INTO audit_logs
            (actor_account_id, actor_name, actor_role, action, entity_type, entity_id, details)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $statement->bind_param(
        'issssis',
        $actorId,
        $actorName,
        $actorRole,
        $action,
        $entityType,
        $entityId,
        $details
    );
    $statement->execute();
    $statement->close();
}

function renderAuthPage($title, $message, $showLogout = true)
{
    $safeTitle = escapeAuthHtml($title);
    $safeMessage = escapeAuthHtml($message);
    echo '<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . $safeTitle . ' - StudentHub</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="auth-shell">
    <section class="auth-card">
      <h1>' . $safeTitle . '</h1>
      <p class="notice ' . ($title === 'Unauthorized Access' ? 'error' : 'success') . '">' . $safeMessage . '</p>';

    if ($showLogout) {
        echo '<p><a class="button" href="dashboard.php">Go to my dashboard</a></p>';
        echo '<form method="post" action="logout.php"><input type="hidden" name="csrf_token" value="' . escapeAuthHtml(logoutCsrfToken()) . '"><button type="submit">Logout</button></form>';
    } else {
        echo '<p><a class="button" href="login.php">Go to login</a></p>';
    }

    echo '</section>
  </main>
</body>
</html>';
}

function logoutCsrfToken()
{
    if (!isset($_SESSION['logout_csrf'])) {
        $_SESSION['logout_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['logout_csrf'];
}

function clearStudentHubSession()
{
    startStudentHubSession();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $cookie['path'],
            'domain' => $cookie['domain'],
            'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'],
            'samesite' => $cookie['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}
