<?php

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function showResult($title, $message, $errors = [], $statusCode = 200)
{
    http_response_code($statusCode);
    echo '<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . escapeHtml($title) . ' - StudentHub</title>
  <link rel="stylesheet" href="css/common.css">
</head>
<body>
  <main class="auth-container">
    <section class="page-title"><h1>StudentHub Registration</h1></section>
    <section class="auth-card">
      <h2>' . escapeHtml($title) . '</h2>
      <p class="form-message ' . ($statusCode >= 400 ? 'error-message' : 'success-message') . '">' . escapeHtml($message) . '</p>';

    if ($errors) {
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . escapeHtml($error) . '</li>';
        }
        echo '</ul>';
    }

    echo '<p><a class="button" href="pages/register.html">Back to registration</a></p>
    </section>
  </main>
</body>
</html>';
}

function postValue($key)
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    showResult('Submission not accepted', 'Please submit the registration form using the POST method.', [], 405);
    exit;
}

$name = preg_replace('/\s+/u', ' ', postValue('name'));
$username = postValue('username');
$email = postValue('email');
$mobile = postValue('mobile');
$course = postValue('course');
$year = postValue('year');
$password = postValue('password');
$confirmPassword = postValue('confirmPassword');
$gender = postValue('gender');
$errors = [];

if ($name === null || !preg_match('/^(?=.{2,60}$)[\p{L}\p{M}]+(?:[ .\'-][\p{L}\p{M}]+)*$/u', $name)) {
    $errors[] = 'Enter a name with 2–60 letters. Spaces, apostrophes, periods, and hyphens are allowed.';
}
if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
    $errors[] = 'Use 3–30 letters, numbers, or underscores for your username.';
}
if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Enter a valid email address.';
}
if (
    !preg_match('/^\+?[0-9][0-9\s()-]*$/', $mobile)
    || strlen(preg_replace('/\D/', '', $mobile)) < 8
    || strlen(preg_replace('/\D/', '', $mobile)) > 15
) {
    $errors[] = 'Enter a valid mobile number with 8–15 digits.';
}
if (!in_array($course, [
    'B.Tech Computer Engineering',
    'B.Tech Information Technology',
    'Bachelor of Computer Applications'
], true)) {
    $errors[] = 'Select a valid course.';
}
if (!in_array($year, ['Year 1', 'Year 2', 'Year 3', 'Year 4'], true)) {
    $errors[] = 'Select a valid year of study.';
}
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9\s])\S{8,64}$/', $password)) {
    $errors[] = 'Use 8–64 characters with uppercase and lowercase letters, a number, and a symbol.';
}
if ($confirmPassword === '' || $confirmPassword !== $password) {
    $errors[] = 'The passwords do not match.';
}
if (!in_array($gender, ['Female', 'Male', 'Non-binary', 'Prefer not to say'], true)) {
    $errors[] = 'Select a valid gender option.';
}
if (!isset($_POST['terms']) || !is_string($_POST['terms']) || $_POST['terms'] !== 'on') {
    $errors[] = 'Accept the terms and conditions to register.';
}

if ($errors) {
    showResult('Please check your details', 'The registration could not be completed. Correct these items and try again:', $errors, 422);
    exit;
}

try {
    require __DIR__ . '/db.php';

    $duplicateCheck = $mysqli->prepare(
        'SELECT username, email
         FROM student_accounts
         WHERE username = ? OR email = ?
         LIMIT 1'
    );
    $duplicateCheck->bind_param('ss', $username, $email);
    $duplicateCheck->execute();
    $existingAccount = $duplicateCheck->get_result()->fetch_assoc();
    $duplicateCheck->close();

    if ($existingAccount !== null) {
        if (strcasecmp($existingAccount['email'], $email) === 0) {
            showResult('Email already registered', 'There is already an account for this email address.', [], 409);
        } else {
            showResult('Username already taken', 'Choose a different username and try again.', [], 409);
        }
        exit;
    }

    $insert = $mysqli->prepare(
        'INSERT INTO student_accounts
            (full_name, username, email, mobile, course, study_year, gender, password_hash)
         VALUES
            (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $insert->bind_param('ssssssss', $name, $username, $email, $mobile, $course, $year, $gender, $passwordHash);
    $insert->execute();
    $insert->close();
} catch (mysqli_sql_exception $exception) {
    if ($exception->getCode() === 1062) {
        showResult('Account details already in use', 'That email or username is already registered. Use different account details.', [], 409);
        exit;
    }

    error_log('StudentHub registration database error: ' . $exception->getMessage());
    showResult('Could not save registration', 'The database could not save your registration. Check that MySQL is running and schema.sql has been imported.', [], 500);
    exit;
} catch (Throwable $exception) {
    error_log('StudentHub registration error: ' . $exception->getMessage());
    showResult('Could not save registration', 'The database could not save your registration. Check that MySQL is running and schema.sql has been imported.', [], 500);
    exit;
}

showResult('Registration successful', 'Thanks, ' . $name . '! Your account was added to the MySQL database.');
