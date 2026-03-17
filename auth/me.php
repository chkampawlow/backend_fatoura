<?php
require_once __DIR__ . '/auth_required.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/db.php';

try {

    $authUser = requireAuth();
    $conn = db();

    $stmt = $conn->prepare("
        SELECT id, email, fiscal_id, organization_name, fax, address, website
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $authUser->id);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        jsonResponse([
            "success" => false,
            "message" => "User not found."
        ], 404);
    }

    jsonResponse([
        "success" => true,
        "user" => [
            "id" => (int)$user['id'],
            "email" => $user['email'],
            "organization_name" => $user['organization_name'] ?? "",
            "fiscal_id" => $user['fiscal_id'] ?? "",
            "fax" => $user['fax'] ?? "",
            "address" => $user['address'] ?? "",
            "website" => $user['website'] ?? ""
        ]
    ]);

} catch (Throwable $e) {

    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 401);

}
?>