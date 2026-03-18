<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/auth_required.php';

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

    $type = trim($data["type"] ?? "");
    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");
    $phone = trim($data["phone"] ?? "");
    $address = trim($data["address"] ?? "");
    $fiscalId = trim($data["fiscalId"] ?? "");
    $cin = trim($data["cin"] ?? "");

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