<?php

$databaseHost = getenv('STUDENTHUB_DB_HOST') ?: '127.0.0.1';
$databaseName = getenv('STUDENTHUB_DB_NAME') ?: 'studenthub_practical8';
$databaseUser = getenv('STUDENTHUB_DB_USER') ?: 'root';
$databasePassword = getenv('STUDENTHUB_DB_PASSWORD');

if ($databasePassword === false) {
    $databasePassword = '';
}

try {
    $pdo = new PDO(
        "mysql:host={$databaseHost};dbname={$databaseName};charset=utf8mb4",
        $databaseUser,
        $databasePassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    error_log('StudentHub Practical 8 PDO connection failed: ' . $exception->getMessage());
    throw new RuntimeException(
        'Could not connect to MySQL. Check that the database is running and schema.sql has been imported.',
        0,
        $exception
    );
}
