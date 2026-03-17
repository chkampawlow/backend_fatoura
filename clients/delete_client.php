<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/response.php';
require_once __DIR__ . '/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            "success" => false,
            "message" => "Method not allowed"
        ], 405);
    }

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        $data = $_POST;
    }

    $id = isset($data['id']) ? (int)$data['id'] : 0;

    if ($id <= 0) {
        jsonResponse([
            "success" => false,
            "message" => "Client ID is required"
        ], 400);
    }

    if (function_exists('db')) {
        $conn = db();
    } elseif (isset($conn) && $conn instanceof mysqli) {
    } elseif (isset($mysqli) && $mysqli instanceof mysqli) {
        $conn = $mysqli;
    } else {
        throw new Exception("Database connection unavailable");
    }

    $stmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare delete statement");
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to delete client");
    }

    if ($stmt->affected_rows === 0) {
        $stmt->close();
        jsonResponse([
            "success" => false,
            "message" => "Client not found"
        ], 404);
    }

    $stmt->close();

    jsonResponse([
        "success" => true,
        "message" => "Client deleted successfully"
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 500);
}