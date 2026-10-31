# StudentHub Final Practical Submission Report

## Submission Checklist

| Item | Status | Deliverable |
|---|---|---|
| Integrated source code / GitHub repository | Source is organized for the private course repository. | [StudentHub Portal](https://github.com/25cs026-oss/WDF-ITUE203/tree/main/StudentHub-Portal); see the repository root README for the practical index. |
| Database dump | Ready; schema and fictional starter records only. Current development account data and audit history are excluded. | [studenthub.sql](../studenthub.sql) |
| Setup and testing instructions | Ready; includes XAMPP, import, demo logins, and practical steps. | [README.md](../README.md) |
| Test, security, and accessibility report | This report and the linked practical test reports. | Checklist below and the practical reports under `test-report/`. |
| Integrated UI and authentication proof | Full-page screenshots of the public interface, login, and authenticated Admin/Student views. | [01-integrated-home-full-page.png](./final-submission/01-integrated-home-full-page.png), [02-login-full-page.png](./final-submission/02-login-full-page.png), [03-admin-dashboard-full-page.png](./final-submission/03-admin-dashboard-full-page.png), [04-student-dashboard-full-page.png](./final-submission/04-student-dashboard-full-page.png) |

## Functional Test Summary

| Area | Result | Detailed evidence |
|---|---|---|
| Fetch JSON data explorer | Pass — 15 records each for students/events/FAQs; search, course/category filter, ascending/descending sort, pagination, loading, and simulated HTTP 404 error state | [Practical 6 source and guide](../practicals/06-fetch-search-filter/README.md) |
| PHP form validation and file storage | Pass — valid POST, duplicate email rejection, invalid-name server validation, locked JSON persistence, and CSV response headers/content | [Practical 7 source and guide](../practicals/07-php-json-storage/README.md) |
| Express REST API and database access | Pass — endpoint tests plus live MySQL GET/POST/PUT/DELETE/404; test row removed; PDO prepared-query companion returned HTTP 200 | [Practical 8 source and guide](../practicals/08-node-express-api/README.md) |
| Student account administration | Pass — table, create, update, search, course filter, soft delete, messages, and CSV export | [Student Records report](./TEST-REPORT.md) |
| Event administration | Pass — create/update/delete, poster display, invalid type and size handling, and Student denial | [Event Management report](./events/TEST-REPORT.md) |
| Student JSON API | Pass — list/create/update/delete, validation, error/loading states, and Fetch status evidence | [Fetch API report](./fetch-api/TEST-REPORT.md) |
| Dynamic Admin dashboard and role-specific navigation | Pass — live user/student/event counts, latest registrations, Student menu | [Dashboard/RBAC report](./dashboard/TEST-REPORT.md) |
| Audit logging | Pass — student create/update/deactivate and event update entries retained after the temporary test account was removed | [Audit-log screenshot](./dashboard/02-audit-logs-full-page.png) |
| Admin-only route protection | Pass — Student session received HTTP 403 from `admin_dashboard.php`, `event_management.php`, and `audit_logs.php` | [403 screenshot](./dashboard/04-non-admin-403-full-page.png) |

## Security Checklist

| Control | Result / evidence |
|---|---|
| Password handling | Password hashes are verified with `password_verify()`; demo account passwords are stored as hashes in the SQL dump. |
| Session management | Strict session mode, HttpOnly and SameSite=Lax cookies, session ID regeneration on login, and a 15-minute inactivity timeout are implemented in the existing authentication flow. |
| Role-based authorization | Admin routes perform server-side role checks; Student attempts returned HTTP 403 in browser testing. Hiding a link is not used as the authorization boundary. |
| CSRF defenses | Session-bound tokens are checked for student/event writes, API write requests, and logout. |
| Database access | User-controlled values in the reviewed CRUD/dashboard flows use prepared MySQLi statements. |
| Practical 8 API | Express routes use parameterized SQL, validate IDs and fields, handle duplicate emails, and return generic server errors; the PDO companion disables emulated prepares. |
| File-based Practical 7 storage | Writes are protected by exclusive locks, reads use shared locks, output is escaped, and generated JSON is blocked from direct web access and excluded from Git. |
| Output encoding | Dynamic PHP-rendered user/event/audit values are HTML-escaped. |
| Poster uploads | Server checks MIME and image contents, allows JPEG/PNG/WebP, limits files to 2 MB, assigns random names, and denies script-like extensions in the upload folder. Invalid type and oversize tests were rejected. |
| Audit integrity | Successful student/event changes and their audit records are committed in the same database transaction. |
| SQL dump privacy | The delivered dump excludes the live development account rows and test audit history; it includes only fictional demo fixtures. Import it only into an empty database or after backing up existing data. |
| External security assessment | Not performed. This is a practical-level security checklist, not a penetration test or a claim of production certification. |

### Deployment Follow-ups

- Serve the app over HTTPS in production; the local XAMPP demonstration uses HTTP.
- Replace the empty-password local MySQL defaults with a dedicated database user and strong secret configuration.
- Review production security headers, especially a Content Security Policy compatible with the Bootstrap CDN.
- Restrict demo credentials to local/testing use and change or remove them before deployment.
- Maintain backups and apply least-privilege database permissions.

## Accessibility Checklist

| Check | Result |
|---|---|
| Page titles, primary headings, and semantic main/navigation landmarks | Checked on the integrated login and dashboard views. |
| Form fields have visible labels; primary buttons and links have readable names | Checked in the PHP forms and captured role views. |
| Tables have header cells and responsive overflow containers where used | Checked in student, event, dashboard, and audit table views. |
| Status/error content is rendered as readable text, not color alone | Checked in the tested success/error flows. |
| Uploaded posters include descriptive alternative text | Checked in the event-list template. |
| Narrow-screen layout | Checked at 390 px for the integrated home, login, Admin dashboard, and Student Records; no document-level horizontal overflow was observed. |
| Automated WCAG/axe scan, measured contrast, and assistive-technology test | Not performed; these remain recommended before making a formal WCAG conformance claim. |

## Database Dump Notes

`studenthub.sql` was generated from the current database structure without
exporting its live rows, then supplied with fictional demonstration data for the
Admin/Student sign-ins, sample students, events, and registrations. The audit
log and Fetch API tables start empty. The SQL dump recreates the listed tables,
so back up an existing database before import.

Demo sign-ins:

- Admin: `admin.demo@example.test` / `AdminDemo9!`
- Student: `student.demo@example.test` / `StudentDemo9!`

These are fictional local-test credentials. Do not reuse them outside the
demonstration environment.

## Practical PDFs

The supplied Practical 3–15 PDF reports are retained in the repository's
`Practical-Reports/` folder, including the [Practical 15 final submission PDF](../../Practical-Reports/25CS026_WDF_Practical_15.pdf).
The Practical 15 PDF states that the GitHub repository is private, matching
the requested repository. Its listed demo login credentials match the
password hashes for the fictional sample accounts in the SQL dump. Practical
1 planning/source files are in `Practical-1/`; Practical 2 is represented by
the semantic site pages and project guide. The Practical 8 PDF contains
conflicting Express and PHP/MySQL requirements; the implementation and README
document how both sets are covered.
