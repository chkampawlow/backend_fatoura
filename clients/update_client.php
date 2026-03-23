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
        throw new Exception("Invalid JSON body.");
    }

    $id = (int)($data["id"] ?? 0);

    $type = trim($data["type"] ?? "");
    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");
    $phone = trim($data["phone"] ?? "");
    $address = trim($data["address"] ?? "");
    $fiscalId = trim($data["fiscalId"] ?? "");
    $cin = trim($data["cin"] ?? "");

    if ($id <= 0) {
        throw new Exception("Client ID is required");
    }

    if ($type === "" || $name === "") {
        throw new Exception("type and name are required");
    }

    if ($type !== "company" && $type !== "individual") {
        throw new Exception("type must be either company or individual");
    }

    if ($type === "company" && $fiscalId === "") {
        throw new Exception("fiscalId is required for company");
    }

    if ($type === "individual" && $cin === "") {
        throw new Exception("cin is required for individual");
    }

    $conn = db();

    // ✅ Make sure client belongs to user
    $check = $conn->prepare("
        SELECT id FROM clients WHERE id = ? AND user_id = ?
    ");
    $check->bind_param("ii", $id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Client not found or unauthorized");
    }

    $check->close();

    // ✅ Update client
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