<?php
require __DIR__ . '/../../auth_helpers.php';
startStudentHubSession();

function p7Escape($value)
{
    return escapeAuthHtml($value);
}

function p7ReadRegistrations($handle = null)
{
    $ownsHandle = false;
    if (!is_resource($handle)) {
        $file = __DIR__ . '/storage/registrations.json';
        if (!is_file($file)) {
            return [];
        }
        $handle = fopen($file, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Registration storage could not be opened.');
        }
        if (!flock($handle, LOCK_SH)) {
            fclose($handle);
            throw new RuntimeException('Registration storage is temporarily locked.');
        }
        $ownsHandle = true;
    }

    try {
        if (!rewind($handle)) {
            throw new RuntimeException('Registration storage could not be read.');
        }
        $contents = stream_get_contents($handle);
        if ($contents === false) {
            throw new RuntimeException('Registration storage could not be read.');
        }
        if (trim($contents) === '') {
            return [];
        }

        try {
            $registrations = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            error_log('StudentHub Practical 7 JSON storage is invalid: ' . $exception->getMessage());
            throw new RuntimeException('Stored registration data could not be read. Contact the administrator.');
        }

        if (!is_array($registrations) || !array_is_list($registrations)) {
            throw new RuntimeException('Stored registration data has an invalid format.');
        }

        return $registrations;
    } finally {
        if ($ownsHandle) {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}

function p7PostValue($key)
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function p7CsvValue($value)
{
    $value = (string) $value;
    if (preg_match('/\A[\s]*[=+\-@]/u', $value)) {
        return "'" . $value;
    }
    return $value;
}

$storageDirectory = __DIR__ . '/storage';
$storageFile = $storageDirectory . '/registrations.json';
$pageError = '';
$pageSuccess = '';
$errors = [];
$name = '';
$email = '';
$course = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $name = preg_replace('/\s+/u', ' ', p7PostValue('name'));
    $email = p7PostValue('email');
    $course = p7PostValue('course');
    $submittedToken = p7PostValue('csrf_token');

    if (!isset($_SESSION['p7_csrf']) || !hash_equals($_SESSION['p7_csrf'], $submittedToken)) {
        http_response_code(403);
        $pageError = 'Your form session expired. Reload the page and try again.';
    } else {
        if ($name === null || !preg_match('/^(?=.{2,100}$)[\p{L}\p{M}]+(?:[ .\'-][\p{L}\p{M}]+)*$/u', $name)) {
            $errors[] = 'Enter a valid name with 2–100 letters.';
        }
        if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        }
        if (!in_array($course, [
            'B.Tech Computer Engineering',
            'B.Tech Information Technology',
            'Bachelor of Computer Applications',
        ], true)) {
            $errors[] = 'Select a valid course.';
        }

        if ($errors) {
            http_response_code(422);
        } else {
            try {
                if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0750, true) && !is_dir($storageDirectory)) {
                    throw new RuntimeException('Registration storage could not be created.');
                }

                $handle = fopen($storageFile, 'c+');
                if ($handle === false) {
                    throw new RuntimeException('Registration storage could not be opened.');
                }
                if (!flock($handle, LOCK_EX)) {
                    fclose($handle);
                    throw new RuntimeException('Registration storage is temporarily locked.');
                }

                try {
                    $registrations = p7ReadRegistrations($handle);
                    foreach ($registrations as $registration) {
                        if (
                            isset($registration['email'])
                            && is_string($registration['email'])
                            && strcasecmp($registration['email'], $email) === 0
                        ) {
                            http_response_code(409);
                            $errors[] = 'This email address is already registered.';
                            break;
                        }
                    }

                    if (!$errors) {
                        $registrations[] = [
                            'registration_id' => bin2hex(random_bytes(8)),
                            'name' => $name,
                            'email' => $email,
                            'course' => $course,
                            'registered_at' => date(DATE_ATOM),
                        ];
                        $json = json_encode(
                            $registrations,
                            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
                        ) . PHP_EOL;
                        if (!ftruncate($handle, 0) || !rewind($handle)) {
                            throw new RuntimeException('Registration data could not be updated.');
                        }
                        $written = fwrite($handle, $json);
                        if ($written !== strlen($json) || !fflush($handle)) {
                            throw new RuntimeException('Registration data could not be saved completely.');
                        }
                        $pageSuccess = 'Registration saved to the JSON file successfully.';
                        $name = '';
                        $email = '';
                        $course = '';
                    }
                } finally {
                    flock($handle, LOCK_UN);
                    fclose($handle);
                }
            } catch (JsonException | RuntimeException $exception) {
                error_log('StudentHub Practical 7 storage error: ' . $exception->getMessage());
                http_response_code(500);
                $pageError = $exception instanceof JsonException
                    ? 'Registration data could not be encoded.'
                    : $exception->getMessage();
            }
        }
    }
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    try {
        $registrations = p7ReadRegistrations();
    } catch (RuntimeException $exception) {
        error_log('StudentHub Practical 7 CSV export failed: ' . $exception->getMessage());
        http_response_code(500);
        exit($exception->getMessage());
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="practical7-registrations.csv"');
    $output = fopen('php://output', 'wb');
    if ($output === false) {
        throw new RuntimeException('CSV export could not be opened.');
    }
    fputcsv($output, ['Registration ID', 'Name', 'Email', 'Course', 'Registered At']);
    foreach ($registrations as $registration) {
        fputcsv($output, [
            p7CsvValue($registration['registration_id'] ?? ''),
            p7CsvValue($registration['name'] ?? ''),
            p7CsvValue($registration['email'] ?? ''),
            p7CsvValue($registration['course'] ?? ''),
            p7CsvValue($registration['registered_at'] ?? ''),
        ]);
    }
    fclose($output);
    exit;
}

