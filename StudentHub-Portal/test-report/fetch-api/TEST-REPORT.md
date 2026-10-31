# Fetch API Student Records Test Report

## Summary

The Admin-only student records page was tested locally with fictional records.
Screenshots were captured as full-page browser renders at desktop dimensions so
the form, table, status message, and actions are not clipped.

## UI Evidence

| Test | Result | Screenshot |
|---|---|---|
| Student list | Existing records and all table actions displayed | [01-list-full-page.png](./01-list-full-page.png) |
| Add | New record created and success message displayed | [02-add-success-full-page.png](./02-add-success-full-page.png) |
| Edit | Student details updated and success message displayed | [03-edit-success-full-page.png](./03-edit-success-full-page.png) |
| Delete | Record deleted and success message displayed | [04-delete-success-full-page.png](./04-delete-success-full-page.png) |
| Error | Duplicate email rejected with a clear error message | [05-error-state-full-page.png](./05-error-state-full-page.png) |
| Loading | Loading indicator shown while the Fetch request is pending | [06-loading-state-full-page.png](./06-loading-state-full-page.png) |

## Network Evidence

The Network screenshot is from the browser's DevTools Network panel. It shows
the API Fetch/XHR requests and their actual HTTP statuses. Create uses
`201 Created`; list, update, and delete use `200 OK`.

| Evidence | Screenshot |
|---|---|
| Fetch/XHR requests and status codes | [07-network-fetch-crud.png](./07-network-fetch-crud.png) |
| JSON body from a successful `PUT` response (`200 OK`) | [08-json-response.png](./08-json-response.png) |

## API Verification

| Request | Expected result | Observed |
|---|---|---|
| `GET api.php` | `200 OK`, JSON student list and CSRF token | Pass |
| `POST api.php` | `201 Created`, JSON success response | Pass |
| `GET api.php?id=...` | `200 OK`, JSON student details | Pass |
| `PUT api.php?id=...` | `200 OK`, JSON update response | Pass |
| `DELETE api.php?id=...` | `200 OK`, JSON delete response | Pass |
| Duplicate email | `409 Conflict`, JSON error message | Pass |
| Missing record delete | `404 Not Found`, JSON error message | Pass |

The API uses prepared MySQL statements, validates request data, checks the
Admin session, and requires a session-bound CSRF token for write operations.
The existing `db.php` connection configuration is reused. The new
`api_students` table keeps this CRUD exercise independent of registrations and
the existing Student account records.

After testing, the fictional records used for the screenshots were removed.
