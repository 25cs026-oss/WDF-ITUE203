<?php

$databaseHost = getenv('STUDENTHUB_DB_HOST') ?: '127.0.0.1';
$databaseName = getenv('STUDENTHUB_DB_NAME') ?: 'studenthub_practical8';
$databaseUser = getenv('STUDENTHUB_DB_USER') ?: 'root';
$databasePassword = getenv('STUDENTHUB_DB_PASSWORD');

if ($databasePassword === false) {
    $databasePassword = '';
}

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = new mysqli($databaseHost, $databaseUser, $databasePassword, $databaseName);
    $mysqli->set_charset('utf8mb4');
} catch (Throwable $exception) {
    error_log('StudentHub database connection failed: ' . $exception->getMessage());
    throw new RuntimeException(
        'Could not connect to the database. Check that MySQL is running and schema.sql has been imported.',
        0,
        $exception
    );
}
