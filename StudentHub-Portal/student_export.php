<?php
require __DIR__ . '/auth_helpers.php';
requireUserRole('Admin');
try {
    require __DIR__ . '/db.php';
} catch (RuntimeException $exception) {
    http_response_code(500);
    exit($exception->getMessage());
}

$courses = [
    'B.Tech Computer Engineering',
    'B.Tech Information Technology',
    'Bachelor of Computer Applications',
];
$search = isset($_GET['search']) && is_string($_GET['search']) ? trim($_GET['search']) : '';
$course = isset($_GET['course']) && is_string($_GET['course']) ? $_GET['course'] : '';
if (!in_array($course, $courses, true)) {
    $course = '';
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
if ($course !== '') {
    $where[] = 'course = ?';
    $types .= 's';
    $parameters[] = $course;
}

try {
    $statement = $mysqli->prepare(
        'SELECT account_id, full_name, username, email, mobile, course, study_year, gender, status
         FROM student_accounts WHERE ' . implode(' AND ', $where) . ' ORDER BY account_id'
    );
    if ($types !== '') {
        $statement->bind_param($types, ...$parameters);
    }
    $statement->execute();
    $students = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    $statement->close();
} catch (mysqli_sql_exception $exception) {
    error_log('StudentHub CSV export database error: ' . $exception->getMessage());
    http_response_code(500);
    exit('The student records could not be exported. Check the database connection and try again.');
}

function csvSafeCell($value)
{
    $value = (string) $value;
    if (preg_match('/^\s*[=+\-@]/u', $value)) {
        return "'" . $value;
    }

    return $value;
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="student_records.csv"');
header('X-Content-Type-Options: nosniff');
$output = fopen('php://output', 'w');
if ($output === false) {
    http_response_code(500);
    exit('The CSV export could not be generated.');
}
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['ID', 'Full Name', 'Username', 'Email', 'Mobile', 'Course', 'Year of Study', 'Gender', 'Status']);
foreach ($students as $student) {
    fputcsv($output, array_map('csvSafeCell', array_values($student)));
}
fclose($output);
