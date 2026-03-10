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

    $host = $_ENV['DB_HOST'] ?? getenv('MYSQLHOST');
    $user = $_ENV['DB_USER'] ?? getenv('MYSQLUSER');
    $pass = $_ENV['DB_PASS'] ?? getenv('MYSQLPASSWORD');
    $name = $_ENV['DB_NAME'] ?? getenv('MYSQLDATABASE');
    $port = (int)($_ENV['DB_PORT'] ?? getenv('MYSQLPORT'));

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