<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body.");
    }

    $first_name = trim($data['first_name'] ?? '');
    $last_name = trim($data['last_name'] ?? '');
    $organization_name = trim($data['organization_name'] ?? '');
    $fiscal_id = strtoupper(trim($data['fiscal_id'] ?? ''));
    $email = strtolower(trim($data['email'] ?? ''));
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';

    if (
        $first_name === '' ||
        $last_name === '' ||
        $fiscal_id === '' ||
        $email === '' ||
        $password === '' ||
        $confirm_password === ''
    ) {
        throw new Exception("All required fields must be filled.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address.");
    }

    if (!preg_match('/^[0-9]{7}[A-Z]{3}[0-9]{3}$/', $fiscal_id)) {
        throw new Exception("Invalid Tunisian fiscal ID format. Example: 1234567ABC123");
    }

    if ($phone !== '' && !preg_match('/^\+216\s?\d{2}\s?\d{3}\s?\d{3}$/', $phone)) {
        throw new Exception("Invalid Tunisian phone number. Example: +216 12 345 678");
    }

    if (strlen($password) < 6) {
        throw new Exception("Password must be at least 6 characters.");
    }

    if ($password !== $confirm_password) {
        throw new Exception("Passwords do not match.");
    }

    $conn = db();

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        throw new Exception("This email is already used.");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (
            first_name,
            last_name,
            organization_name,
            fiscal_id,
            email,
            phone,
            password_hash
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssssss",
        $first_name,
        $last_name,
        $organization_name,
        $fiscal_id,
        $email,
        $phone,
        $password_hash
    );

    $stmt->execute();
    $userId = $stmt->insert_id;
    $stmt->close();

    jsonResponse([
        "success" => true,
        "message" => "Account created successfully.",
        "user_id" => $userId
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}
?>