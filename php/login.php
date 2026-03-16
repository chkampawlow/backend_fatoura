<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_helper.php';
require_once __DIR__ . '/response.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body.");
    }

    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';
    $remember_me = (bool)($data['remember_me'] ?? false);

    if ($email === '' || $password === '') {
        jsonResponse([
            "success" => false,
            "message" => "Email and password are required."
        ], 400);
    }

    $conn = db();

    $stmt = $conn->prepare("
        SELECT id, email, password_hash
        FROM users
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        jsonResponse([
            "success" => false,
            "message" => "Invalid email or password."
        ], 401);
    }

    // short-lived access token
    $accessExpiresIn = 60 * 15; // 15 minutes

    // longer-lived refresh token
    $refreshExpiresIn = $remember_me
        ? (60 * 60 * 24 * 30) // 30 days
        : (60 * 60 * 24 * 7); // 7 days

    $accessToken = generateJwt($user, $accessExpiresIn);
    $refreshToken = generateRefreshJwt($user, $refreshExpiresIn);

    jsonResponse([
        "success" => true,
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "remember_me" => $remember_me,
        "access_expires_in" => $accessExpiresIn,
        "refresh_expires_in" => $refreshExpiresIn,
        "user" => [
            "id" => (int)$user['id'],
            "email" => $user['email']
        ]
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 500);
}
?>