try {
    $registrations = p7ReadRegistrations();
} catch (RuntimeException $exception) {
    error_log('StudentHub Practical 7 listing failed: ' . $exception->getMessage());
    http_response_code(500);
    $pageError = $exception->getMessage();
    $registrations = [];
}

if (!isset($_SESSION['p7_csrf'])) {
    $_SESSION['p7_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['p7_csrf'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Practical 7: PHP JSON File Storage - StudentHub</title>
  <link rel="stylesheet" href="../../css/common.css">
</head>
<body>
  <main class="auth-container">
    <header class="page-title">
      <p>Practical 7</p>
      <h1>PHP Registration and JSON Storage</h1>
      <p>Server-side validation, duplicate checking, locked JSON-file storage, and CSV export.</p>
    </header>

    <section class="auth-card" aria-labelledby="form-title">
      <h2 id="form-title">Register a student</h2>
      <?php if ($pageSuccess !== ''): ?>
        <p class="form-message success-message" role="status"><?= p7Escape($pageSuccess) ?></p>
      <?php endif; ?>
      <?php if ($pageError !== ''): ?>
        <p class="form-message error-message" role="alert"><?= p7Escape($pageError) ?></p>
      <?php endif; ?>
      <?php if ($errors): ?>
        <div class="form-message error-message" role="alert">
          <ul><?php foreach ($errors as $error): ?><li><?= p7Escape($error) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>
      <form method="post" action="index.php">
        <input type="hidden" name="csrf_token" value="<?= p7Escape($csrfToken) ?>">
        <label for="name">Full name</label>
        <input id="name" name="name" required minlength="2" maxlength="100" value="<?= p7Escape($name) ?>">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required maxlength="254" value="<?= p7Escape($email) ?>">
        <label for="course">Course</label>
        <select id="course" name="course" required>
          <option value="">Select a course</option>
          <?php foreach (['B.Tech Computer Engineering', 'B.Tech Information Technology', 'Bachelor of Computer Applications'] as $courseOption): ?>
            <option value="<?= p7Escape($courseOption) ?>" <?= $course === $courseOption ? 'selected' : '' ?>><?= p7Escape($courseOption) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit">Save registration</button>
      </form>
      <p><a class="button" href="index.php?export=csv">Export registrations as CSV</a></p>
      <p><a href="../../home.html">Back to StudentHub</a></p>
    </section>

    <section class="auth-card" aria-labelledby="records-title">
      <h2 id="records-title">Saved registrations</h2>
      <div class="table-wrapper">
        <table>
          <thead><tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Course</th><th scope="col">Registered</th></tr></thead>
          <tbody>
            <?php if (!$registrations): ?><tr><td colspan="4">No file-based registrations yet.</td></tr><?php endif; ?>
            <?php foreach (array_reverse($registrations) as $registration): ?>
              <tr>
                <td><?= p7Escape($registration['name'] ?? '') ?></td>
                <td><?= p7Escape($registration['email'] ?? '') ?></td>
                <td><?= p7Escape($registration['course'] ?? '') ?></td>
                <td><?= p7Escape($registration['registered_at'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
