<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/response.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    loadEnv(__DIR__ . '/../.env');
} catch (Throwable $e) {
    // Ignore if .env does not exist on Railway
}

function db(): mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
    $user = $_ENV['DB_USER'] ?? getenv('DB_USER');
    $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS');
    $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
    $port = (int)($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306);

    try {
        $conn = new mysqli($host, $user, $pass, $name, $port);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Throwable $e) {
        jsonResponse([
            "success" => false,
            "message" => "Database connection failed",
            "error" => $e->getMessage()
        ], 500);
        exit;
    }
}