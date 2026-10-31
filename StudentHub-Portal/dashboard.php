<?php
require __DIR__ . '/auth_helpers.php';
requireAuthenticatedUser();

$role = $_SESSION['role'];
if (!in_array($role, ['Admin', 'Student'], true)) {
    http_response_code(403);
    renderAuthPage('Unauthorized Access', 'Your account does not have a supported dashboard role.', false);
    exit;
}

$dashboardError = '';
$stats = null;
$latestRegistrations = [];

try {
    require __DIR__ . '/db.php';

    if ($role === 'Admin') {
        $statsResult = $mysqli->query(
            'SELECT
                (SELECT COUNT(*) FROM student_accounts) AS user_count,
                (SELECT COUNT(*) FROM student_accounts WHERE role = \'Student\') AS student_count,
                (SELECT COUNT(*) FROM events) AS event_count'
        );
        $stats = $statsResult->fetch_assoc();
        $statsResult->free();

        $registrations = $mysqli->query(
            'SELECT r.registration_id, s.full_name AS student_name, e.title AS event_title, r.registered_at
             FROM registrations r
             INNER JOIN students s ON s.student_id = r.student_id
             INNER JOIN events e ON e.event_id = r.event_id
             ORDER BY r.registered_at DESC, r.registration_id DESC
             LIMIT 6'
        );
        $latestRegistrations = $registrations->fetch_all(MYSQLI_ASSOC);
        $registrations->free();
    }
} catch (RuntimeException | mysqli_sql_exception $exception) {
    error_log('StudentHub dashboard query failed: ' . $exception->getMessage());
    http_response_code(500);
    $dashboardError = 'Dashboard data is temporarily unavailable. Check the database connection and try again.';
}

$adminMenu = [
    ['label' => 'Overview', 'href' => 'dashboard.php'],
    ['label' => 'Student Records', 'href' => 'admin_dashboard.php'],
    ['label' => 'Manage Events', 'href' => 'event_management.php'],
    ['label' => 'Audit Logs', 'href' => 'audit_logs.php'],
];
$studentMenu = [
    ['label' => 'Overview', 'href' => 'dashboard.php'],
    ['label' => 'Student Home', 'href' => 'student_dashboard.php'],
    ['label' => 'Events', 'href' => 'pages/events.html'],
];
$menu = $role === 'Admin' ? $adminMenu : $studentMenu;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= escapeAuthHtml($role) ?> Dashboard - StudentHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg-body-tertiary">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="dashboard.php">StudentHub</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardMenu" aria-controls="dashboardMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="dashboardMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <?php foreach ($menu as $item): ?>
            <li class="nav-item">
              <a class="nav-link <?= $item['href'] === 'dashboard.php' ? 'active' : '' ?>" href="<?= escapeAuthHtml($item['href']) ?>"><?= escapeAuthHtml($item['label']) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
        <span class="navbar-text me-lg-3"><?= escapeAuthHtml($_SESSION['full_name']) ?> · <?= escapeAuthHtml($role) ?></span>
        <form method="post" action="logout.php">
          <input type="hidden" name="csrf_token" value="<?= escapeAuthHtml(logoutCsrfToken()) ?>">
          <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
        </form>
      </div>
    </div>
  </nav>

  <main class="container py-4 py-lg-5">
    <header class="mb-4">
      <p class="text-uppercase text-primary fw-semibold small mb-1"><?= escapeAuthHtml($role) ?> portal</p>
      <h1 class="display-6 fw-bold mb-2">Welcome, <?= escapeAuthHtml($_SESSION['full_name']) ?></h1>
      <p class="text-secondary mb-0"><?= $role === 'Admin' ? 'A live overview of StudentHub accounts, events, and registrations.' : 'Your StudentHub links are tailored to your Student account.' ?></p>
    </header>

    <?php if ($dashboardError !== ''): ?>
      <div class="alert alert-danger" role="alert"><?= escapeAuthHtml($dashboardError) ?></div>
    <?php elseif ($role === 'Admin'): ?>
      <section aria-labelledby="statistics-title" class="mb-4">
        <h2 id="statistics-title" class="h4 mb-3">Dashboard statistics</h2>
        <div class="row g-3">
          <div class="col-12 col-md-4">
            <article class="card h-100 border-0 shadow-sm">
              <div class="card-body">
                <p class="text-secondary mb-2">User accounts</p>
                <p class="display-5 fw-bold text-primary mb-0"><?= number_format((int) $stats['user_count']) ?></p>
              </div>
            </article>
          </div>
          <div class="col-12 col-md-4">
            <article class="card h-100 border-0 shadow-sm">
              <div class="card-body">
                <p class="text-secondary mb-2">Student accounts</p>
                <p class="display-5 fw-bold text-success mb-0"><?= number_format((int) $stats['student_count']) ?></p>
              </div>
            </article>
          </div>
          <div class="col-12 col-md-4">
            <article class="card h-100 border-0 shadow-sm">
              <div class="card-body">
                <p class="text-secondary mb-2">Events</p>
                <p class="display-5 fw-bold text-info mb-0"><?= number_format((int) $stats['event_count']) ?></p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <section class="card border-0 shadow-sm" aria-labelledby="registrations-title">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
          <h2 id="registrations-title" class="h4 mb-0">Latest event registrations</h2>
          <span class="badge text-bg-primary"><?= count($latestRegistrations) ?> latest</span>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th scope="col">Registration</th><th scope="col">Student</th><th scope="col">Event</th><th scope="col">Registered</th></tr>
            </thead>
            <tbody>
              <?php if (!$latestRegistrations): ?>
                <tr><td colspan="4" class="text-center text-secondary py-4">No event registrations yet.</td></tr>
              <?php endif; ?>
              <?php foreach ($latestRegistrations as $registration): ?>
                <tr>
                  <td>#<?= escapeAuthHtml($registration['registration_id']) ?></td>
                  <td><?= escapeAuthHtml($registration['student_name']) ?></td>
                  <td><?= escapeAuthHtml($registration['event_title']) ?></td>
                  <td><?= escapeAuthHtml(date('d M Y, g:i A', strtotime($registration['registered_at']))) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer bg-white text-secondary small">Counts and registrations are read from the live database.</div>
      </section>
    <?php else: ?>
      <section class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h4">Student access</h2>
          <p class="text-secondary mb-3">Use the role-specific navigation to open your student page and view public events. Administrative tools are not available to Student accounts.</p>
          <a class="btn btn-primary" href="student_dashboard.php">Open Student Home</a>
          <a class="btn btn-outline-primary ms-2" href="pages/events.html">Browse Events</a>
        </div>
      </section>
    <?php endif; ?>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
