<?php

try {
    require __DIR__ . '/db.php';

    $eventId = 1;
    $statement = $pdo->prepare(
        'SELECT
            r.registration_id,
            s.full_name,
            s.email,
            e.title AS event_title,
            r.registered_at
         FROM registrations AS r
         INNER JOIN students AS s ON s.student_id = r.student_id
         INNER JOIN events AS e ON e.event_id = r.event_id
         WHERE e.event_id = :event_id
         ORDER BY r.registration_id'
    );
    $statement->execute(['event_id' => $eventId]);
    $registrations = $statement->fetchAll();
} catch (Throwable $exception) {
    http_response_code(500);
    error_log('StudentHub database test failed: ' . $exception->getMessage());
    $errorMessage = $exception instanceof RuntimeException
        ? $exception->getMessage()
        : 'The database query failed. Check the PHP error log for details.';
}

function escapeOutput($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Connection Test - StudentHub</title>
  <link rel="stylesheet" href="css/common.css">
</head>
<body>
  <main class="auth-container">
    <section class="page-title">
      <h1>Practical 8: Database Connection</h1>
      <?php if (isset($errorMessage)): ?>
        <p class="form-message error-message"><?= escapeOutput($errorMessage) ?></p>
      <?php else: ?>
        <p class="form-message success-message">Database connection successful. Registration data is displayed below.</p>
      <?php endif; ?>
    </section>

    <?php if (!isset($errorMessage)): ?>
      <section class="auth-card">
        <h2>Web Development Workshop Registrations</h2>
        <?php if ($registrations): ?>
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Registration ID</th>
                  <th>Student</th>
                  <th>Email</th>
                  <th>Event</th>
                  <th>Registered At</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($registrations as $registration): ?>
                  <tr>
                    <td><?= escapeOutput($registration['registration_id']) ?></td>
                    <td><?= escapeOutput($registration['full_name']) ?></td>
                    <td><?= escapeOutput($registration['email']) ?></td>
                    <td><?= escapeOutput($registration['event_title']) ?></td>
                    <td><?= escapeOutput($registration['registered_at']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p>Connection worked, but there are no registrations for this event yet. Import the sample data from schema.sql.</p>
        <?php endif; ?>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
