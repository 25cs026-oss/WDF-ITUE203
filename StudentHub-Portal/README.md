# StudentHub – Responsive Academic Portal

## 1. Project Overview

StudentHub is a multi-page academic portal created for Web Development Practical 3. It demonstrates semantic HTML5, CSS3, Flexbox, CSS Grid, media queries, Bootstrap 5, and mobile-first responsive design.

## 2. Practical Information

- **Practical Number:** Practical 3
- **Practical Title:** Responsive UI Design using CSS Grid, Flexbox, and Bootstrap
- **Course Outcome:** CO2
- **Technology Used:** HTML5, CSS3, Bootstrap 5, Browser Developer Tools

## 3. Objectives

- Create responsive web pages.
- Understand Flexbox and CSS Grid.
- Use Bootstrap components.
- Design for mobile, tablet, and desktop devices.
- Maintain consistent website styling.
- Organize styles in external CSS files.

## 4. Pages Included

| Page | Filename | Main Technique | Purpose |
|---|---|---|---|
| Home | `home.html` | Flexbox | Introduction and academic quick links |
| About | `pages/about.html` | Flexbox | Purpose, features, mission, and benefits |
| Registration | `pages/register.html` | CSS Grid | Responsive student form demonstration |
| Dashboard | `pages/student/dashboard.html` | CSS Grid | Academic summary and student navigation |
| Faculty | `pages/student/faculty.html` | CSS Grid | Responsive faculty information cards |
| Events | `pages/events.html` | Bootstrap 5 | View sample college events by status |
| Other student pages | `pages/student/*.html` | Common CSS | Academic information and student services |

## 5. Project Folder Structure

```text
studentHUB/
├── home.html
├── README.md
├── css/
│   ├── common.css
│   ├── flexbox.css
│   └── grid.css
└── pages/
    ├── about.html
    ├── contact-help.html
    ├── events.html
    ├── features.html
    ├── login.html
    ├── register.html
    └── student/
        ├── assignments.html
        ├── attendance-tracking.html
        ├── courses.html
        ├── dashboard.html
        ├── faculty.html
        ├── notices.html
        ├── profile.html
        ├── resources.html
        ├── results.html
        └── timetable.html
```

## 6. Technologies Used

- **HTML5:** Provides page structure and semantic elements.
- **CSS3:** Provides colours, spacing, typography, responsive layout, and media queries.
- **Flexbox:** Organizes navigation, hero content, quick links, and About page cards.
- **CSS Grid:** Organizes dashboard cards, academic summaries, faculty cards, and the registration form.
- **Bootstrap 5:** Builds the responsive Events page with ready-made components.

## 7. Responsive Design

The website follows a mobile-first approach. Base styles support small screens, and media queries improve the layouts when more space is available.

- **Mobile:** Below 600px, mainly one-column layouts.
- **Tablet:** 600px to 991px, mainly two-column layouts.
- **Desktop:** 992px and above, three or four columns where suitable.

Navigation wraps or collapses on smaller screens, and wide tables can scroll horizontally.

## 8. Page Descriptions

- **Home:** Introduces StudentHub and provides responsive academic quick links.
- **About:** Explains the portal purpose, features, mission, and student benefits.
- **Registration:** Demonstrates a responsive two-column CSS Grid form.
- **Student Dashboard:** Shows student details, academic summary cards, navigation, announcements, and search.
- **Faculty:** Displays four sample faculty profiles as responsive Grid cards.
- **Events:** Uses Bootstrap to group fictional events into upcoming, running, and past sections.
- **Courses:** Lists enrolled subjects and course details.
- **Assignments:** Shows current work, deadlines, marks, and a sample submission form.
- **Attendance:** Displays overall and subject-wise attendance.
- **Results:** Presents semester grades and overall performance.
- **Timetable:** Shows the weekly class schedule.
- **Notices:** Lists academic announcements.
- **Resources:** Describes available learning materials.
- **Profile:** Displays personal and academic student information.

## 9. Events Page Status

Events are divided into Upcoming Events, Currently Running Events, and Past Events.

