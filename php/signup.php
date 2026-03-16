<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/response.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body.");
    }


    $organization_name = trim($data['organization_name'] ?? '');
    $fiscal_id = strtoupper(trim($data['fiscal_id'] ?? ''));
    $email = strtolower(trim($data['email'] ?? ''));
    $phone = preg_replace('/\s+/', ' ', trim($data['phone'] ?? ''));
    $password = $data['password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';

    if (
        $fiscal_id === '' ||
        $email === '' ||
        $phone === '' ||
        $password === '' ||
        $confirm_password === ''
    ) {
        throw new Exception("All required fields must be filled.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address.");
    }

    if (!preg_match('/^[0-9]{7}[A-Z]{3}[0-9]{3}$/', $fiscal_id)) {
        throw new Exception("Invalid fiscal ID.");
    }

    if (!preg_match('/^\+\d{1,3}\s\d{6,12}$/', $phone)) {
        throw new Exception("Invalid phone number. Example: +216 20123456");
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
            organization_name,
            fiscal_id,
            email,
            phone,
            password_hash
        ) VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare signup query: " . $conn->error);
    }

    $stmt->bind_param(
        "sssss",
        $organization_name,
        $fiscal_id,
        $email,
        $phone,
        $password_hash
    );

    $stmt->execute();

    if ($stmt->error) {
        throw new Exception("Failed to create account: " . $stmt->error);
    }

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