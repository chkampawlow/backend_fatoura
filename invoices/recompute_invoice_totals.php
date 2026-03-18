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
        throw new Exception("Invalid JSON body");
    }

    $invoice_id = isset($data['invoice_id']) ? (int)$data['invoice_id'] : 0;

    if ($invoice_id <= 0) {
        throw new Exception("Invalid invoice_id");
    }

    $conn = db();

    $check = $conn->prepare("
        SELECT id
        FROM erp_invoices
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    $check->bind_param("ii", $invoice_id, $user_id);
    $check->execute();
    $invoiceRow = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$invoiceRow) {
        throw new Exception("Invoice not found or not allowed");
    }

    $stmt = $conn->prepare("
        SELECT
            COALESCE(SUM(subtotal), 0) AS sumHT,
            COALESCE(SUM(montant_tva), 0) AS sumTVA,
            COALESCE(SUM(subtotalTTC), 0) AS sumTTC
        FROM erp_invoice_items
        WHERE invoice_id = ?
    ");
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $sumHT = (float)$result['sumHT'];
    $sumTVA = (float)$result['sumTVA'];
    $sumTTC = (float)$result['sumTTC'];

    $stmt2 = $conn->prepare("
        UPDATE erp_invoices
        SET subtotal = ?, montant_tva = ?, total = ?, subtotal_ttc = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt2->bind_param("ddddii", $sumHT, $sumTVA, $sumTTC, $sumTTC, $invoice_id, $user_id);
    $stmt2->execute();
    $stmt2->close();

    jsonResponse([
        "success" => true,
        "invoice_id" => $invoice_id,
        "subtotal" => $sumHT,
        "montant_tva" => $sumTVA,
        "subtotal_ttc" => $sumTTC,
        "total" => $sumTTC
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}
?>