**The event information in this project is fictional and is included only for academic demonstration.** There is no event registration feature.

## 10. Faculty Information Disclaimer

**The faculty contact details shown in this project are fictional and created only for practical demonstration. They are not official contact details.**

## 11. How to Run the Project

1. Download or copy the project folder.
2. Open the folder in Visual Studio Code.
3. Install the Live Server extension.
4. Right-click `home.html`.
5. Select **Open with Live Server**.
6. Use the navigation links to visit the other pages.

Internet access is required to load the Bootstrap CDN on the Events page.

## Final Submission: Integrated PHP Portal

The integrated PHP features require XAMPP Apache and MySQL; Live Server alone
does not execute PHP.

1. Back up any existing StudentHub database before importing
   [`studenthub.sql`](./studenthub.sql). The sanitized dump drops and recreates
   the project tables, then seeds only fictional demo accounts, students,
   events, and registrations.
2. Start Apache and MySQL in XAMPP, then import the dump with phpMyAdmin or:

   ```powershell
   cmd.exe /c "C:\xampp\mysql\bin\mysql.exe -u root < studenthub.sql"
   ```

3. Configure `STUDENTHUB_DB_HOST`, `STUDENTHUB_DB_NAME`,
   `STUDENTHUB_DB_USER`, and `STUDENTHUB_DB_PASSWORD` for Apache as needed.
   The local default is `127.0.0.1`, database `studenthub_practical8`, user
   `root`, and an empty password; use secure credentials outside a local lab.
4. Open `http://localhost/studentHUB/home.html` for the integrated public UI,
   or `http://localhost/studentHUB/login.php` for PHP authentication.
5. Fictional demo sign-ins:
   - Admin: `admin.demo@example.test` / `AdminDemo9!`
   - Student: `student.demo@example.test` / `StudentDemo9!`
6. See [`test-report/FINAL-SUBMISSION-REPORT.md`](./test-report/FINAL-SUBMISSION-REPORT.md)
   for test, security, accessibility, and full-page screenshot evidence.

