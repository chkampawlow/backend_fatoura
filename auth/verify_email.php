<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/response.php';
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

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body.");
    }

    $code = trim($data['code'] ?? '');

    if ($code === '') {
        throw new Exception("Verification code is required");
    }

    if (!preg_match('/^\d{6}$/', $code)) {
        throw new Exception("Verification code must be 6 digits");
    }

    $tokenHash = hash('sha256', $code);
    $type = 'email_verification';

    $conn = db();

    $stmt = $conn->prepare("
        SELECT id, user_id, expires_at, attempts
        FROM user_tokens
        WHERE token_hash = ? AND type = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception("Prepare failed (select token): " . $conn->error);
    }

    $stmt->bind_param("ss", $tokenHash, $type);
    $stmt->execute();
    $tokenRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$tokenRow) {
        throw new Exception("Invalid verification code");
    }

    if ((int)$tokenRow['attempts'] >= 5) {
        throw new Exception("Too many attempts. Request a new code.");
    }

    if (strtotime($tokenRow['expires_at']) < time()) {
        throw new Exception("Verification code expired");
    }

    $verifiedAt = date('Y-m-d H:i:s');

    $updateUserStmt = $conn->prepare("
        UPDATE users
        SET email_verified_at = ?
        WHERE id = ?
    ");

    if (!$updateUserStmt) {
        throw new Exception("Prepare failed (update user): " . $conn->error);
    }

    $userId = (int)$tokenRow['user_id'];
    $updateUserStmt->bind_param("si", $verifiedAt, $userId);
    $updateUserStmt->execute();
    $updateUserStmt->close();

    $deleteStmt = $conn->prepare("
        DELETE FROM user_tokens
        WHERE id = ?
    ");

    if (!$deleteStmt) {
        throw new Exception("Prepare failed (delete token): " . $conn->error);
    }

    $tokenId = (int)$tokenRow['id'];
    $deleteStmt->bind_param("i", $tokenId);
    $deleteStmt->execute();
    $deleteStmt->close();

    jsonResponse([
        "success" => true,
        "message" => "Email verified successfully"
    ]);

} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}