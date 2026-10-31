<?php

function escapeHtml($value)
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function showResult($title, $message, $errors = [], $statusCode = 200)
{
    http_response_code($statusCode);
    $safeTitle = escapeHtml($title);
    $safeMessage = escapeHtml($message);

    echo '<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . $safeTitle . ' - StudentHub</title>
  <link rel="stylesheet" href="css/common.css">
</head>
<body>
  <main class="auth-container">
    <section class="page-title"><h1>StudentHub Registration</h1></section>
    <section class="auth-card">
      <h2>' . $safeTitle . '</h2>
      <p class="form-message ' . ($statusCode >= 400 ? 'error-message' : 'success-message') . '">' . $safeMessage . '</p>';

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
    if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
        return '';
    }

    return trim($_POST[$key]);
}

function saveRegistration($registration)
{
    $directory = __DIR__ . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        return 'error';
    }

    $path = $directory . DIRECTORY_SEPARATOR . 'registrations.json';
    $file = fopen($path, 'c+');
    if ($file === false) {
        return 'error';
    }

    if (!flock($file, LOCK_EX)) {
        fclose($file);
        return 'error';
    }

    rewind($file);
    $contents = stream_get_contents($file);
    if ($contents === false) {
        flock($file, LOCK_UN);
        fclose($file);
        return 'error';
    }

    if (trim($contents) === '') {
        $registrations = [];
    } else {
        $decoded = json_decode($contents);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            flock($file, LOCK_UN);
            fclose($file);
            return 'error';
        }

        $registrations = json_decode($contents, true);
    }

    foreach ($registrations as $savedRegistration) {
        if (
            is_array($savedRegistration)
            && isset($savedRegistration['email'])
            && strtolower($savedRegistration['email']) === strtolower($registration['email'])
        ) {
            flock($file, LOCK_UN);
            fclose($file);
            return 'duplicate';
        }
    }

    $registrations[] = $registration;
    $json = json_encode($registrations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        flock($file, LOCK_UN);
        fclose($file);
        return 'error';
    }

    rewind($file);
    $output = $json . PHP_EOL;
    $bytesWritten = ftruncate($file, 0) ? fwrite($file, $output) : false;
    $written = $bytesWritten === strlen($output) && fflush($file);
    flock($file, LOCK_UN);
    fclose($file);

    return $written ? 'saved' : 'error';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    showResult('Submission not accepted', 'Please submit the registration form using the POST method.', [], 405);
    exit;
}

$name = preg_replace('/\s+/u', ' ', postValue('name'));
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

$registration = [
    'name' => $name,
    'email' => $email,
    'mobile' => $mobile,
    'course' => $course,
    'year' => $year,
    'gender' => $gender,
    'passwordHash' => password_hash($password, PASSWORD_DEFAULT),
    'submittedAt' => date(DATE_ATOM)
];

$saveResult = saveRegistration($registration);
if ($saveResult === 'duplicate') {
    showResult('Email already registered', 'There is already a registration for this email address.', [], 409);
    exit;
}
if ($saveResult !== 'saved') {
    showResult('Could not save registration', 'The registration file could not be written. Check that the server can write to the studentHUB/data folder.', [], 500);
    exit;
}

showResult('Registration successful', 'Thanks, ' . $name . '! Your registration has been saved.');
