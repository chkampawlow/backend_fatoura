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

    $invoice_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($invoice_id <= 0) {
        throw new Exception("Invalid invoice id");
    }

    $conn = db();

    $stmt = $conn->prepare("
        SELECT
            id,
            invoice,
            custom_email,
            custom_code,
            invoice_date,
            invoice_due_date,
            subtotal,
            montant_tva,
            subtotal_ttc,
            total,
            notes,
            invoice_type,
            status,
            type_doc,
            user_id
        FROM erp_invoices
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $invoice_id, $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        throw new Exception("Invoice not found or not allowed");
    }

    jsonResponse([
        "success" => true,
        "invoice" => $row
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}
?>