<?php

require_once __DIR__ . '/config/env.php';
loadEnv(__DIR__ . '/.env');

function requireStaticToken() {
    $expectedToken = $_ENV['STATIC_API_TOKEN'] ?? '';

    $headers = [];

    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$key] = $value;
            }
        }
    }

    $authHeader = '';

    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
    } elseif (isset($headers['authorization'])) {
        $authHeader = $headers['authorization'];
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (!$authHeader) {
        sendError('Missing API token', 401);
    }

    if (stripos($authHeader, 'Bearer ') !== 0) {
        sendError('Invalid authorization format', 401);
    }

    $providedToken = trim(substr($authHeader, 7));

    if ($providedToken !== $expectedToken) {
        sendError('Invalid API token', 401);
    }
}

function sendError($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
}