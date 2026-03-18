<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/jwt_helper.php';

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

    $refreshToken = $data['refresh_token'] ?? '';

    if ($refreshToken === '') {
        throw new Exception("Refresh token is required.");
    }

    $decoded = decodeJwt($refreshToken);

    if (($decoded->type ?? '') !== 'refresh') {
        throw new Exception("Invalid token type.");
    }

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