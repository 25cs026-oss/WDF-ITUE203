# Practical 6: Fetch JSON and Data Operations

This standalone page fetches three fictional data sets from JSON files and
supports dynamic rendering, text search, category/course filtering, sorting,
pagination, and loading/error feedback.

## Run

Serve the project through XAMPP Apache or another local HTTP server; opening the
HTML file directly does not allow browser `fetch()` requests to load the JSON
files.

```text
http://localhost/studentHUB/practicals/06-fetch-search-filter/index.html
```

The data files are `students.json`, `events.json`, and `faqs.json`. Each file
contains 15 fictional records. `dataset-config.js` defines each data set's
columns and controls, while `app.js` handles loading, filtering, sorting,
rendering, and pagination.

Use the search field, filter and sort controls, and Previous/Next buttons to
verify the interactions. A missing or invalid data file produces a visible
error state instead of an empty success state.
