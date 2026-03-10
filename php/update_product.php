<?php
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth_required.php';

try {
    $authUser = requireAuth();
    $user_id = (int)$authUser->id;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body");
    }

    $id = isset($data['id']) ? (int)$data['id'] : 0;
    $code = isset($data['code']) ? trim((string)$data['code']) : '';
    $name = isset($data['name']) ? trim((string)$data['name']) : '';
    $price = isset($data['price']) ? (float)$data['price'] : 0;
    $tva_rate = isset($data['tva_rate']) ? (float)$data['tva_rate'] : 0;
    $unit = isset($data['unit']) ? trim((string)$data['unit']) : '';

    if ($id <= 0) {
        throw new Exception("Invalid product id");
    }

    if ($name === '') {
        throw new Exception("Product name is required");
    }

    if ($price < 0) {
        throw new Exception("Price cannot be negative");
    }

    if ($tva_rate < 0) {
        throw new Exception("TVA rate cannot be negative");
    }

    $conn = db();

    $stmt = $conn->prepare("
        UPDATE products
        SET code = ?, name = ?, price = ?, tva_rate = ?, unit = ?
        WHERE id = ? AND user_id = ?
    ");
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
?>