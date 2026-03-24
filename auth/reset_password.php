<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../static_token.php';

requireStaticToken();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(["success" => false, "message" => "Use POST"], 405);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    $code = trim($data['code'] ?? '');
    $password = $data['password'] ?? '';

    if (!preg_match('/^\d{6}$/', $code)) {
        throw new Exception("Invalid code");
    }

    if (strlen($password) < 6) {
        throw new Exception("Password too short");
    }

    $conn = db();

    $hash = hash('sha256', $code);
    $type = 'password_reset';

    $stmt = $conn->prepare("
        SELECT id, user_id, expires_at, attempts
        FROM user_tokens
        WHERE token_hash = ? AND type = ?
        LIMIT 1
    ");
    $stmt->bind_param("ss", $hash, $type);
    $stmt->execute();
    $token = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$token) {
        throw new Exception("Invalid code");
    }

    if ($token['attempts'] >= 5) {
        throw new Exception("Too many attempts");
    }

    if (strtotime($token['expires_at']) < time()) {
        throw new Exception("Code expired");
    }

    // update password
    $newHash = password_hash($password, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $update->bind_param("si", $newHash, $token['user_id']);
    $update->execute();
    $update->close();

    // delete token
    $del = $conn->prepare("DELETE FROM user_tokens WHERE id = ?");
    $del->bind_param("i", $token['id']);
    $del->execute();
    $del->close();

    jsonResponse([
        "success" => true,
        "message" => "Password updated"
    ]);

} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}