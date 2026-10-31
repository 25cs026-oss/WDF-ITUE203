<?php
require __DIR__ . '/auth_helpers.php';
requireUserRole('Admin');
try {
    require __DIR__ . '/db.php';
} catch (RuntimeException $exception) {
    http_response_code(500);
    echo '<!doctype html><html lang="en"><meta charset="UTF-8"><title>Database unavailable</title><body><main><h1>Student records unavailable</h1><p>'
        . escapeAuthHtml($exception->getMessage())
        . '</p></main></body></html>';
    exit;
}

const STUDENT_COURSES = [
    'B.Tech Computer Engineering',
    'B.Tech Information Technology',
    'Bachelor of Computer Applications',
];
const STUDENT_YEARS = ['Year 1', 'Year 2', 'Year 3', 'Year 4'];
const STUDENT_GENDERS = ['Female', 'Male', 'Non-binary', 'Prefer not to say'];

function studentPostValue($key)
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function studentValidateDetails($details, $includePassword)
{
    $errors = [];
    if (!preg_match('/^(?=.{2,100}$)[\p{L}\p{M}]+(?:[ .\'-][\p{L}\p{M}]+)*$/u', $details['full_name'])) {
        $errors[] = 'Enter a valid full name (2–100 letters).';
    }
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $details['username'])) {
        $errors[] = 'Use 3–30 letters, numbers, or underscores for the username.';
    }
    if (strlen($details['email']) > 254 || !filter_var($details['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (
        !preg_match('/^\+?[0-9][0-9\s()-]*$/', $details['mobile'])
        || strlen(preg_replace('/\D/', '', $details['mobile'])) < 8
        || strlen(preg_replace('/\D/', '', $details['mobile'])) > 15
    ) {
        $errors[] = 'Enter a mobile number with 8–15 digits.';
    }
    if (!in_array($details['course'], STUDENT_COURSES, true)) {
        $errors[] = 'Select a valid course.';
    }
    if (!in_array($details['study_year'], STUDENT_YEARS, true)) {
        $errors[] = 'Select a valid year of study.';
    }
    if (!in_array($details['gender'], STUDENT_GENDERS, true)) {
        $errors[] = 'Select a valid gender option.';
    }
    if (
        $includePassword
        && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9\s])\S{8,64}$/', $details['password'])
    ) {
        $errors[] = 'Use 8–64 password characters with uppercase and lowercase letters, a number, and a symbol.';
    }

    return $errors;
}

function studentRedirect($search = '', $course = '')
{
    $filters = array_filter([
        'search' => $search,
        'course' => $course,
    ], static function ($value) {
        return $value !== '';
    });
    header('Location: admin_dashboard.php' . ($filters ? '?' . http_build_query($filters) : ''));
    exit;
}

function studentFlash($message, $type)
{
    $_SESSION['student_flash'] = ['message' => $message, 'type' => $type];
}

