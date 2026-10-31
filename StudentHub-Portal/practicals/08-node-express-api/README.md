# Practical 8: REST API with Node.js and Express

The Practical 8 PDF has conflicting instructions: its aim and result describe a
Node.js/Express CRUD API, while its requirements list a MySQL schema, a PHP PDO
connection, and a prepared-query test page. This folder implements the
Express-based CRUD API and includes the separate PDO connection/test page;
both use the integrated project database schema at `../../schema.sql`.

## Database setup

Import the project's [`schema.sql`](../../schema.sql) into MySQL. The default
local database is `studenthub_practical8`; set the `STUDENTHUB_DB_*`
environment variables to use different credentials.

## Run the Express API

```powershell
cd C:\xampp\htdocs\studentHUB\practicals\08-node-express-api
npm ci
npm start
```

The server listens on `http://localhost:3000`. Endpoints:

| Method | Path | Purpose |
|---|---|---|
| GET | `/api/students` | List students |
| GET | `/api/students/:id` | Read one student |
| POST | `/api/students` | Create a student |
| PUT | `/api/students/:id` | Update a student |
| DELETE | `/api/students/:id` | Delete a student |

JSON writes accept `full_name` and `email`. The API uses parameterized SQL,
validates inputs and IDs, returns appropriate HTTP status codes, and returns
generic server errors without exposing database details.

Run the isolated endpoint tests (they use a test double and do not write to the
local database):

```powershell
npm test
```

## PHP PDO companion

With Apache and MySQL running and the schema imported, open
`http://localhost/studentHUB/practicals/08-node-express-api/php/test_connection.php`.
It verifies a PDO connection and runs a prepared query against events and
registrations. The integrated portal's root `db.php` remains MySQLi-based for
compatibility with the other practicals.
