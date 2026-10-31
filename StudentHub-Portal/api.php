<?php
require __DIR__ . '/auth_helpers.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function apiRespond($status, $message, $data = null)
{
    http_response_code($status);
    $response = ['success' => $status >= 200 && $status < 300, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function apiStudentPayload($payload)
{
    if (!is_array($payload)) {
        apiRespond(400, 'A JSON object is required.');
    }

    $name = isset($payload['full_name']) && is_string($payload['full_name'])
        ? trim(preg_replace('/\s+/u', ' ', $payload['full_name']))
        : '';
    $email = isset($payload['email']) && is_string($payload['email'])
        ? trim($payload['email'])
        : '';
    $course = isset($payload['course']) && is_string($payload['course'])
        ? trim($payload['course'])
        : '';

    if ($name === '' || strlen($name) > 100) {
        apiRespond(422, 'Enter a student name no longer than 100 characters.');
    }
    if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        apiRespond(422, 'Enter a valid email address.');
    }
    if ($course === '' || strlen($course) > 100) {
        apiRespond(422, 'Enter a course no longer than 100 characters.');
    }

    return [$name, $email, $course];
}

startStudentHubSession();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
$lastActivity = $_SESSION['last_activity'] ?? null;

if (!$userId || !$role || !$lastActivity) {
    apiRespond(401, 'Please sign in as an Admin to manage student records.');
}
if (time() - (int) $lastActivity >= sessionTimeoutSeconds()) {
    $_SESSION = [];
    session_regenerate_id(true);
    apiRespond(401, 'Your session expired. Sign in again to continue.');
}
$_SESSION['last_activity'] = time();
if ($role !== 'Admin') {
    apiRespond(403, 'Only Admin users can manage student records.');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (isset($_GET['id']) && (!$id || $id < 1)) {
    apiRespond(400, 'Student ID must be a positive integer.');
}

if ($method !== 'GET' && $method !== 'POST' && $method !== 'PUT' && $method !== 'DELETE') {
    header('Allow: GET, POST, PUT, DELETE');
    apiRespond(405, 'This HTTP method is not supported.');
}

if ($method !== 'GET') {
    $submittedToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (
        !isset($_SESSION['api_students_csrf'])
        || !is_string($submittedToken)
        || !hash_equals($_SESSION['api_students_csrf'], $submittedToken)
    ) {
        apiRespond(403, 'Your security token is missing or expired. Reload the page and try again.');
    }
}

try {
    require __DIR__ . '/db.php';

    if ($method === 'GET') {
        if (!isset($_SESSION['api_students_csrf'])) {
            $_SESSION['api_students_csrf'] = bin2hex(random_bytes(32));
        }

        if ($id) {
            $statement = $mysqli->prepare(
                'SELECT student_id, full_name, email, course, created_at, updated_at
                 FROM api_students WHERE student_id = ?'
            );
            $statement->bind_param('i', $id);
            $statement->execute();
            $student = $statement->get_result()->fetch_assoc();
            $statement->close();
            if ($student === null) {
                apiRespond(404, 'Student record was not found.');
            }
            apiRespond(200, 'Student record loaded.', [
                'student' => $student,
                'csrf_token' => $_SESSION['api_students_csrf'],
            ]);
        }

        $result = $mysqli->query(
            'SELECT student_id, full_name, email, course, created_at, updated_at
             FROM api_students ORDER BY student_id DESC'
        );
        apiRespond(200, 'Student records loaded.', [
            'students' => $result->fetch_all(MYSQLI_ASSOC),
            'csrf_token' => $_SESSION['api_students_csrf'],
        ]);
    }

    $payload = null;
    if ($method === 'POST' || $method === 'PUT') {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== 0) {
            apiRespond(415, 'Send request data using application/json.');
        }
        try {
            $payload = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            apiRespond(400, 'Request body must contain valid JSON.');
        }
    }

    if ($method === 'POST') {
        [$name, $email, $course] = apiStudentPayload($payload);
        $statement = $mysqli->prepare(
            'INSERT INTO api_students (full_name, email, course) VALUES (?, ?, ?)'
        );
        $statement->bind_param('sss', $name, $email, $course);
        try {
            $statement->execute();
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                apiRespond(409, 'A student with that email address already exists.');
            }
            throw $exception;
        }
        $newId = $mysqli->insert_id;
        $statement->close();
        apiRespond(201, 'Student added successfully.', ['student_id' => $newId]);
    }

    if (!$id) {
        apiRespond(400, 'A valid student ID is required for this request.');
    }

    if ($method === 'PUT') {
        [$name, $email, $course] = apiStudentPayload($payload);
        $statement = $mysqli->prepare(
            'UPDATE api_students SET full_name = ?, email = ?, course = ? WHERE student_id = ?'
        );
        $statement->bind_param('sssi', $name, $email, $course, $id);
        try {
            $statement->execute();
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                apiRespond(409, 'A student with that email address already exists.');
            }
            throw $exception;
        }
        $changed = $statement->affected_rows;
        $statement->close();
        if ($changed === 0) {
            $exists = $mysqli->prepare('SELECT student_id FROM api_students WHERE student_id = ?');
            $exists->bind_param('i', $id);
            $exists->execute();
            $found = $exists->get_result()->fetch_assoc() !== null;
            $exists->close();
            if (!$found) {
                apiRespond(404, 'Student record was not found.');
            }
        }
        apiRespond(200, 'Student details updated successfully.', ['student_id' => $id]);
    }

    $statement = $mysqli->prepare('DELETE FROM api_students WHERE student_id = ?');
    $statement->bind_param('i', $id);
    $statement->execute();
    $deleted = $statement->affected_rows;
    $statement->close();
    if ($deleted === 0) {
        apiRespond(404, 'Student record was not found.');
    }
    apiRespond(200, 'Student deleted successfully.', ['student_id' => $id]);
} catch (RuntimeException $exception) {
    apiRespond(503, 'The student service is temporarily unavailable. Please try again later.');
} catch (Throwable $exception) {
    error_log('StudentHub JSON API error: ' . $exception->getMessage());
    apiRespond(500, 'The student service could not complete the request.');
}
