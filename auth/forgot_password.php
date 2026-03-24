<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../static_token.php';
require_once __DIR__ . '/../mailer/mailer.php';

requireStaticToken();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(["success" => false, "message" => "Use POST"], 405);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $email = strtolower(trim($data['email'] ?? ''));

    if ($email === '') {
        throw new Exception("Email is required");
    }

    $conn = db();

    $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Always success (security)
    if (!$user) {
        jsonResponse([
            "success" => true,
            "message" => "If email exists, code sent"
        ]);
        exit;
    }

    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash = hash('sha256', $code);
    $expires = date('Y-m-d H:i:s', time() + 600);
    $type = 'password_reset';

    // delete old tokens
    $del = $conn->prepare("DELETE FROM user_tokens WHERE user_id = ? AND type = ?");
    $del->bind_param("is", $user['id'], $type);
    $del->execute();
    $del->close();

    // insert new
    $ins = $conn->prepare("
        INSERT INTO user_tokens (user_id, token_hash, expires_at, type, attempts)
        VALUES (?, ?, ?, ?, 0)
    ");
    $ins->bind_param("isss", $user['id'], $hash, $expires, $type);
    $ins->execute();
    $ins->close();

    sendMailMessage(
        $user['email'],
        'Reset your password',
        "<h2>Password Reset</h2>
         <p>Your code:</p>
         <h1>{$code}</h1>
         <p>Expires in 10 minutes</p>"
    );

    jsonResponse([
        "success" => true,
        "message" => "If email exists, code sent"
    ]);

} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}