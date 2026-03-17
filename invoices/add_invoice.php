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

    $client_id        = isset($data['client_id']) ? (int)$data['client_id'] : 0;
    $invoice_date     = isset($data['invoice_date']) ? trim((string)$data['invoice_date']) : '';
    $invoice_due_date = isset($data['invoice_due_date']) ? trim((string)$data['invoice_due_date']) : '';
    $status           = isset($data['status']) ? trim((string)$data['status']) : 'open';
    $subtotal         = isset($data['subtotal']) ? (float)$data['subtotal'] : 0;
    $montant_tva      = isset($data['montant_tva']) ? (float)$data['montant_tva'] : 0;
    $subtotal_ttc     = isset($data['subtotal_ttc']) ? (float)$data['subtotal_ttc'] : 0;
    $total            = isset($data['total']) ? (float)$data['total'] : 0;
    $invoice_type     = isset($data['invoice_type']) ? trim((string)$data['invoice_type']) : 'FACTURE';
    $notes            = isset($data['notes']) ? trim((string)$data['notes']) : '';

    if ($client_id <= 0) {
        throw new Exception("Invalid client_id");
    }

    if ($invoice_date === '') {
        throw new Exception("invoice_date is required");
    }

    if ($invoice_due_date === '') {
        throw new Exception("invoice_due_date is required");
    }

    $conn = db();

    $checkClient = $conn->prepare("
        SELECT id, name
        FROM clients
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    $checkClient->bind_param("ii", $client_id, $user_id);
    $checkClient->execute();
    $clientRow = $checkClient->get_result()->fetch_assoc();
    $checkClient->close();

    if (!$clientRow) {
        throw new Exception("Client not found or not allowed");
    }

    $invoiceNumber = 'INV-' . date('Ymd-His');
    $custom_code = (string)$client_id;

    $sql = "
        INSERT INTO erp_invoices (
            invoice,
            custom_code,
            invoice_date,
            invoice_due_date,
            subtotal,
            montant_tva,
            subtotal_ttc,
            shipping,
            discount,
            vat,
            total,
            notes,
            invoice_type,
            status,
            type_doc,
            user_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, 0, ?, ?, ?, ?, 'F', ?)
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssddddsssi",
        $invoiceNumber,
        $custom_code,
        $invoice_date,
        $invoice_due_date,
        $subtotal,
        $montant_tva,
        $subtotal_ttc,
        $total,
        $notes,
        $invoice_type,
        $status,
        $user_id
    );

    $stmt->execute();
    $invoiceId = $stmt->insert_id;
    $stmt->close();

    jsonResponse([
        "success" => true,
        "id" => $invoiceId,
        "invoice" => $invoiceNumber,
        "client_id" => $client_id,
        "user_id" => $user_id,
        "message" => "Invoice created successfully"
    ]);
} catch (Throwable $e) {
    jsonResponse([
        "success" => false,
        "message" => $e->getMessage()
    ], 400);
}
?>