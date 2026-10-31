<?php
require __DIR__ . '/auth_helpers.php';
startStudentHubSession();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    renderAuthPage('Login required', 'Submit the login form to continue.', false);
    exit;
}

$email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    $_SESSION['auth_error'] = 'Invalid email or password.';
    header('Location: login.php');
    exit;
}

try {
    require __DIR__ . '/db.php';
    $statement = $mysqli->prepare(
        'SELECT account_id, full_name, username, email, password_hash, role
         FROM student_accounts
         WHERE email = ?
         LIMIT 1'
    );
    $statement->bind_param('s', $email);
    $statement->execute();
    $user = $statement->get_result()->fetch_assoc();
    $statement->close();
} catch (Throwable $exception) {
    error_log('StudentHub login database error: ' . $exception->getMessage());
    http_response_code(500);
    renderAuthPage('Login unavailable', 'The database could not verify your account. Please try again later.', false);
    exit;
}

if ($user === null || !password_verify($password, $user['password_hash'])) {
    $_SESSION['auth_error'] = 'Invalid email or password.';
    header('Location: login.php');
    exit;
}

session_regenerate_id(true);
$_SESSION = [
    'user_id' => (int) $user['account_id'],
    'full_name' => $user['full_name'],
    'username' => $user['username'],
    'email' => $user['email'],
    'role' => $user['role'],
    'last_activity' => time(),
    'session_regenerated' => true,
];

header('Location: dashboard.php');
exit;
