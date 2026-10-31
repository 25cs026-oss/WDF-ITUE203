# WDF-ITUE203 — StudentHub Practical Portfolio

This private repository contains the original Practical 1 planning submission
and the integrated StudentHub source, database, and test evidence for the
Web Development Frameworks (ITUE203) practical sequence.

## Repository Layout

```text
Practical-1/                 Original planning, sitemap, and starter submission
  studentHUB/                 Original starter pages (retained for comparison)
StudentHub-Portal/            Integrated source for Practicals 1–15
  practicals/                 Standalone Practical 6, 7, and 8 demonstrations
  test-report/                Screenshots and practical/final reports
  README.md                   Complete setup and individual practical guides
  schema.sql                  Re-runnable project schema
  studenthub.sql              Sanitized schema and fictional seed-data dump
```

The integrated project lives in `StudentHub-Portal/` so that the original
Practical 1 submission remains intact. Copy `StudentHub-Portal` into
`C:\xampp\htdocs` and rename it `studentHUB`, or use the matching folder name in
the local URL.

## Practical Index

| Practical | Topic | Source and evidence | Supplied report |
|---|---|---|---|
| 1 | Project initiation, requirements, sitemap, wireframe, GitHub | [`Practical-1/`](./Practical-1/) | Included in the original folder |
| 2 | Semantic HTML5 pages | [`StudentHub-Portal/home.html`](./StudentHub-Portal/home.html), [`pages/`](./StudentHub-Portal/pages/) | No separate PDF supplied |
| 3 | Responsive CSS Grid, Flexbox, and Bootstrap | [`css/`](./StudentHub-Portal/css/), [`README.md`](./StudentHub-Portal/README.md) | [PDF](./Practical-Reports/25CS026_WDF_Practical_3.pdf) |
| 4 | DOM, events, localStorage, and UI interaction | [`js/ui.js`](./StudentHub-Portal/js/ui.js), Practical 4 guide in project README | [PDF](./Practical-Reports/25CS026_WDF_Practical_4.pdf) |
| 5 | Registration form and client-side validation | [`pages/register.html`](./StudentHub-Portal/pages/register.html), [`js/auth.js`](./StudentHub-Portal/js/auth.js) | [PDF](./Practical-Reports/25CS026_WDF_Practical_5.pdf) |
| 6 | Fetch three JSON data sets, search, filter, sort, and paginate | [`practicals/06-fetch-search-filter/`](./StudentHub-Portal/practicals/06-fetch-search-filter/) | [PDF](./Practical-Reports/25CS026_WDF_Practical_6.pdf) |
| 7 | PHP validation and JSON/CSV file storage | [`practicals/07-php-json-storage/`](./StudentHub-Portal/practicals/07-php-json-storage/) | [PDF](./Practical-Reports/25CS026_WDF_Practical_7.pdf) |
| 8 | Express CRUD API, shared MySQL schema, and PDO prepared-query companion | [`practicals/08-node-express-api/`](./StudentHub-Portal/practicals/08-node-express-api/), [`schema.sql`](./StudentHub-Portal/schema.sql) | [PDF](./Practical-Reports/25CS026_WDF_Practical_8.pdf) |
| 9 | Secure registration, duplicates, password hashing | [`process.php`](./StudentHub-Portal/process.php), [`user_registration.sql`](./StudentHub-Portal/user_registration.sql) | [PDF](./Practical-Reports/25CS026_WDF_Practical_9.pdf) |
| 10 | Login/logout, sessions, roles, timeout | [`authenticate.php`](./StudentHub-Portal/authenticate.php), [`auth_helpers.php`](./StudentHub-Portal/auth_helpers.php) | [PDF](./Practical-Reports/25CS026_WDF_Practical_10.pdf) |
| 11 | Student CRUD, search, filter, CSV | [`admin_dashboard.php`](./StudentHub-Portal/admin_dashboard.php), [report](./StudentHub-Portal/test-report/TEST-REPORT.md) | [PDF](./Practical-Reports/25CS026_WDF_Practical_11.pdf) |
| 12 | Event CRUD and validated poster upload | [`event_management.php`](./StudentHub-Portal/event_management.php), [report](./StudentHub-Portal/test-report/events/TEST-REPORT.md) | [PDF](./Practical-Reports/25CS026_WDF_Practical_12.pdf) |
| 13 | PHP JSON API and Fetch CRUD | [`api.php`](./StudentHub-Portal/api.php), [`script.js`](./StudentHub-Portal/script.js), [report](./StudentHub-Portal/test-report/fetch-api/TEST-REPORT.md) | [PDF](./Practical-Reports/25CS026_WDF_Practical_13.pdf) |
| 14 | Admin dashboard, RBAC, dynamic menu, audit log | [`dashboard.php`](./StudentHub-Portal/dashboard.php), [`audit_logs.php`](./StudentHub-Portal/audit_logs.php), [report](./StudentHub-Portal/test-report/dashboard/TEST-REPORT.md) | [PDF](./Practical-Reports/25CS026_WDF_Practical_14.pdf) |
| 15 | Integration, tests, documentation, GitHub submission, local deployment | [final submission report](./StudentHub-Portal/test-report/FINAL-SUBMISSION-REPORT.md), [setup guide](./StudentHub-Portal/README.md) | Final report in StudentHub-Portal |

### Practical Report Files

The original Practical 1 materials are retained in `Practical-1/`. The supplied
Practical 3–14 reports are stored in `Practical-Reports/`; Practical 2 is
represented by the semantic HTML pages and the project guide, and Practical 15
is documented in the final submission report. Practical 8's PDF mixes an
Express REST API aim/result with PHP/MySQL file requirements. The submitted
source addresses both sets, with the API in its own folder and the PDO
connection/query example alongside it.

## Run the Integrated PHP Project

1. Install XAMPP and start Apache and MySQL.
2. Back up any existing database before importing [`studenthub.sql`](./StudentHub-Portal/studenthub.sql). The sanitized dump recreates the project tables and seeds fictional demo accounts, students, events, and registrations.
3. Configure the database environment variables if your local MySQL credentials differ from the documented XAMPP defaults.
4. Open `http://localhost/studentHUB/home.html` and sign in at `http://localhost/studentHUB/login.php`.

Fictional local demonstration accounts:

- Admin: `admin.demo@example.test` / `AdminDemo9!`
- Student: `student.demo@example.test` / `StudentDemo9!`

Do not use these demonstration credentials in a deployed application.

## GitHub

Repository: [25cs026-oss/WDF-ITUE203](https://github.com/25cs026-oss/WDF-ITUE203)
