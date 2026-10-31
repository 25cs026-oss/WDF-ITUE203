<?php
require __DIR__ . '/auth_helpers.php';
requireUserRole('Admin');

try {
    require __DIR__ . '/db.php';
} catch (RuntimeException $exception) {
    http_response_code(500);
    echo '<!doctype html><html lang="en"><meta charset="UTF-8"><title>Database unavailable</title><body><main><h1>Event management unavailable</h1><p>'
        . escapeAuthHtml($exception->getMessage())
        . '</p></main></body></html>';
    exit;
}

const EVENT_POSTER_MAX_BYTES = 2097152;
const EVENT_POSTER_MIME_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];

function eventPostValue($key)
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
}

function eventEscape($value)
{
    return escapeAuthHtml($value);
}

function eventFlash($message, $type)
{
    $_SESSION['event_flash'] = ['message' => $message, 'type' => $type];
}

function eventRedirect()
{
    header('Location: event_management.php');
    exit;
}

function eventTextLength($value)
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function validateEventFields($fields)
{
    $errors = [];
    if ($fields['title'] === '' || eventTextLength($fields['title']) > 150) {
        $errors[] = 'Enter an event title with no more than 150 characters.';
    }
    if ($fields['description'] === '' || eventTextLength($fields['description']) > 10000) {
        $errors[] = 'Enter an event description with no more than 10,000 characters.';
    }
    if ($fields['location'] === '' || eventTextLength($fields['location']) > 150) {
        $errors[] = 'Enter an event location with no more than 150 characters.';
    }
    $date = DateTime::createFromFormat('Y-m-d\TH:i', $fields['event_date']);
    $dateErrors = DateTime::getLastErrors();
    if (
        $date === false
        || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
        || $date->format('Y-m-d\TH:i') !== $fields['event_date']
    ) {
        $errors[] = 'Choose a valid event date and time.';
    }

    return $errors;
}

function validatePosterUpload($file, $required)
{
    if (!isset($file) || !is_array($file) || !isset($file['error'])) {
        return $required
            ? ['error' => 'Choose an event poster image.']
            : ['error' => null, 'path' => null];
    }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return $required
            ? ['error' => 'Choose an event poster image.']
            : ['error' => null, 'path' => null];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = $file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
            ? 'The poster file exceeds the 2 MB size limit.'
            : 'The poster upload failed. Choose another file and try again.';
        return ['error' => $message];
    }
    if (!isset($file['tmp_name'], $file['size']) || !is_uploaded_file($file['tmp_name'])) {
        return ['error' => 'The uploaded poster could not be verified. Choose the image again.'];
    }
    if ((int) $file['size'] < 1 || (int) $file['size'] > EVENT_POSTER_MAX_BYTES) {
        return ['error' => 'The poster file must be no larger than 2 MB.'];
    }

    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($fileInfo === false) {
        throw new RuntimeException('The server could not initialize image file validation.');
    }
    $mimeType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);
    $imageInfo = getimagesize($file['tmp_name']);
    if (
        !isset(EVENT_POSTER_MIME_TYPES[$mimeType])
        || $imageInfo === false
        || $imageInfo['mime'] !== $mimeType
    ) {
        return ['error' => 'Invalid poster file type. Upload a JPEG, PNG, or WebP image.'];
    }

    return [
        'error' => null,
        'temporary_path' => $file['tmp_name'],
        'extension' => EVENT_POSTER_MIME_TYPES[$mimeType],
    ];
}

