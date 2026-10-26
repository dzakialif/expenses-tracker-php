<?php

$host = "localhost";
$port = "5432";
$dbname = "expenses_tracker_db";
$username = "belajar_user";
$password = "belajar123";

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

try {
    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log('[ERROR] Database connection: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Terjadi kesalahan database']);
    exit;
}