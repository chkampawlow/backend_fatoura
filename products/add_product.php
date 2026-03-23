<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/db.php';
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

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body");
    }

    $code = isset($data['code']) ? trim((string)$data['code']) : '';
    $name = isset($data['name']) ? trim((string)$data['name']) : '';
    $price = isset($data['price']) ? (float)$data['price'] : 0;
    $tva_rate = isset($data['tva_rate']) ? (float)$data['tva_rate'] : 0;
    $unit = isset($data['unit']) ? trim((string)$data['unit']) : '';

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
        INSERT INTO products (
            user_id,
            code,
            name,
            price,
            tva_rate,
            unit
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issdds",
        $user_id,
        $code,
        $name,
        $price,
        $tva_rate,
        $unit
    );

    $stmt->execute();
    $productId = $stmt->insert_id;
    $stmt->close();

    jsonResponse([
        "success" => true,
        "id" => $productId,
        "message" => "Product created successfully"
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}
?>