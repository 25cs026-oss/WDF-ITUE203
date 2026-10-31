# Practical 14: Dashboard, RBAC, and Audit Logs

## Result

Implemented and tested a Bootstrap-based Admin dashboard with live database
statistics, latest event registrations, role-specific navigation, Admin-only
routes, and a persistent audit trail. Screenshots are full-page browser captures
at desktop dimensions.

## Dashboard and RBAC Evidence

| Requirement | Result | Screenshot |
|---|---|---|
| Dynamic Admin statistics and latest registrations | Pass — counts show 7 user accounts, 6 student accounts, 3 events, and the latest 4 registrations | [01-admin-dashboard-full-page.png](./01-admin-dashboard-full-page.png) |
| Admin navigation | Pass — Overview, Student Records, Manage Events, and Audit Logs are present in the Admin view | [01-admin-dashboard-full-page.png](./01-admin-dashboard-full-page.png) |
| Student role-specific menu | Pass — only Overview, Student Home, and Events are shown | [03-student-dashboard-role-menu-full-page.png](./03-student-dashboard-role-menu-full-page.png) |
| Non-admin restriction page | Pass — Student access receives HTTP 403 and an Unauthorized Access page | [04-non-admin-403-full-page.png](./04-non-admin-403-full-page.png) |

The Student session was also used to request `admin_dashboard.php`,
`event_management.php`, and `audit_logs.php` directly. Each route returned
HTTP 403.

## Audit Log Evidence

The audit table view is populated from the `audit_logs` database table and shows
the acting Admin, action, entity, timestamp, and details.

| Requirement | Result | Screenshot |
|---|---|---|
| Recorded Admin actions in audit log table | Pass — successful student create, update, and deactivate actions, plus event updates, are visible | [02-audit-logs-full-page.png](./02-audit-logs-full-page.png) |

Audit writes share a database transaction with their corresponding successful
student or event change. The test student account was removed after testing;
the audit history was retained as evidence.

## Verification

- Imported `schema.sql` and verified the `audit_logs` table, indexes, and
  account foreign key.
- PHP syntax checks passed for `dashboard.php`, `audit_logs.php`,
  `auth_helpers.php`, `admin_dashboard.php`, and `event_management.php`.
- Compared dashboard counts with database queries; the displayed values matched.
- Confirmed Student requests to all three Admin-only routes returned HTTP 403.
- Confirmed successful student and event actions produced corresponding audit
  rows, including the Admin actor and entity details.
