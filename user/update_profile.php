<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../auth/auth_required.php';
require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/db.php';
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
    $conn = db();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON body.");
    }

    $organization_name = trim($data['organization_name'] ?? '');
    $fax = trim($data['fax'] ?? '');
    $address = trim($data['address'] ?? '');
    $website = trim($data['website'] ?? '');

    if ($organization_name === '') {
        throw new Exception("Organization name is required.");
    }


    $stmt = $conn->prepare("
        UPDATE users
        SET organization_name = ?, fax = ?, address = ?, website = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssssi",
        $organization_name,
        $fax,
        $address,
        $website,
        $authUser->id
    );

    $stmt->execute();
    $stmt->close();

    jsonResponse([
        "success" => true,
        "message" => "Profile updated successfully."
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}
?>