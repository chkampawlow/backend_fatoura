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

    if ($id <= 0) {
        throw new Exception("Invalid item id");
    }

    $conn = db();

    $check = $conn->prepare("
        SELECT ii.id, ii.invoice_id
        FROM erp_invoice_items ii
        INNER JOIN erp_invoices i ON i.id = ii.invoice_id
        WHERE ii.id = ? AND i.user_id = ?
        LIMIT 1
    ");
    $check->bind_param("ii", $id, $user_id);
    $check->execute();
    $itemRow = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$itemRow) {
        throw new Exception("Invoice item not found or not allowed");
    }

    $invoice_id = (int)$itemRow['invoice_id'];

    $stmt = $conn->prepare("DELETE FROM erp_invoice_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $sumStmt = $conn->prepare("
        SELECT
            COALESCE(SUM(subtotal), 0) AS sum_subtotal,
            COALESCE(SUM(montant_tva), 0) AS sum_tva,
            COALESCE(SUM(subtotalTTC), 0) AS sum_ttc
        FROM erp_invoice_items
        WHERE invoice_id = ?
    ");
    $sumStmt->bind_param("i", $invoice_id);
    $sumStmt->execute();
    $totals = $sumStmt->get_result()->fetch_assoc();
    $sumStmt->close();

    $sumSubtotal = (float)$totals['sum_subtotal'];
    $sumTva = (float)$totals['sum_tva'];
    $sumTtc = (float)$totals['sum_ttc'];
    $total = $sumTtc;

    $upd = $conn->prepare("
        UPDATE erp_invoices
        SET subtotal = ?, montant_tva = ?, subtotal_ttc = ?, total = ?
        WHERE id = ? AND user_id = ?
    ");
    $upd->bind_param(
        "ddddii",
        $sumSubtotal,
        $sumTva,
        $sumTtc,
        $total,
        $invoice_id,
        $user_id
    );
    $upd->execute();
    $upd->close();

    jsonResponse([
        "success" => true,
        "message" => "Invoice item deleted successfully",
        "invoice_id" => $invoice_id,
        "totals" => [
            "subtotal" => $sumSubtotal,
            "montant_tva" => $sumTva,
            "subtotal_ttc" => $sumTtc,
            "total" => $total
        ]
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}
?>