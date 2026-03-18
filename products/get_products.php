<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/auth_required.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        jsonResponse([
            "success" => false,
            "message" => "Method not allowed. Use GET."
        ], 405);
        exit;
    }

    $authUser = requireAuth();
    $user_id = (int)$authUser->id;

    $conn = db();

    $stmt = $conn->prepare("
        SELECT id, code, name, price, tva_rate, unit
        FROM products
        WHERE user_id = ?
        ORDER BY name ASC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $stmt->close();

    jsonResponse([
        "success" => true,
        "data" => $rows
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}
?>