function storeEventPoster($upload)
{
    $directory = __DIR__ . '/uploads/event-posters';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('The poster upload directory could not be created.');
    }
    if (!is_writable($directory)) {
        throw new RuntimeException('The poster upload directory is not writable by the PHP server.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $upload['extension'];
    if (!move_uploaded_file($upload['temporary_path'], $directory . '/' . $filename)) {
        throw new RuntimeException('The uploaded poster could not be stored. Check the upload directory permissions.');
    }

    return 'uploads/event-posters/' . $filename;
}

function removeEventPoster($posterPath)
{
    if (!is_string($posterPath) || !preg_match('/\Auploads\/event-posters\/[a-f0-9]{32}\.(?:jpg|png|webp)\z/', $posterPath)) {
        return;
    }
    $absolutePath = __DIR__ . '/' . $posterPath;
    if (is_file($absolutePath) && !unlink($absolutePath)) {
        error_log('StudentHub could not remove old event poster: ' . $posterPath);
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $submittedToken = eventPostValue('csrf_token');
    if (
        !isset($_SESSION['event_csrf'])
        || !hash_equals($_SESSION['event_csrf'], $submittedToken)
    ) {
        http_response_code(403);
        $pageError = 'Your form session expired. Reload this page and try again.';
    } else {
        $auditTransactionStarted = false;
        $action = eventPostValue('action');
        $eventId = filter_var($_POST['event_id'] ?? null, FILTER_VALIDATE_INT);

        try {
            if (in_array($action, ['add', 'update'], true)) {
                $fields = [
                    'title' => eventPostValue('title'),
                    'description' => eventPostValue('description'),
                    'event_date' => eventPostValue('event_date'),
                    'location' => eventPostValue('location'),
                ];
                $errors = validateEventFields($fields);
                if ($action === 'update' && (!$eventId || $eventId < 1)) {
                    $errors[] = 'Select a valid event to update.';
                }
                $posterUpload = validatePosterUpload($_FILES['poster'] ?? null, $action === 'add');
                if ($posterUpload['error'] !== null) {
                    $errors[] = $posterUpload['error'];
                }

                if ($errors) {
                    eventFlash(implode(' ', $errors), 'error');
                } else {
                    $newPosterPath = isset($posterUpload['temporary_path'])
                        ? storeEventPoster($posterUpload)
                        : null;
                    $dateForDatabase = str_replace('T', ' ', $fields['event_date']) . ':00';

                    try {
                        if ($action === 'add') {
                            $mysqli->begin_transaction();
                            $auditTransactionStarted = true;
                            $insert = $mysqli->prepare(
                                'INSERT INTO events (title, description, event_date, location, poster_path)
                                 VALUES (?, ?, ?, ?, ?)'
                            );
                            $insert->bind_param(
                                'sssss',
                                $fields['title'],
                                $fields['description'],
                                $dateForDatabase,
                                $fields['location'],
                                $newPosterPath
                            );
                            $insert->execute();
                            $eventId = (int) $mysqli->insert_id;
                            $insert->close();
                            recordAuditLog(
                                $mysqli,
                                'event.created',
                                'event',
                                $eventId,
                                'Created event: ' . $fields['title']
                            );
                            $mysqli->commit();
                            $auditTransactionStarted = false;
                            eventFlash('Event created successfully.', 'success');
                        } else {
                            $currentPoster = $mysqli->prepare(
                                'SELECT poster_path FROM events WHERE event_id = ?'
                            );
                            $currentPoster->bind_param('i', $eventId);
                            $currentPoster->execute();
                            $currentEvent = $currentPoster->get_result()->fetch_assoc();
                            $currentPoster->close();

                            if ($currentEvent === null) {
                                if ($newPosterPath !== null) {
                                    removeEventPoster($newPosterPath);
                                }
                                eventFlash('The event could not be found.', 'error');
                                eventRedirect();
                            }

                            $mysqli->begin_transaction();
                            $auditTransactionStarted = true;
                            if ($newPosterPath === null) {
                                $update = $mysqli->prepare(
                                    'UPDATE events SET title = ?, description = ?, event_date = ?, location = ?
                                     WHERE event_id = ?'
                                );
                                $update->bind_param(
                                    'ssssi',
                                    $fields['title'],
                                    $fields['description'],
                                    $dateForDatabase,
                                    $fields['location'],
                                    $eventId
                                );
                            } else {
                                $update = $mysqli->prepare(
                                    'UPDATE events
                                     SET title = ?, description = ?, event_date = ?, location = ?, poster_path = ?
                                     WHERE event_id = ?'
                                );
                                $update->bind_param(
                                    'sssssi',
                                    $fields['title'],
                                    $fields['description'],
                                    $dateForDatabase,
                                    $fields['location'],
                                    $newPosterPath,
                                    $eventId
                                );
                            }
                            $update->execute();
                            $update->close();
                            recordAuditLog(
                                $mysqli,
                                'event.updated',
                                'event',
                                (int) $eventId,
                                'Updated event: ' . $fields['title']
                            );
                            $mysqli->commit();
                            $auditTransactionStarted = false;
                            if ($newPosterPath !== null) {
                                removeEventPoster($currentEvent['poster_path']);
                            }
                            eventFlash('Event updated successfully.', 'success');
                        }
                    } catch (mysqli_sql_exception $exception) {
                        if ($auditTransactionStarted) {
                            $mysqli->rollback();
                            $auditTransactionStarted = false;
                        }
                        if ($newPosterPath !== null) {
                            removeEventPoster($newPosterPath);
                        }
                        throw $exception;
                    }
                }
            } elseif ($action === 'delete' && $eventId && $eventId > 0) {
                $selectPoster = $mysqli->prepare('SELECT poster_path FROM events WHERE event_id = ?');
                $selectPoster->bind_param('i', $eventId);
                $selectPoster->execute();
                $event = $selectPoster->get_result()->fetch_assoc();
                $selectPoster->close();

                if ($event === null) {
                    eventFlash('The event could not be found.', 'error');
                } else {
                    $mysqli->begin_transaction();
                    $auditTransactionStarted = true;
                    $delete = $mysqli->prepare('DELETE FROM events WHERE event_id = ?');
                    $delete->bind_param('i', $eventId);
                    $delete->execute();
                    $delete->close();
                    recordAuditLog(
                        $mysqli,
                        'event.deleted',
                        'event',
                        (int) $eventId,
                        'Deleted event: ' . $event['title']
                    );
                    $mysqli->commit();
                    $auditTransactionStarted = false;
                    removeEventPoster($event['poster_path']);
                    eventFlash('Event deleted successfully.', 'success');
                }
            } else {
                eventFlash('The requested event action is not valid.', 'error');
            }
        } catch (mysqli_sql_exception $exception) {
            if ($auditTransactionStarted) {
                $mysqli->rollback();
            }
            error_log('StudentHub event management database error: ' . $exception->getMessage());
            eventFlash('The event could not be saved. Check the database connection and try again.', 'error');
        } catch (RuntimeException $exception) {
            if ($auditTransactionStarted) {
                $mysqli->rollback();
            }
            error_log('StudentHub event poster error: ' . $exception->getMessage());
            eventFlash($exception->getMessage(), 'error');
        }

        eventRedirect();
    }
}

if (!isset($_SESSION['event_csrf'])) {
    $_SESSION['event_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['event_csrf'];
$flash = $_SESSION['event_flash'] ?? null;
unset($_SESSION['event_flash']);

$editEvent = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $edit = $mysqli->prepare(
        'SELECT event_id, title, description, event_date, location, poster_path
         FROM events WHERE event_id = ?'
    );
    $edit->bind_param('i', $editId);
    $edit->execute();
    $editEvent = $edit->get_result()->fetch_assoc();
    $edit->close();
}

$list = $mysqli->prepare(
    'SELECT event_id, title, description, event_date, location, poster_path
     FROM events ORDER BY event_date DESC, event_id DESC'
);
$list->execute();
$events = $list->get_result()->fetch_all(MYSQLI_ASSOC);
$list->close();

function eventDateForInput($value)
{
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
    return $date === false ? '' : $date->format('Y-m-d\TH:i');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Management - StudentHub</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="student-admin">
    <header class="student-admin-header">
      <div>
        <p class="eyebrow">Admin role</p>
        <h1>Event Management</h1>
        <p>Create and manage college events and their posters.</p>
      </div>
      <nav class="event-admin-navigation" aria-label="Admin navigation">
        <a class="button secondary-button" href="dashboard.php">Dashboard</a>
        <a class="button secondary-button" href="admin_dashboard.php">Admin dashboard</a>
        <a class="button secondary-button" href="audit_logs.php">Audit Logs</a>
        <form method="post" action="logout.php">
          <input type="hidden" name="csrf_token" value="<?= eventEscape(logoutCsrfToken()) ?>">
          <button type="submit" class="secondary-button">Logout</button>
        </form>
      </nav>
    </header>

    <?php if ($pageError ?? false): ?>
      <p class="notice error" role="alert"><?= eventEscape($pageError) ?></p>
    <?php endif; ?>
    <?php if ($flash): ?>
      <p class="notice <?= eventEscape($flash['type']) ?>" role="<?= $flash['type'] === 'error' ? 'alert' : 'status' ?>"><?= eventEscape($flash['message']) ?></p>
    <?php endif; ?>

    <section class="student-panel">
      <h2><?= $editEvent ? 'Update event' : 'Create an event' ?></h2>
      <p class="event-upload-help">Required poster formats: JPEG, PNG, or WebP. Maximum file size: 2 MB.</p>
      <form method="post" action="event_management.php" class="student-form event-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= eventEscape($csrfToken) ?>">
        <input type="hidden" name="action" value="<?= $editEvent ? 'update' : 'add' ?>">
        <?php if ($editEvent): ?>
          <input type="hidden" name="event_id" value="<?= eventEscape($editEvent['event_id']) ?>">
        <?php endif; ?>
        <label>Event title
          <input name="title" required maxlength="150" value="<?= eventEscape($editEvent['title'] ?? '') ?>">
        </label>
        <label>Event date and time
          <input name="event_date" type="datetime-local" required value="<?= eventEscape(isset($editEvent['event_date']) ? eventDateForInput($editEvent['event_date']) : '') ?>">
        </label>
        <label>Location
          <input name="location" required maxlength="150" value="<?= eventEscape($editEvent['location'] ?? '') ?>">
        </label>
        <label class="event-description-field">Description
          <textarea name="description" rows="3" maxlength="10000" required><?= eventEscape($editEvent['description'] ?? '') ?></textarea>
        </label>
        <label class="event-poster-field">Event poster <?= $editEvent ? '(optional; leave empty to keep current poster)' : '' ?>
          <input name="poster" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" <?= $editEvent ? '' : 'required' ?>>
        </label>
        <div class="student-form-actions">
          <button type="submit"><?= $editEvent ? 'Save event changes' : 'Create event' ?></button>
          <?php if ($editEvent): ?><a class="button secondary-button" href="event_management.php">Cancel edit</a><?php endif; ?>
        </div>
      </form>
    </section>

    <section class="student-panel">
      <div class="student-list-heading">
        <h2>Event list</h2>
        <span><?= count($events) ?> event(s)</span>
      </div>
      <?php if (!$events): ?>
        <p>No events have been added yet.</p>
      <?php else: ?>
        <div class="event-card-grid">
          <?php foreach ($events as $event): ?>
            <article class="event-admin-card">
              <?php if ($event['poster_path'] !== null && $event['poster_path'] !== ''): ?>
                <img class="event-poster" src="<?= eventEscape($event['poster_path']) ?>" alt="Poster for <?= eventEscape($event['title']) ?>">
              <?php else: ?>
                <div class="event-poster-placeholder">No poster uploaded</div>
              <?php endif; ?>
              <div class="event-admin-card-content">
                <h3><?= eventEscape($event['title']) ?></h3>
                <p><strong>Date:</strong> <?= eventEscape(date('d M Y, g:i A', strtotime($event['event_date']))) ?></p>
                <p><strong>Location:</strong> <?= eventEscape($event['location']) ?></p>
                <p><?= nl2br(eventEscape($event['description'])) ?></p>
                <div class="student-form-actions">
                  <a class="button secondary-button" href="event_management.php?edit=<?= eventEscape($event['event_id']) ?>">Edit</a>
                  <button
                    type="button"
                    class="danger-button"
                    data-delete-event="<?= eventEscape($event['event_id']) ?>"
                    data-event-title="<?= eventEscape($event['title']) ?>"
                  >Delete</button>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <dialog class="student-confirm-dialog" id="delete-confirmation" aria-labelledby="delete-title">
    <h2 id="delete-title">Confirm event deletion</h2>
    <p>Delete <strong data-event-title></strong>? This action cannot be undone.</p>
    <form method="post" action="event_management.php" id="delete-form">
      <input type="hidden" name="csrf_token" value="<?= eventEscape($csrfToken) ?>">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="event_id" value="">
      <div class="student-form-actions">
        <button type="button" class="secondary-button" data-close-dialog>Cancel</button>
        <button type="submit" class="danger-button">Confirm delete</button>
      </div>
    </form>
  </dialog>
  <script>
    const deleteDialog = document.getElementById('delete-confirmation');
    const deleteForm = document.getElementById('delete-form');
    document.querySelectorAll('[data-delete-event]').forEach((button) => {
      button.addEventListener('click', () => {
        deleteForm.elements.event_id.value = button.dataset.deleteEvent;
        deleteDialog.querySelector('[data-event-title]').textContent = button.dataset.eventTitle;
        deleteDialog.showModal();
      });
    });
    document.querySelector('[data-close-dialog]').addEventListener('click', () => deleteDialog.close());
  </script>
</body>
</html>