The integrated source and practical reports are published in the private
[WDF-ITUE203 repository](https://github.com/25cs026-oss/WDF-ITUE203). The
repository root README contains the index for Practicals 1–15.

## 12. How to Test Responsiveness

1. Open the website in Google Chrome.
2. Press `F12`.
3. Click **Toggle Device Toolbar**.
4. Test these sizes:
   - Mobile: 375 × 667
   - Tablet: 768 × 1024
   - Desktop: 1366 × 768
5. Check that cards, navigation, forms, and tables adjust correctly.

## 13. Screenshots Required

- **CSS Grid:** Dashboard mobile, tablet, and desktop.
- **Flexbox:** Home mobile, tablet, and desktop.
- **Bootstrap:** Events mobile, tablet, and desktop.

## 14. CSS Organization

- **common.css:** Shared typography, colours, buttons, forms, tables, headers, footers, and content cards.
- **flexbox.css:** Home, About, navigation, quick links, and other Flexbox layouts.
- **grid.css:** Dashboard, academic summary, faculty, and registration Grid layouts.

Inline CSS is avoided.

## 15. Key Features

- Multi-page academic portal
- Responsive navigation and mobile-first design
- Dashboard and academic summary cards
- Responsive forms and faculty cards
- Upcoming, running, and past events
- Responsive tables
- Simple and consistent interface
- Accessible labels and semantic elements

## 16. Accessibility Features

- Semantic HTML elements
- Labels for form fields
- `aria-label` for navigation
- `aria-current` for current pages
- Readable colour contrast
- Visible keyboard focus styles
- Meaningful link text
- Responsive text and spacing
- Buttons for interactive controls, with expanded/pressed states exposed to assistive technology
- Modal can be closed with Escape and returns keyboard focus to its trigger
- Reduced-motion preference is respected

## 17. Known Limitations

- Forms do not save data.
- Search does not perform a real search.
- Login is not connected to a database.
- Event filters are visual links only.
- Event and faculty information is sample data.
- The project is front-end only.

## 18. Viva Questions and Answers

1. **What is responsive web design?** It makes a website adjust to different screen sizes.
2. **What is mobile-first design?** It starts with styles for small screens, then adds styles for larger screens.
3. **What is Flexbox?** A CSS layout method mainly used to arrange items in one direction.
4. **What is CSS Grid?** A CSS layout method used to arrange content in rows and columns.
5. **What is the difference between Grid and Flexbox?** Grid is two-dimensional; Flexbox is mainly one-dimensional.
6. **What is a media query?** A CSS rule that applies styles at selected screen sizes.
7. **Why is Bootstrap used?** It provides responsive components and utility classes quickly.
8. **What are Bootstrap breakpoints?** Screen-width points where Bootstrap layouts change.
9. **Why should inline CSS be avoided?** External CSS is easier to reuse and maintain.
10. **What is the viewport meta tag?** It tells the browser how to scale a page on mobile devices.
11. **Why are external CSS files used?** They keep styling organized and reusable across pages.
12. **How are responsive tables handled?** Wide tables use horizontal scrolling on smaller screens.

## 19. Submission Checklist

- [ ] HTML files completed
- [ ] CSS files completed
- [ ] Flexbox implemented
- [ ] CSS Grid implemented
- [ ] Bootstrap implemented
- [ ] Mobile screenshots captured
- [ ] Tablet screenshots captured
- [ ] Desktop screenshots captured
- [ ] README added
- [ ] Links tested
- [ ] Folder zipped
- [ ] ZIP uploaded to Microsoft Teams

## 20. Author

- **Name:** Kirtan Kakadiya
- **Project:** StudentHub
- **Practical:** Responsive UI Design using CSS Grid, Flexbox, and Bootstrap
- **Academic Year:** 2026–2027

## Authentication Demonstration

StudentHub includes a **front-end login simulation** for academic demonstration. It uses `localStorage` for its sample account and `sessionStorage` for the active login state. The Practical 9 registration form is handled separately by `process.php` and requires a PHP web server and MySQL.

The login simulation is not connected to the Practical 9 MySQL account table and does not provide real authentication. Practical 9 stores a password hash in MySQL, never the submitted plain-text password. This is still a small academic example, not a production-ready account system.

## Registration Flow

1. A new student opens Register.
2. JavaScript checks the form and shows inline validation messages.
3. The form sends its fields to `process.php` using the POST method.
4. PHP validates the fields again, hashes the password, and inserts the account into MySQL.
5. The result page displays a success or error message.

Keep real student information out of this demonstration project.

## Practical 6 and 7: Fetch JSON and PHP File Storage

The standalone Practical 6 data explorer fetches 15+ fictional records from
`events.json`, `students.json`, and `faqs.json`, with search, filtering,
sorting, pagination, and loading/error states. Run it through Apache at
`http://localhost/studentHUB/practicals/06-fetch-search-filter/index.html`.
See [`practicals/06-fetch-search-filter/README.md`](./practicals/06-fetch-search-filter/README.md).

Practical 7 provides a POST registration form with server validation, duplicate
checks, JSON file storage, and CSV export at
`http://localhost/studentHUB/practicals/07-php-json-storage/index.php`. Its
generated JSON file is local runtime data and is intentionally not committed.
See [`practicals/07-php-json-storage/README.md`](./practicals/07-php-json-storage/README.md).

## Practical 8: REST API and Database Connectivity

The supplied Practical 8 PDF is internally inconsistent: its aim/result call
for a Node.js/Express CRUD API, while its requirements list a MySQL schema and
PHP PDO connection/query files. The submission addresses both: a separate
Express API with GET/POST/PUT/DELETE student routes, and a PHP PDO prepared
query companion. See
[`practicals/08-node-express-api/README.md`](./practicals/08-node-express-api/README.md)
for API routes and run instructions.

### Shared MySQL schema and legacy MySQLi connectivity

The project schema includes normalized student, event, and registration
tables, as well as the additional account and audit-log tables used by later
practicals. Import it before starting the API.

### Run it with XAMPP

1. Copy `studentHUB` into `C:\xampp\htdocs` and start **Apache** and **MySQL**.
2. Open `http://localhost/phpmyadmin` and choose **Import**.
3. Select `C:\xampp\htdocs\studentHUB\schema.sql` and click **Import** or **Go**.
4. Open `http://localhost/studentHUB/practicals/08-node-express-api/php/test_connection.php`.
5. Confirm the PDO success message and sample event/registration rows appear.

`db.php` defaults to XAMPP's local MySQL settings (user `root`, no password).
For another setup, set `STUDENTHUB_DB_HOST`, `STUDENTHUB_DB_NAME`,
`STUDENTHUB_DB_USER`, and `STUDENTHUB_DB_PASSWORD` in the PHP server environment.
Do not use the default local credentials on an internet-facing server.

### Database connectivity screenshot

Open the test page after importing `schema.sql`. Press **Win + Shift + S** and
select the browser area showing the green successful connection message and the
registration table. Save the screenshot for submission.

## Practical 9: Registration Validation and Secure MySQL Storage

The registration form includes required-field checks, email validation, username
and email duplicate checks, password confirmation, server-side validation, and
password hashing with PHP `password_hash()`. Successful registrations are
inserted into `student_accounts` using a MySQLi prepared statement. The database
also has unique constraints on both username and email.

### Run and test it with XAMPP

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Import or re-import `C:\xampp\htdocs\studentHUB\schema.sql` in phpMyAdmin.
   The import creates the `student_accounts` table in `studenthub_practical8`.
3. Open `http://localhost/studentHUB/pages/register.html` in a browser. Do not
   use Live Server; PHP needs Apache.
4. Submit the empty form to see required-field errors. Try an invalid email,
   then valid-looking account details with mismatched passwords.
5. Register with a made-up username/email and a password that meets the stated
   requirements. The result page should say **Registration successful**.
6. Submit the same email, then (with a different email) the same username, to
   verify duplicate handling.
7. In phpMyAdmin, select `studenthub_practical8`, open `student_accounts`, and
   confirm the account was inserted. The `password_hash` value should look like
   a hash and should not match the password you entered.

### Practical 9 screenshots for submission

Capture these using **Win + Shift + S** and save each image in your submission
folder:

- Empty registration form with labels visible.
- Empty-field, invalid-email, and password-mismatch validation messages.
- Successful registration result page.
- phpMyAdmin `student_accounts` row showing the registered username/email and
  `password_hash` column (use only made-up demo data).
- Optional: duplicate email and duplicate username result messages.

Never include a real password in a screenshot. The database view should show
only the stored hash.

## Practical 10: PHP Login, Roles, and Session Security

The role-based login uses `db.php` with MySQLi, verifies stored password hashes
with `password_verify()`, regenerates the session ID after authentication, and
expires sessions after 15 minutes of inactivity. New registrations receive the
Student role by default. Import `user_registration.sql` in phpMyAdmin to add the
role column and two fictional test accounts:

- Admin: `admin.demo@example.test` / `AdminDemo9!`
- Student: `student.demo@example.test` / `StudentDemo9!`

Open `http://localhost/studentHUB/login.php` to test. Student and Admin access
are restricted to their respective dashboards. Logout uses a CSRF-protected
POST form. To capture a timeout demonstration without waiting 15 minutes,
temporarily set the Apache environment variable `STUDENTHUB_SESSION_TIMEOUT`
to a smaller positive number, then restore the normal default after testing.

Capture login success for both roles, invalid credentials, each wrong-role
dashboard attempt, successful logout, dashboard access after logout, session
timeout, and session ID regeneration. The dashboard confirms session ID
regeneration without displaying the ID itself. Never expose the session ID in
screenshots.

### Practical 11: Admin Student Records

The Admin dashboard manages Student-role accounts with validated add and edit
forms, text search, course filtering, CSV export, and a soft-delete action that
changes a student's status to `Inactive`. Deleted records remain visible in the
admin table and are not physically removed. Student actions require a
session-bound CSRF token. CSV cells are protected against spreadsheet formula
execution.

1. Import `schema.sql` (or re-import it to add the `status` column), then import
   `user_registration.sql` for the fictional Admin login.
2. Sign in at `http://localhost/studentHUB/login.php` with
   `admin.demo@example.test` / `AdminDemo9!`.
3. Use **Student Records** to add and edit sample students, search by name,
   username, or email, filter by course, and export the current filtered list.
4. Choose **Delete** and confirm the browser prompt. Verify the record stays in
   the table with `Inactive` status.
5. Capture screenshots for the student table, add/update successes, search,
   course filter, delete confirmation, inactive status, success/error messages,
   and the exported `student_records.csv` opened in Excel. Use fictional data
   only.
6. See [`test-report/TEST-REPORT.md`](./test-report/TEST-REPORT.md) for a
   completed local test report and its screenshots.

### Practical 12: Admin Event Management and Posters

The role-protected Event Management page supports creating, updating, and
deleting events. New events require a JPEG, PNG, or WebP poster (maximum 2 MB).
The server validates the uploaded image type and content, stores it under a
generated filename, and renders uploaded posters in the event list. Import
`schema.sql` to add the poster and update columns to an existing `events` table.

1. Sign in as the fictional Admin at `http://localhost/studentHUB/login.php`,
   then choose **Manage Events** on the Admin dashboard.
2. Create an event with a valid poster. Edit the event to verify updates, then
   delete the test event and confirm the success message.
3. Try a non-image file and an image larger than 2 MB. Both uploads should be
   rejected with an error message.
4. Sign in as the fictional Student and open
   `http://localhost/studentHUB/event_management.php`. The request should be
   denied.
5. Evidence and screenshots are in
   [`test-report/events/TEST-REPORT.md`](./test-report/events/TEST-REPORT.md).

### Practical 13: Student Records JSON API with Fetch

The Admin-only student records demo at `http://localhost/studentHUB/index.html`
uses `script.js` Fetch requests to call `api.php`. The API implements JSON
`GET`, `POST`, `PUT`, and `DELETE` operations backed by the `api_students`
table. It validates input, rejects duplicate email addresses, and requires an
Admin session and session-bound CSRF token for write requests. The existing
`db.php` connection settings are reused.

1. Import or re-import `schema.sql` to create the `api_students` table, then
   sign in as the fictional Admin at `http://localhost/studentHUB/login.php`.
2. Open `http://localhost/studentHUB/index.html` and add a fictional student.
   Use **Edit** to update details and **Delete** to remove a record.
3. Use a duplicate email address to verify that the API error is shown; the
   page also displays a loading state while records are fetched.
4. Open browser Developer Tools → **Network**, select **Fetch/XHR**, then
   capture the list request and one successful write with their status codes
   and JSON responses.
5. Capture full-page UI screenshots and the Network requests in the report at
   [`test-report/fetch-api/TEST-REPORT.md`](./test-report/fetch-api/TEST-REPORT.md).

### Practical 14: Bootstrap Dashboard, RBAC, and Audit Logs

The shared `dashboard.php` uses role-aware navigation and displays live Admin
counts for user accounts, student accounts, and events, followed by the latest
event registrations. Student accounts see only Student-facing links and do not
receive Admin statistics or management links. Admin-only routes enforce the
session role on the server and return HTTP 403 to a signed-in Student. Successful
student and event administration actions are written to `audit_logs` in the
same database transaction as the change.

1. Import or re-import `schema.sql` to create the `audit_logs` table.
2. Sign in with the fictional Admin account at
   `http://localhost/studentHUB/login.php`, then open `dashboard.php`.
3. Verify that the statistics and recent event registrations match the current
   database, and use **Student Records**, **Manage Events**, and **Audit Logs**
   from the Admin navigation.
4. Create or update a fictional student/event, then open `audit_logs.php` to
   confirm that the Admin action is recorded. Do not retain unnecessary test
   records after evidence capture.
5. Sign in as the fictional Student and open `dashboard.php` to verify its
   reduced menu. Directly request `admin_dashboard.php`,
   `event_management.php`, or `audit_logs.php`; each Admin-only page must return
   HTTP 403.
6. See [`test-report/dashboard/TEST-REPORT.md`](./test-report/dashboard/TEST-REPORT.md)
   for full-page dashboard, role, access-control, and audit-log screenshots.

## Public Pages

- Home
- About
- Features
- Events
- Contact/Help
- Login
- Register

## Protected Pages

- Dashboard
- Profile
- Courses
- Faculty
- Attendance
- Assignments
- Results
- Timetable
- Notices
- Resources

## Testing the Authentication Flow

1. Open `home.html`.
2. Confirm that no direct student-page links are visible.
3. Click **Login to Access Dashboard**.
4. Try logging in before registration.
5. Confirm that an account-not-found message appears.
6. Open Register and complete the form.
7. Confirm redirection to Dashboard.
8. Visit different protected pages.
9. Click Logout.
10. Manually enter a protected-page URL.
11. Confirm redirection to Login.
12. Log in again using the registered email and password.

## Important Limitation

This system is for educational demonstration only. It must not be used for real personal data or real passwords. It is a front-end simulation, not secure authentication.


## Practical 4: JavaScript DOM Manipulation, Event Handling, and UI Interactivity

This practical extends the existing StudentHub pages. It uses plain JavaScript in
`js/ui.js` and keeps the Bootstrap CSS already used for Practical 3.

### Files updated during review

- `js/ui.js`: feature handlers; banner dismissal, safe modal text updates,
  Escape/outside/close-button dismissal, keyboard focus, slider state and theme controls.
- `css/common.css`: corrected mobile menu rule order, readable dark theme for
  headings/cards/forms/tables, FAQ visibility without a fixed height, modal styling
  that does not conflict with Bootstrap, and reduced-motion support.
- `home.html`: accessible menu controls, slider announcements, FAQ, StudentHub guide
  modal, theme-compatible text styling, and the existing authentication script/message
  for logout feedback.
- `pages/contact-help.html`: menu accessibility attributes and FAQ answer IDs.
- `pages/events.html`: menu attributes and separate event-modal classes.
- `pages/register.html`: shared menu/theme controls, UI script, corrected existing
  form action path to `../process.php` (JavaScript still handles registration).
- `pages/about.html`, `pages/features.html`, `pages/login.html`: shared menu/theme
  controls, navigation IDs and UI script.
- `pages/student/assignments.html`, `pages/student/attendance-tracking.html`,
  `pages/student/courses.html`, `pages/student/dashboard.html`,
  `pages/student/faculty.html`, `pages/student/notices.html`,
  `pages/student/profile.html`, `pages/student/resources.html`,
  `pages/student/results.html`, `pages/student/timetable.html`: shared menu/theme
  controls, navigation IDs and UI script; existing page content and authentication retained.
- `README.md`: this Practical 4 guide.

No project files or folders were created during this review. `auth.js`,
`flexbox.css` and `grid.css` were not changed.

### Browser testing steps

Open `home.html` with VS Code Live Server. Keep the same server address and port
when testing saved preferences. Press F12 and open Console to check for errors.

1. **FAQ:** On Home or Contact/Help, scroll to Frequently Asked Questions. Click a
   question to open it, then another question: only the latest answer stays open.
   Click the open question again to close it. Focus a question with Tab and press
   Enter or Space to check keyboard operation.
2. **Modal:** On Home, click **Open StudentHub Guide**, or open Events and click
   View Details on the Git and GitHub workshop. Close with the X. Reopen and click the shaded area
   outside the dialog. Reopen and press Escape. Tab stays on the close button;
   closing returns focus to the button that opened it.
3. **Slider:** On Home, use Next to cycle through all three announcements and
   return to the first. Previous from the first should show the third.
4. **Banner:** On Home, click the announcement's X. It disappears. Refresh:
   it returns, because banner dismissal is only for the current page load.
5. **Mobile menu:** In F12, enable Toggle Device Toolbar (Ctrl+Shift+M) and set
   the viewport to 375 x 667. Navigation starts closed. Click Menu to open it and
   click again to close it. Test a link while open. At 768px or wider, navigation
   is visible and the menu button is hidden. Repeat on Contact/Help and a student page.
6. **Theme:** Click Dark Mode. Check text, cards, inputs and navigation. Refresh
   and navigate to another page: dark mode remains selected. Close and reopen the
   page using the same browser profile and server address: it should remain dark.
   Click Light Mode and refresh to confirm that light mode also persists.

The login simulation is separate from PHP registration, so a saved JSON
registration will not create a login account. Check the login page's
account-not-found validation and confirm that protected student pages redirect
to Login when there is no active browser session.

### Screenshots for submission

- Home in light mode showing the announcement and slider controls.
- Home showing a different slide after Next is clicked.
- Home after the notification is closed.
- Home with the StudentHub Guide modal open.
- Contact/Help with one FAQ answer expanded.
- Events with the event details modal open.
- Home at mobile width with Menu open (and optionally closed).
- Home in dark mode after a refresh, showing that the theme persists.
- Optional: `ui.js` showing event listeners, classList and localStorage usage.

### Simple viva points

- **DOM:** The browser represents HTML as objects; JavaScript can find and change them.
- **querySelector:** Finds the first element matching a CSS selector.
- **querySelectorAll:** Finds all matching elements, which we can loop through.
- **addEventListener:** Runs a function when an event, such as a click, happens.
- **classList.add/remove/toggle:** Adds, removes or switches a CSS class on an element.
- **textContent:** Changes plain text without interpreting it as HTML.
- **DOMContentLoaded:** Runs setup after the HTML has been parsed.
- **FAQ:** Clicking a question removes open from all items, then opens the selected one.
- **Slider:** A variable stores the current slide index; % wraps it at the ends.
- **Modal:** A show class displays the overlay; removing it hides the overlay.
- **event.target:** Identifies the clicked element; used to detect an outside-modal click.
- **Media query:** Applies the mobile navigation rules below 768px.
- **localStorage:** Keeps the theme across refreshes and browser sessions.
  getItem reads a value and setItem saves a string value.
- **sessionStorage:** Used by the existing login demo for the current tab session.
- **aria-expanded:** Tells assistive technology whether a menu or answer is open.
- **const vs let:** const prevents reassignment; let is used for changing values like the slide index.

### Review verification

Real Chrome browser checks passed for all six features, FAQ keyboard interaction,
modal focus/dismissal, theme persistence on refresh and navigation, and the
registration/login/logout/access-guard flow. Main content pages were checked at
375px, 768px and 1366px with no horizontal page overflow. All 17 HTML pages have
valid local link, script, stylesheet and form-action paths. No JavaScript console
errors occurred in the tested flows. Tests used an isolated browser profile.

## Practical 5: Registration Form Validation

The Register page (`pages/register.html`) demonstrates HTML5 form controls and
JavaScript validation. It includes name, email, mobile, course, year, password,
confirm password, gender, and terms fields. Name, email, mobile, and password
are checked with regular expressions. Field errors are shown beside the matching
control, and the password strength meter updates while typing.

### How to test

PHP does not run through VS Code Live Server. For a working submission, install
XAMPP, copy the `studentHUB` folder into `C:\xampp\htdocs`, start Apache from
the XAMPP Control Panel, then open
`http://localhost/studentHUB/pages/register.html`.

Submit the empty form to see the inline validation messages. Try an invalid
email or mobile number, mismatched passwords, and unchecked terms. Correct the
fields and submit a demo registration to see the PHP success message. For the
Practical 9 MySQL result, use the registration steps above and verify the
`student_accounts` row in phpMyAdmin.

### Screenshots for submission

- Complete registration form before entering data.
- Valid form filled in before selecting Register.
- Invalid name, email, and mobile with their inline error messages.
- Password requirements and a weak/strong password strength state.
- Confirm password mismatch error.
- Terms acceptance error when the checkbox is unchecked.
- PHP validation error message.
- PHP successful registration message.
- phpMyAdmin showing the Practical 9 account row and password hash.

For screenshots on Windows, press **Win + Shift + S**, drag around the browser
or JSON editor, then paste into Paint or the document used for submission and
save the image. Use only made-up demo details; do not capture a real password.
