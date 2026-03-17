<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/env.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (empty($_ENV['JWT_SECRET'])) {
    loadEnv(__DIR__ . '/../.env');
}

function generateJwt(array $user, int $expiresInSeconds = 86400): string
{
    $now = time();

    $payload = [
        'iss' => $_ENV['JWT_ISSUER'] ?? 'facturation_app',
        'iat' => $now,
        'exp' => $now + $expiresInSeconds,
        'user' => [
            'id' => (int)$user['id'],
            'email' => $user['email'] ?? '',
        ]
    ];

    return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
}

function decodeJwt(string $token): object
{
    return JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
}

function getBearerToken(): ?string
{
    $headers = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['Authorization'] ?? null;

    if (!$headers && function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $headers = $requestHeaders['Authorization'] ?? $requestHeaders['authorization'] ?? null;
    }

    if (!$headers) {
        return null;
    }

    if (preg_match('/Bearer\s+(\S+)/i', $headers, $matches)) {
        return $matches[1];
    }

    return null;
}
function generateRefreshJwt(array $user, int $expiresInSeconds = 2592000): string
{
    $now = time();

    $payload = [
        'iss' => $_ENV['JWT_ISSUER'] ?? 'facturation_app',
        'iat' => $now,
        'exp' => $now + $expiresInSeconds,
        'type' => 'refresh',
        'user' => [
            'id' => (int)$user['id'],
            'email' => $user['email'] ?? '',
        ]
    ];

    return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
}
?>