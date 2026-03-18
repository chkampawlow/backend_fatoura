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
            shipping,
            discount,
            vat,
            total,
            notes,
            invoice_type,
            status,
            type_doc,
            timbre,
            date_ajout,
            tx_retenue,
            retenue,
            net_retenue,
            id_extract,
            id_lettrage,
            contrat_no,
            json_finsys,
            json_return,
            json_return2,
            stat_api,
            mnt_lettre,
            stat_ttn,
            qr_code,
            uuid,
            user_id
        FROM erp_invoices
        WHERE user_id = ?
        ORDER BY id DESC
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