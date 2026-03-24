<?php

header('Content-Type: application/json');

require_once __DIR__ . '/jwt_helper.php';
require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../static_token.php';

requireStaticToken();

function requireAuth(): object
{
    $token = getBearerToken();

    if (!$token) {
        jsonResponse([
            "success" => false,
            "message" => "Missing access token."
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