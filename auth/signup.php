<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/validator.php';
require_once __DIR__ . '/../static_token.php';

requireStaticToken();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            'success' => false,
            'message' => 'Method not allowed. Use POST.'
        ], 405);
        exit;
    }

    $data = requireJsonBody();

    $organization_name = getOptionalString($data, 'organization_name');
    $fiscal_id = strtoupper(getRequiredString($data, 'fiscal_id', 'Fiscal ID'));
    $email = strtolower(getRequiredString($data, 'email', 'Email'));
    $phone = preg_replace('/\s+/', ' ', getRequiredString($data, 'phone', 'Phone'));
    $password = (string)($data['password'] ?? '');
    $confirm_password = (string)($data['confirm_password'] ?? '');

    if ($password === '' || $confirm_password === '') {
        throw new Exception('Password and confirm password are required.');
    }

    validateEmailIfPresent($email);
    validateMaxLength($organization_name, 255, 'Organization name');
    validateMaxLength($fiscal_id, 13, 'Fiscal ID');
    validateMaxLength($email, 255, 'Email');
    validateMaxLength($phone, 20, 'Phone');

    if (!preg_match('/^[0-9]{7}[A-Z]{3}[0-9]{3}$/', $fiscal_id)) {
        throw new Exception('Invalid fiscal ID.');
    }

    if (!preg_match('/^\+\d{1,3}\s\d{6,12}$/', $phone)) {
        throw new Exception('Invalid phone number. Example: +216 20123456');
    }

    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters.');
    }

    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match.');
    }

    $conn = db();

    ensureUserFiscalIdUnique(
        $conn,
        $fiscal_id,
        null,
        'fiscal_id'
    );

    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');

    if (!$stmt) {
        throw new Exception('Failed to prepare email uniqueness query: ' . $conn->error);
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        throw new Exception('This email is already used.');
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare('
        INSERT INTO users (
            organization_name,
            fiscal_id,
            email,
            phone,
            password_hash
        ) VALUES (?, ?, ?, ?, ?)
    ');

    if (!$stmt) {
        throw new Exception('Failed to prepare signup query: ' . $conn->error);
    }

    $stmt->bind_param(
        'sssss',
        $organization_name,
        $fiscal_id,
        $email,
        $phone,
        $password_hash
    );

    $stmt->execute();

    if ($stmt->error) {
        throw new Exception('Failed to create account: ' . $stmt->error);
    }

    $userId = $stmt->insert_id;
    $stmt->close();

    jsonResponse([
        'success' => true,
        'message' => 'Account created successfully.',
        'user_id' => $userId
    ]);
} catch (Throwable $e) {
    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}