function studentEscape($value)
{
    return escapeAuthHtml($value);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $submittedToken = studentPostValue('csrf_token');
    if (
        !isset($_SESSION['student_csrf'])
        || !hash_equals($_SESSION['student_csrf'], $submittedToken)
    ) {
        http_response_code(403);
        $pageError = 'Your form session expired. Reload this page and try again.';
    } else {
        $auditTransactionStarted = false;
        $action = studentPostValue('action');
        $id = filter_var($_POST['account_id'] ?? null, FILTER_VALIDATE_INT);
        $returnSearch = studentPostValue('return_search');
        $returnCourse = studentPostValue('return_course');
        if (!in_array($returnCourse, STUDENT_COURSES, true)) {
            $returnCourse = '';
        }

        try {
            if ($action === 'add' || $action === 'update') {
                $details = [
                    'full_name' => preg_replace('/\s+/u', ' ', studentPostValue('full_name')),
                    'username' => studentPostValue('username'),
                    'email' => studentPostValue('email'),
                    'mobile' => studentPostValue('mobile'),
                    'course' => studentPostValue('course'),
                    'study_year' => studentPostValue('study_year'),
                    'gender' => studentPostValue('gender'),
                    'password' => studentPostValue('password'),
                ];
                $errors = studentValidateDetails($details, $action === 'add');

                if ($action === 'update' && (!$id || $id < 1)) {
                    $errors[] = 'Select a valid student record to update.';
                }

                if (!$errors) {
                    $duplicate = $mysqli->prepare(
                        'SELECT account_id FROM student_accounts
                         WHERE (username = ? OR email = ?)
                           AND (? = 0 OR account_id <> ?)
                         LIMIT 1'
                    );
                    $excludeId = $action === 'update' ? $id : 0;
                    $duplicate->bind_param('ssii', $details['username'], $details['email'], $excludeId, $excludeId);
                    $duplicate->execute();
                    $isDuplicate = $duplicate->get_result()->fetch_assoc() !== null;
                    $duplicate->close();

                    if ($isDuplicate) {
                        $errors[] = 'That username or email is already in use.';
                    }
                }

                if ($errors) {
                    studentFlash(implode(' ', $errors), 'error');
                } elseif ($action === 'add') {
                    $passwordHash = password_hash($details['password'], PASSWORD_DEFAULT);
                    $mysqli->begin_transaction();
                    $auditTransactionStarted = true;
                    $insert = $mysqli->prepare(
                        'INSERT INTO student_accounts
                            (full_name, username, email, mobile, course, study_year, gender, password_hash, role, status)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, \'Student\', \'Active\')'
                    );
                    $insert->bind_param(
                        'ssssssss',
                        $details['full_name'],
                        $details['username'],
                        $details['email'],
                        $details['mobile'],
                        $details['course'],
                        $details['study_year'],
                        $details['gender'],
                        $passwordHash
                    );
                    $insert->execute();
                    $accountId = (int) $mysqli->insert_id;
                    $insert->close();
                    recordAuditLog(
                        $mysqli,
                        'student.created',
                        'student_account',
                        $accountId,
                        'Created student account: ' . $details['full_name']
                    );
                    $mysqli->commit();
                    $auditTransactionStarted = false;
                    studentFlash('Student added successfully.', 'success');
                } else {
                    $mysqli->begin_transaction();
                    $auditTransactionStarted = true;
                    $update = $mysqli->prepare(
                        'UPDATE student_accounts
                         SET full_name = ?, username = ?, email = ?, mobile = ?, course = ?, study_year = ?, gender = ?
                         WHERE account_id = ? AND role = \'Student\''
                    );
                    $update->bind_param(
                        'sssssssi',
                        $details['full_name'],
                        $details['username'],
                        $details['email'],
                        $details['mobile'],
                        $details['course'],
                        $details['study_year'],
                        $details['gender'],
                        $id
                    );
                    $update->execute();
                    $update->close();
                    $exists = $mysqli->prepare(
                        'SELECT account_id FROM student_accounts WHERE account_id = ? AND role = \'Student\''
                    );
                    $exists->bind_param('i', $id);
                    $exists->execute();
                    $studentExists = $exists->get_result()->fetch_assoc() !== null;
                    $exists->close();
                    if ($studentExists) {
                        recordAuditLog(
                            $mysqli,
                            'student.updated',
                            'student_account',
                            (int) $id,
                            'Updated student account: ' . $details['full_name']
                        );
                    }
                    $mysqli->commit();
                    $auditTransactionStarted = false;
                    studentFlash(
                        $studentExists ? 'Student details updated successfully.' : 'Student record could not be found.',
                        $studentExists ? 'success' : 'error'
                    );
                }
            } elseif ($action === 'deactivate' && $id && $id > 0) {
                $mysqli->begin_transaction();
                $auditTransactionStarted = true;
                $deactivate = $mysqli->prepare(
                    'UPDATE student_accounts SET status = \'Inactive\'
                     WHERE account_id = ? AND role = \'Student\' AND status = \'Active\''
                );
                $deactivate->bind_param('i', $id);
                $deactivate->execute();
                $deactivated = $deactivate->affected_rows;
                $deactivate->close();
                if ($deactivated === 1) {
                    recordAuditLog(
                        $mysqli,
                        'student.deactivated',
                        'student_account',
                        (int) $id,
                        'Marked student account inactive.'
                    );
                }
                $mysqli->commit();
                $auditTransactionStarted = false;
                studentFlash(
                    $deactivated === 1 ? 'Student marked inactive successfully.' : 'Student was already inactive or could not be found.',
                    $deactivated === 1 ? 'success' : 'error'
                );
            } else {
                studentFlash('The requested student action is not valid.', 'error');
            }
        } catch (mysqli_sql_exception $exception) {
            if ($auditTransactionStarted) {
                $mysqli->rollback();
            }
            error_log('StudentHub student management database error: ' . $exception->getMessage());
            studentFlash(
                $exception->getCode() === 1062
                    ? 'That username or email is already in use.'
                    : 'The student record could not be saved. Check the database connection and try again.',
                'error'
            );
        }

        studentRedirect($returnSearch, $returnCourse);
    }
}

