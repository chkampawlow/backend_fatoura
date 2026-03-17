<?php
require_once __DIR__ . '/jwt_helper.php';
require_once __DIR__ . '/response.php';

function requireAuth(): object
{
    $token = getBearerToken();

    if (!$token) {
        jsonResponse([
            "success" => false,
            "message" => "Missing token."
        ], 401);
        exit;
    }

    try {
        return decodeJwt($token)->user;
    } catch (Throwable $e) {
        jsonResponse([
            "success" => false,
            "message" => "Invalid or expired token."
        ], 401);
        exit;
    }
}