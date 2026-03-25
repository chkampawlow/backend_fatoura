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

    $id = getRequiredInt($data, 'id', 'Client ID');

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

    $check = $conn->prepare("
        SELECT id
        FROM clients
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");

    if (!$check) {
        throw new Exception("Failed to prepare ownership check: " . $conn->error);
    }

    $check->bind_param("ii", $id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $check->close();
        throw new Exception("Client not found or unauthorized");
    }

    $check->close();

    $stmt = $conn->prepare("
        UPDATE clients SET
            type = ?,
            name = ?,
            email = ?,
            phone = ?,
            address = ?,
            fiscalId = ?,
            cin = ?
        WHERE id = ? AND user_id = ?
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare update client query: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssii",
        $type,
        $name,
        $email,
        $phone,
        $address,
        $fiscalId,
        $cin,
        $id,
        $user_id
    );

    $stmt->execute();

    if ($stmt->error) {
        throw new Exception("Failed to update client: " . $stmt->error);
    }

    $stmt->close();

    jsonResponse([
        "success" => true,
        "message" => "Client updated successfully"
    ]);

} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}