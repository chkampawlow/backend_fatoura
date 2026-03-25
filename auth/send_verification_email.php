<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../static_token.php';
require_once __DIR__ . '/../mailer/mailer.php';
require_once __DIR__ . '/auth_required.php';

requireStaticToken();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            'success' => false,
            'message' => 'Method not allowed. Use POST.'
        ], 405);
        exit;
    }

    $authUser = requireAuth();
    $userId = (int) $authUser->id;

    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        $data = [];
    }

    $language = trim($data['language'] ?? 'en');

    $conn = db();

    $stmt = $conn->prepare("
        SELECT id, email, email_verified_at
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception('Prepare failed (select user): ' . $conn->error);
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception('User not found');
    }

    if (!empty($user['email_verified_at'])) {
        jsonResponse([
            'success' => true,
            'message' => 'Email already verified'
        ]);
        exit;
    }

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $tokenHash = hash('sha256', $code);
    $expires = date('Y-m-d H:i:s', time() + 600);
    $type = 'email_verification';

    $deleteStmt = $conn->prepare("
        DELETE FROM user_tokens
        WHERE user_id = ? AND type = ?
    ");

    if (!$deleteStmt) {
        throw new Exception('Prepare failed (delete): ' . $conn->error);
    }

    $deleteStmt->bind_param('is', $userId, $type);
    $deleteStmt->execute();
    $deleteStmt->close();

    $insertStmt = $conn->prepare("
        INSERT INTO user_tokens (user_id, token_hash, expires_at, type, attempts)
        VALUES (?, ?, ?, ?, 0)
    ");

    if (!$insertStmt) {
        throw new Exception('Prepare failed (insert): ' . $conn->error);
    }

    $insertStmt->bind_param('isss', $userId, $tokenHash, $expires, $type);
    $insertStmt->execute();
    $insertStmt->close();

    sendVerificationCode($user['email'], $code, $language);

    jsonResponse([
        'success' => true,
        'message' => 'Verification email sent'
    ]);
} catch (Throwable $e) {
    jsonResponse([
        'success' => false,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], 500);
}