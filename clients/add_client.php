<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/validator.php';
require_once __DIR__ . '/../auth/auth_required.php';
require_once __DIR__ . '/../static_token.php';

requireStaticToken();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            "success" => false,
            "message" => "Method not allowed. Use POST."
        ], 405);
        exit;
    }

    $authUser = requireAuth();
    $user_id = (int)$authUser->id;

    $data = requireJsonBody();

    $type = getRequiredString($data, 'type', 'Type');
    $name = getOptionalString($data, 'name');
    $email = strtolower(getOptionalString($data, 'email'));
    $phone = preg_replace('/\s+/', ' ', getOptionalString($data, 'phone'));
    $address = getOptionalString($data, 'address');
    $fiscalId = strtoupper(getOptionalString($data, 'fiscalId'));
    $cin = getOptionalString($data, 'cin');

    validateEnum($type, ['company', 'individual'], 'Type');

    if ($name !== '') {
        validateMaxLength($name, 255, 'Name');
    }

    validateMaxLength($email, 255, 'Email');
    validateMaxLength($phone, 20, 'Phone');
    validateMaxLength($address, 255, 'Address');
    validateMaxLength($fiscalId, 13, 'Fiscal ID');
    validateMaxLength($cin, 8, 'CIN');

    validateEmailIfPresent($email);
    validatePhoneIfPresent($phone);
    validateAddressIfPresent($address);

    if ($type === "company" && $fiscalId === "") {
        throw new Exception("fiscalId is required for company");
    }

    if ($type === "individual" && $cin === "") {
        throw new Exception("cin is required for individual");
    }

    if ($fiscalId !== '') {
        validateFiscalId($fiscalId);
    }

    if ($cin !== '') {
        validateCin($cin);
    }

    $conn = db();

    $stmt = $conn->prepare("
        INSERT INTO clients (
            user_id,
            type,
            name,
            email,
            phone,
            address,
            fiscalId,
            cin
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare add client query: " . $conn->error);
    }

    $stmt->bind_param(
        "isssssss",
        $user_id,
        $type,
        $name,
        $email,
        $phone,
        $address,
        $fiscalId,
        $cin
    );

    $stmt->execute();

    if ($stmt->error) {
        throw new Exception("Failed to add client: " . $stmt->error);
    }

    $newId = $conn->insert_id;
    $stmt->close();

    jsonResponse([
        "success" => true,
        "id" => $newId,
        "message" => "Client added successfully"
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}