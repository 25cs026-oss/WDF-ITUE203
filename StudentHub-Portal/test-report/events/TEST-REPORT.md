# Admin Event Management Test Report

## Summary

Admin event management was tested locally in the XAMPP environment. The test
covered Admin access, event creation and update, poster uploads and validation,
event deletion, and denial of access to a Student account. Test event data and
posters were fictional.

## Evidence

| # | Test | Result | Screenshot |
|---|------|--------|------------|
| 1 | Admin login page | Displayed successfully | [01-admin-login.png](./01-admin-login.png) |
| 2 | Admin dashboard | Admin dashboard and Manage Events navigation displayed | [02-admin-dashboard.png](./02-admin-dashboard.png) |
| 3 | Event creation | Created a test event with a valid PNG poster; success message displayed | [03-event-created.png](./03-event-created.png) |
| 4 | Event list and poster | Uploaded poster and event details displayed in the event list | [04-event-list-poster.png](./04-event-list-poster.png) |
| 5 | Event update | Updated title, location, and description; success message displayed | [05-event-updated.png](./05-event-updated.png) |
| 6 | Poster retained after update | Existing poster remained when no replacement was selected | [06-updated-event-poster.png](./06-updated-event-poster.png) |
| 7 | Invalid poster file type | A text file was rejected with an error message | [07-invalid-file-type.png](./07-invalid-file-type.png) |
| 8 | Poster file size limit | A 2 MB + 1 byte image was rejected; the maximum is 2 MB | [08-file-size-limit.png](./08-file-size-limit.png) |
| 9 | Delete confirmation | Confirmation dialog identified the event and warned that deletion cannot be undone | [09-delete-confirmation.png](./09-delete-confirmation.png) |
| 10 | Event deletion | Confirmed deletion; success message displayed and the event was removed | [10-event-deleted.png](./10-event-deleted.png) |
| 11 | Unauthorized access | A Student account received an Unauthorized Access response when opening the Admin event page | [11-unauthorized-access.png](./11-unauthorized-access.png) |

## Results

- Valid JPEG, PNG, and WebP posters are supported; the successful upload test
  used a PNG.
- The server rejected a non-image file and an image exceeding the 2 MB limit
  with visible error messages.
- Event create, update, and delete operations displayed their respective
  success messages.
- After cleanup, the test event and its uploaded poster were removed. The
  original three seeded events remained.
- Admin event management is restricted to Admin users.
