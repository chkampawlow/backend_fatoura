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

    $invoice_id   = isset($data['invoice_id']) ? (int)$data['invoice_id'] : 0;
    $product_code = isset($data['product_code']) ? trim((string)$data['product_code']) : '';
    $product      = isset($data['product']) ? trim((string)$data['product']) : '';
    $qty          = isset($data['qty']) ? (float)$data['qty'] : 0;
    $tva_rate     = isset($data['tva_rate']) ? (float)$data['tva_rate'] : 0;
    $price        = isset($data['price']) ? (float)$data['price'] : 0;
    $discount     = isset($data['discount']) ? (float)$data['discount'] : 0;

    if ($invoice_id <= 0) {
        throw new Exception("Invalid invoice_id");
    }

    if ($product === '') {
        throw new Exception("Product name is required");
    }

    if ($qty <= 0) {
        throw new Exception("Quantity must be greater than 0");
    }

    if ($price < 0) {
        throw new Exception("Price cannot be negative");
    }

    if ($discount < 0 || $discount > 100) {
        throw new Exception("Discount must be between 0 and 100");
    }

    if ($tva_rate < 0) {
        throw new Exception("TVA rate cannot be negative");
    }

    $conn = db();

    // Check that invoice exists and belongs to logged user
    $check = $conn->prepare("
        SELECT id, invoice, invoice_date
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

    $invoice = (string)$invoiceRow['invoice'];
    $invoice_date = $invoiceRow['invoice_date'];

    // Recompute values server-side
    $subtotal = $qty * $price;
    if ($discount > 0) {
        $subtotal -= ($subtotal * ($discount / 100));
    }

    $montant_tva = $subtotal * ($tva_rate / 100);
    $subtotalTTC = $subtotal + $montant_tva;

    $stmt = $conn->prepare("
        INSERT INTO erp_invoice_items (
            invoice_id,
            invoice,
            product_code,
            product,
            qty,
            tva_rate,
            montant_tva,
            price,
            discount,
            subtotal,
            subtotalTTC,
            invoice_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssddddddds",
        $invoice_id,
        $invoice,
        $product_code,
        $product,
        $qty,
        $tva_rate,
        $montant_tva,
        $price,
        $discount,
        $subtotal,
        $subtotalTTC,
        $invoice_date
    );

    $stmt->execute();
    $itemId = $stmt->insert_id;
    $stmt->close();

    // Recompute invoice totals
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
        "id" => $itemId,
        "message" => "Invoice item added successfully",
        "invoice_id" => $invoice_id,
        "item" => [
            "invoice" => $invoice,
            "product_code" => $product_code,
            "product" => $product,
            "qty" => $qty,
            "tva_rate" => $tva_rate,
            "montant_tva" => $montant_tva,
            "price" => $price,
            "discount" => $discount,
            "subtotal" => $subtotal,
            "subtotalTTC" => $subtotalTTC,
            "invoice_date" => $invoice_date
        ],
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