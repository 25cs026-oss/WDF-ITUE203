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

StudentHub includes a **front-end login simulation** for academic demonstration. It uses `localStorage` for its sample account and `sessionStorage` for the active login state. Registration is handled separately by `process.php` and requires a PHP web server.

The login simulation is not connected to the PHP registration file and does not provide real authentication. The PHP form handler validates submissions and stores registration details in `data/registrations.json`. Passwords in that file are stored as hashes, not plain text. This is still a small academic example, not a production-ready account system.

## Registration Flow

1. A new student opens Register.
2. JavaScript checks the form and shows inline validation messages.
3. The form sends its fields to `process.php` using the POST method.
4. PHP validates the fields again, hashes the password, and saves the registration in `data/registrations.json`.
5. The result page displays a success or error message.

The JSON folder includes an Apache access rule to prevent the data file being opened directly through the browser. Keep real student information out of this demonstration project.

## Practical 8: MySQL and PDO

The database practical is separate from the registration JSON demo. It includes a
normalized MySQL schema for students, events, and their registrations, plus a
PDO connection and a prepared query that displays sample registrations.

### Run it with XAMPP

1. Copy `studentHUB` into `C:\xampp\htdocs` and start **Apache** and **MySQL**.
2. Open `http://localhost/phpmyadmin` and choose **Import**.
3. Select `C:\xampp\htdocs\studentHUB\schema.sql` and click **Import** or **Go**.
4. Open `http://localhost/studentHUB/test_connection.php`.
5. Confirm the green success message and sample registration rows appear.

`db.php` defaults to XAMPP's local MySQL settings (user `root`, no password).
For another setup, set `STUDENTHUB_DB_HOST`, `STUDENTHUB_DB_NAME`,
`STUDENTHUB_DB_USER`, and `STUDENTHUB_DB_PASSWORD` in the PHP server environment.
Do not use the default local credentials on an internet-facing server.

### Practical 8 screenshot

Open the test page after importing `schema.sql`. Press **Win + Shift + S** and
select the browser area showing the green successful connection message and the
registration table. Save the screenshot for submission.

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
fields and submit a demo registration to see the PHP success message. Check
`studentHUB/data/registrations.json` in VS Code to confirm it was saved; the
password should appear as a hash.

### Screenshots for submission

- Complete registration form before entering data.
- Valid form filled in before selecting Register.
- Invalid name, email, and mobile with their inline error messages.
- Password requirements and a weak/strong password strength state.
- Confirm password mismatch error.
- Terms acceptance error when the checkbox is unchecked.
- PHP validation error message.
- PHP successful registration message.
- `data/registrations.json` showing the saved demo record and password hash.

For screenshots on Windows, press **Win + Shift + S**, drag around the browser
or JSON editor, then paste into Paint or the document used for submission and
save the image. Use only made-up demo details; do not capture a real password.
