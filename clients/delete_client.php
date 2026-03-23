<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/auth_required.php';
require_once __DIR__ . '/../static_token.php';
requireStaticToken();
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            "success" => false,
            "message" => "Method not allowed"
        ], 405);
        exit;
    }

    $authUser = requireAuth();
    $user_id = (int)$authUser->id;

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

    $conn = db();

    $stmt = $conn->prepare("DELETE FROM clients WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare delete statement");
    }

    $stmt->bind_param("ii", $id, $user_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to delete client");
    }

    if ($stmt->affected_rows === 0) {
        $stmt->close();
        jsonResponse([
            "success" => false,
            "message" => "Client not found or access denied"
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