<?php

$events = [];
$errorMessage = '';

try {
    require __DIR__ . '/db.php';
    $statement = $pdo->prepare(
        'SELECT e.event_id, e.title, e.event_date, e.location, COUNT(r.registration_id) AS registration_count
         FROM events e
         LEFT JOIN registrations r ON r.event_id = e.event_id
         WHERE e.event_id >= :minimum_id
         GROUP BY e.event_id, e.title, e.event_date, e.location
         ORDER BY e.event_date ASC'
    );
    $statement->bindValue(':minimum_id', 1, PDO::PARAM_INT);
    $statement->execute();
    $events = $statement->fetchAll();
} catch (RuntimeException | PDOException $exception) {
    error_log('StudentHub Practical 8 prepared query failed: ' . $exception->getMessage());
    http_response_code(500);
    $errorMessage = $exception instanceof RuntimeException
        ? $exception->getMessage()
        : 'The sample records could not be loaded.';
}

function escapePractical8($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Practical 8: PDO Connection Test - StudentHub</title>
  <link rel="stylesheet" href="../../../css/common.css">
</head>
<body>
  <main class="auth-container">
    <header class="page-title">
      <p>Practical 8</p>
      <h1>MySQL PDO Connection Test</h1>
      <p>This PHP companion verifies the normalized schema using a prepared query.</p>
    </header>
    <section class="auth-card" aria-labelledby="connection-title">
      <h2 id="connection-title">Database connection</h2>
      <?php if ($errorMessage !== ''): ?>
        <p class="form-message error-message" role="alert"><?= escapePractical8($errorMessage) ?></p>
      <?php else: ?>
        <p class="form-message success-message" role="status">PDO connection and prepared query succeeded.</p>
        <div class="table-wrapper">
          <table>
            <caption>Events and registration counts</caption>
            <thead><tr><th scope="col">Event</th><th scope="col">Date</th><th scope="col">Location</th><th scope="col">Registrations</th></tr></thead>
            <tbody>
              <?php foreach ($events as $event): ?>
                <tr>
                  <td><?= escapePractical8($event['title']) ?></td>
                  <td><?= escapePractical8($event['event_date']) ?></td>
                  <td><?= escapePractical8($event['location']) ?></td>
                  <td><?= escapePractical8($event['registration_count']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$events): ?><tr><td colspan="4">No event records are available.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
