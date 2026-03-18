<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/response.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ==============================
   LOAD ENV FILE
============================== */

try {
    loadEnv(__DIR__ . '/../.env');
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => "Failed to load environment configuration"
    ], 500);
}


/* ==============================
   DATABASE CONNECTION
============================== */

function db(): mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';
    $name = $_ENV['DB_NAME'] ?? 'facturation_local';
    $port = (int)($_ENV['DB_PORT'] ?? 3306);

    try {

        $conn = new mysqli($host, $user, $pass, $name, $port);

        $conn->set_charset("utf8mb4");

        return $conn;

    } catch (Throwable $e) {

        jsonResponse([
            "success" => false,
            "message" => "Database connection failed"
        ], 500);

        exit;
    }
}
