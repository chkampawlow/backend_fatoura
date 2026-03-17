<?php
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/jwt_helper.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body.");
    }

    $refreshToken = $data['refresh_token'] ?? '';

    if ($refreshToken === '') {
        throw new Exception("Refresh token is required.");
    }

    $decoded = decodeJwt($refreshToken);
    $user = (array)$decoded->user;

    $newAccessToken = generateJwt($user, 60 * 15);

    jsonResponse([
        "success" => true,
        "access_token" => $newAccessToken
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => "Invalid or expired refresh token."
    ], 401);
}
if (($decoded->type ?? '') !== 'refresh') {
    throw new Exception("Invalid token type.");
}
?>