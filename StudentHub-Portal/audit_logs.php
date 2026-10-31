<?php
require __DIR__ . '/auth_helpers.php';
requireUserRole('Admin');

try {
    require __DIR__ . '/db.php';
    $result = $mysqli->query(
        'SELECT audit_id, actor_name, actor_role, action, entity_type, entity_id, details, created_at
         FROM audit_logs
         ORDER BY created_at DESC, audit_id DESC
         LIMIT 100'
    );
    $auditEntries = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
} catch (RuntimeException | mysqli_sql_exception $exception) {
    error_log('StudentHub audit log query failed: ' . $exception->getMessage());
    http_response_code(500);
    $auditError = 'Audit history is temporarily unavailable. Check the database connection and schema.';
    $auditEntries = [];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Audit Logs - StudentHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg-body-tertiary">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="dashboard.php">StudentHub</a>
      <div class="d-flex align-items-center gap-3">
        <a class="nav-link text-white" href="dashboard.php">Dashboard</a>
        <a class="nav-link text-white" href="admin_dashboard.php">Student Records</a>
        <a class="nav-link text-white" href="event_management.php">Manage Events</a>
        <form method="post" action="logout.php">
          <input type="hidden" name="csrf_token" value="<?= escapeAuthHtml(logoutCsrfToken()) ?>">
          <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
        </form>
      </div>
    </div>
  </nav>
  <main class="container py-4 py-lg-5">
    <header class="mb-4">
      <p class="text-uppercase text-primary fw-semibold small mb-1">Admin-only activity history</p>
      <h1 class="display-6 fw-bold mb-2">Audit Logs</h1>
      <p class="text-secondary mb-0">Most recent successful student and event administration actions.</p>
    </header>

    <?php if (isset($auditError)): ?>
      <div class="alert alert-danger" role="alert"><?= escapeAuthHtml($auditError) ?></div>
    <?php else: ?>
      <section class="card border-0 shadow-sm" aria-labelledby="audit-table-title">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
          <h2 id="audit-table-title" class="h4 mb-0">Recorded actions</h2>
          <span class="badge text-bg-primary"><?= count($auditEntries) ?> entries</span>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Time</th>
                <th scope="col">Admin</th>
                <th scope="col">Action</th>
                <th scope="col">Entity</th>
                <th scope="col">Details</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$auditEntries): ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">No admin actions have been recorded yet.</td></tr>
              <?php endif; ?>
              <?php foreach ($auditEntries as $entry): ?>
                <tr>
                  <td class="text-nowrap"><?= escapeAuthHtml(date('d M Y, g:i:s A', strtotime($entry['created_at']))) ?></td>
                  <td><?= escapeAuthHtml($entry['actor_name']) ?><br><span class="badge text-bg-secondary"><?= escapeAuthHtml($entry['actor_role']) ?></span></td>
                  <td><span class="badge text-bg-primary"><?= escapeAuthHtml($entry['action']) ?></span></td>
                  <td><?= escapeAuthHtml($entry['entity_type']) ?><?= $entry['entity_id'] === null ? '' : ' #' . escapeAuthHtml($entry['entity_id']) ?></td>
                  <td><?= escapeAuthHtml($entry['details']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer bg-white text-secondary small">Displaying up to the latest 100 audit records stored in the audit_logs table.</div>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>
