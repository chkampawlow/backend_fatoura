// putting client into archieved state instead of deleting permanently
<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/auth_required.php';
require_once __DIR__ . '/../static_token.php';

requireStaticToken();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            "success" => false,
            "message" => "Method not allowed. Use POST."
        ], 405);
        exit;
    }

    $authUser = requireAuth();
    $user_id = (int)$authUser->id;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body.");
    }

    $id = (int)($data["id"] ?? 0);

    if ($id <= 0) {
        throw new Exception("Client ID is required");
    }

    $conn = db();

    $check = $conn->prepare("
        SELECT id
        FROM clients
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    $check->bind_param("ii", $id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Client not found or unauthorized");
    }

    $check->close();

    $stmt = $conn->prepare("
        UPDATE clients
        SET is_archived = 1,
            archived_at = NOW()
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();

    jsonResponse([
        "success" => true,
        "message" => "Client archived successfully"
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}