if (!isset($_SESSION['student_csrf'])) {
    $_SESSION['student_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['student_csrf'];
$flash = $_SESSION['student_flash'] ?? null;
unset($_SESSION['student_flash']);
$search = isset($_GET['search']) && is_string($_GET['search']) ? trim($_GET['search']) : '';
$selectedCourse = isset($_GET['course']) && is_string($_GET['course']) ? $_GET['course'] : '';
if (!in_array($selectedCourse, STUDENT_COURSES, true)) {
    $selectedCourse = '';
}

$where = ['role = \'Student\''];
$types = '';
$parameters = [];
if ($search !== '') {
    $where[] = '(full_name LIKE ? OR username LIKE ? OR email LIKE ?)';
    $term = '%' . $search . '%';
    $types .= 'sss';
    array_push($parameters, $term, $term, $term);
}
if ($selectedCourse !== '') {
    $where[] = 'course = ?';
    $types .= 's';
    $parameters[] = $selectedCourse;
}
$query = 'SELECT account_id, full_name, username, email, mobile, course, study_year, gender, status
          FROM student_accounts WHERE ' . implode(' AND ', $where) . ' ORDER BY account_id';
$list = $mysqli->prepare($query);
if ($types !== '') {
    $list->bind_param($types, ...$parameters);
}
$list->execute();
$students = $list->get_result()->fetch_all(MYSQLI_ASSOC);
$list->close();

$editStudent = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $edit = $mysqli->prepare(
        'SELECT account_id, full_name, username, email, mobile, course, study_year, gender
         FROM student_accounts WHERE account_id = ? AND role = \'Student\''
    );
    $edit->bind_param('i', $editId);
    $edit->execute();
    $editStudent = $edit->get_result()->fetch_assoc();
    $edit->close();
}

$exportQuery = http_build_query(array_filter([
    'search' => $search,
    'course' => $selectedCourse,
], static function ($value) {
    return $value !== '';
}));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Records - StudentHub</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="student-admin">
    <header class="student-admin-header">
      <div>
        <p class="eyebrow">Admin role</p>
        <h1>Student Records</h1>
        <p>Manage student accounts, search records, and export the current list.</p>
      </div>
      <nav class="event-admin-navigation" aria-label="Admin navigation">
        <a class="button secondary-button" href="dashboard.php">Dashboard</a>
        <a class="button secondary-button" href="event_management.php">Manage Events</a>
        <a class="button secondary-button" href="audit_logs.php">Audit Logs</a>
        <form method="post" action="logout.php">
          <input type="hidden" name="csrf_token" value="<?= studentEscape(logoutCsrfToken()) ?>">
          <button type="submit" class="secondary-button">Logout</button>
        </form>
      </nav>
    </header>

    <?php if ($pageError ?? false): ?>
      <p class="notice error" role="alert"><?= studentEscape($pageError) ?></p>
    <?php endif; ?>
    <?php if ($flash): ?>
      <p class="notice <?= studentEscape($flash['type']) ?>" role="<?= $flash['type'] === 'error' ? 'alert' : 'status' ?>"><?= studentEscape($flash['message']) ?></p>
    <?php endif; ?>

    <section class="student-panel">
      <h2><?= $editStudent ? 'Update student details' : 'Add a student' ?></h2>
      <form method="post" action="admin_dashboard.php" class="student-form">
        <input type="hidden" name="csrf_token" value="<?= studentEscape($csrfToken) ?>">
        <input type="hidden" name="action" value="<?= $editStudent ? 'update' : 'add' ?>">
        <input type="hidden" name="return_search" value="<?= studentEscape($search) ?>">
        <input type="hidden" name="return_course" value="<?= studentEscape($selectedCourse) ?>">
        <?php if ($editStudent): ?>
          <input type="hidden" name="account_id" value="<?= studentEscape($editStudent['account_id']) ?>">
        <?php endif; ?>
        <label>Full name
          <input name="full_name" required maxlength="100" value="<?= studentEscape($editStudent['full_name'] ?? '') ?>">
        </label>
        <label>Username
          <input name="username" required minlength="3" maxlength="30" pattern="[A-Za-z0-9_]{3,30}" value="<?= studentEscape($editStudent['username'] ?? '') ?>">
        </label>
        <label>Email
          <input name="email" type="email" required maxlength="254" value="<?= studentEscape($editStudent['email'] ?? '') ?>">
        </label>
        <label>Mobile
          <input name="mobile" type="tel" required maxlength="20" value="<?= studentEscape($editStudent['mobile'] ?? '') ?>">
        </label>
        <label>Course
          <select name="course" required>
            <option value="">Select a course</option>
            <?php foreach (STUDENT_COURSES as $course): ?>
              <option value="<?= studentEscape($course) ?>" <?= ($editStudent['course'] ?? '') === $course ? 'selected' : '' ?>><?= studentEscape($course) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Year of study
          <select name="study_year" required>
            <option value="">Select a year</option>
            <?php foreach (STUDENT_YEARS as $year): ?>
              <option value="<?= studentEscape($year) ?>" <?= ($editStudent['study_year'] ?? '') === $year ? 'selected' : '' ?>><?= studentEscape($year) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Gender
          <select name="gender" required>
            <option value="">Select an option</option>
            <?php foreach (STUDENT_GENDERS as $gender): ?>
              <option value="<?= studentEscape($gender) ?>" <?= ($editStudent['gender'] ?? '') === $gender ? 'selected' : '' ?>><?= studentEscape($gender) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <?php if (!$editStudent): ?>
          <label>Initial password
            <input name="password" type="password" required minlength="8" maxlength="64" autocomplete="new-password">
          </label>
        <?php endif; ?>
        <div class="student-form-actions">
          <button type="submit"><?= $editStudent ? 'Save changes' : 'Add student' ?></button>
          <?php if ($editStudent): ?><a class="button secondary-button" href="admin_dashboard.php">Cancel edit</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="student-panel">
      <div class="student-list-heading">
        <h2>Student list</h2>
        <a class="button" href="student_export.php<?= $exportQuery !== '' ? '?' . studentEscape($exportQuery) : '' ?>">Export CSV</a>
      </div>
      <form method="get" action="admin_dashboard.php" class="student-filters">
        <div class="student-filter-group">
          <label for="student-search">Search name, username, or email</label>
          <input id="student-search" name="search" type="search" value="<?= studentEscape($search) ?>" placeholder="Search students">
        </div>
        <div class="student-filter-group">
          <label for="course-filter">Filter by course</label>
          <select id="course-filter" name="course">
            <option value="">All courses</option>
            <?php foreach (STUDENT_COURSES as $course): ?>
              <option value="<?= studentEscape($course) ?>" <?= $selectedCourse === $course ? 'selected' : '' ?>><?= studentEscape($course) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit">Apply filters</button>
        <a class="button secondary-button" href="admin_dashboard.php">Clear</a>
      </form>

      <div class="student-table-wrap">
        <table class="student-table">
          <thead>
            <tr>
              <th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Mobile</th>
              <th>Course</th><th>Year</th><th>Gender</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$students): ?>
              <tr><td colspan="10">No student records match these filters.</td></tr>
            <?php endif; ?>
            <?php foreach ($students as $student): ?>
              <tr>
                <td><?= studentEscape($student['account_id']) ?></td>
                <td><?= studentEscape($student['full_name']) ?></td>
                <td><?= studentEscape($student['username']) ?></td>
                <td><?= studentEscape($student['email']) ?></td>
                <td><?= studentEscape($student['mobile']) ?></td>
                <td><?= studentEscape($student['course']) ?></td>
                <td><?= studentEscape($student['study_year']) ?></td>
                <td><?= studentEscape($student['gender']) ?></td>
                <td><span class="student-status <?= strtolower(studentEscape($student['status'])) ?>"><?= studentEscape($student['status']) ?></span></td>
                <td class="student-actions">
                  <a class="button secondary-button" href="admin_dashboard.php?edit=<?= studentEscape($student['account_id']) ?>">Edit</a>
                  <?php if ($student['status'] === 'Active'): ?>
                    <button
                      type="button"
                      class="danger-button"
                      data-delete-student="<?= studentEscape($student['account_id']) ?>"
                      data-student-name="<?= studentEscape($student['full_name']) ?>"
                    >Delete</button>
                  <?php else: ?>
                    <span class="student-inactive-label">Inactive</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="student-record-count"><?= count($students) ?> student record(s) shown. Deleted records remain in the database with inactive status.</p>
    </section>
  </main>
  <dialog class="student-confirm-dialog" id="delete-confirmation" aria-labelledby="delete-title">
    <h2 id="delete-title">Confirm student deletion</h2>
    <p>Delete <strong data-student-name></strong>? This will mark the student as inactive. The record will not be permanently removed.</p>
    <form method="post" action="admin_dashboard.php" id="delete-form">
      <input type="hidden" name="csrf_token" value="<?= studentEscape($csrfToken) ?>">
      <input type="hidden" name="action" value="deactivate">
      <input type="hidden" name="account_id" value="">
      <input type="hidden" name="return_search" value="<?= studentEscape($search) ?>">
      <input type="hidden" name="return_course" value="<?= studentEscape($selectedCourse) ?>">
      <div class="student-form-actions">
        <button type="button" class="secondary-button" data-close-dialog>Cancel</button>
        <button type="submit" class="danger-button">Confirm delete</button>
      </div>
    </form>
  </dialog>
  <script>
    const deleteDialog = document.getElementById('delete-confirmation');
    const deleteForm = document.getElementById('delete-form');
    document.querySelectorAll('[data-delete-student]').forEach((button) => {
      button.addEventListener('click', () => {
        deleteForm.elements.account_id.value = button.dataset.deleteStudent;
        deleteDialog.querySelector('[data-student-name]').textContent = button.dataset.studentName;
        deleteDialog.showModal();
      });
    });
    document.querySelector('[data-close-dialog]').addEventListener('click', () => deleteDialog.close());
  </script>
</body>
</html>
