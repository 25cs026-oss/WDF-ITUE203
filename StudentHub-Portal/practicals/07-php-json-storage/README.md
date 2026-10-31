# Practical 7: PHP Validation and JSON File Storage

`index.php` validates a registration form on the server, stores successful
submissions in a locked JSON file, prevents duplicate email addresses, and
provides a CSV download. The generated `storage/registrations.json` is ignored
by Git and is protected from direct web access by `.htaccess`.

## Run and test

1. Start Apache in XAMPP.
2. Open `http://localhost/studentHUB/practicals/07-php-json-storage/index.php`.
3. Submit made-up valid details, then submit the same email again to verify
   duplicate handling. Try a malformed name or email to see server validation.
4. Use **Export registrations as CSV** to download the records.

The form uses POST, a session-bound CSRF token, server-side validation, escaped
output, exclusive file locks for writes, and shared locks for reads. Do not
commit the generated registration file.
