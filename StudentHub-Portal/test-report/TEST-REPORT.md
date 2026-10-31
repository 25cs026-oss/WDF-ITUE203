# Student Records Test Report

**Date:** 2026-10-31  
**Environment:** Local XAMPP, PHP 8.2, MariaDB, Admin account  
**Result:** All requested student-record workflows passed using fictional test records.

| Test | Result | Evidence |
|---|---|---|
| Student records displayed in a table | Pass | [Screenshot 01](./01-student-table.png) |
| Add a student | Pass | [Screenshot 02](./02-add-success.png) |
| Update student details | Pass | [Screenshot 03](./03-update-success.png) |
| Search by name, username, or email | Pass | [Screenshot 04](./04-search.png) |
| Filter by course | Pass | [Screenshot 05](./05-course-filter.png) |
| Delete confirmation | Pass | [Screenshot 06](./06-delete-confirmation.png) |
| Soft delete changes status to inactive | Pass | [Screenshot 07](./07-soft-delete.png) |
| Success and error messages | Pass | [Success: add](./02-add-success.png), [success: update](./03-update-success.png), [error: duplicate account details](./08-error-message.png) |
| CSV opened in Microsoft Excel | Pass | [Screenshot 09](./09-excel-export.png); [exported CSV](./student_records.csv) |

The export contains the filtered test record, including its inactive status.
Only fictional test records are shown in the evidence. Passwords are not
included in the screenshots or exported CSV.
