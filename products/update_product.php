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

    $id = getRequiredInt($data, 'id', 'Product ID');
    $code = getOptionalString($data, 'code');
    $name = getRequiredString($data, 'name', 'Product name');
    $unit = getOptionalString($data, 'unit');

    $price = isset($data['price']) && $data['price'] !== ''
        ? validatePositiveNumber($data['price'], 'Price')
        : 0.0;

    $tva_rate = isset($data['tva_rate']) && $data['tva_rate'] !== ''
        ? validatePositiveNumber($data['tva_rate'], 'TVA rate')
        : 0.0;

    validateMaxLength($code, 100, 'Code');
    validateMaxLength($name, 255, 'Product name');
    validateMaxLength($unit, 50, 'Unit');

    $conn = db();

    $stmt = $conn->prepare("
        UPDATE products
        SET code = ?, name = ?, price = ?, tva_rate = ?, unit = ?
        WHERE id = ? AND user_id = ?
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare update product query: " . $conn->error);
    }

    $stmt->bind_param(
        "ssddsii",
        $code,
        $name,
        $price,
        $tva_rate,
        $unit,
        $id,
        $user_id
    );

    $stmt->execute();

    if ($stmt->error) {
        throw new Exception("Failed to update product: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        $stmt->close();
        throw new Exception("Product not found or not allowed");
    }

    $stmt->close();

    jsonResponse([
        "success" => true,
        "message" => "Product updated successfully"
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}