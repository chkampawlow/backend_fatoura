<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../auth/auth_required.php';
require_once __DIR__ . '/../static_token.php';
requireStaticToken();
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
            ei.id,
            ei.invoice,
            ei.custom_email,
            ei.custom_code,
            c.name AS client_name,
            ei.invoice_date,
            ei.invoice_due_date,
            ei.subtotal,
            ei.montant_tva,
            ei.subtotal_ttc,
            ei.shipping,
            ei.discount,
            ei.vat,
            ei.total,
            ei.notes,
            ei.invoice_type,
            ei.status,
            ei.type_doc,
            ei.timbre,
            ei.date_ajout,
            ei.tx_retenue,
            ei.retenue,
            ei.net_retenue,
            ei.id_extract,
            ei.id_lettrage,
            ei.contrat_no,
            ei.json_finsys,
            ei.json_return,
            ei.json_return2,
            ei.stat_api,
            ei.mnt_lettre,
            ei.stat_ttn,
            ei.qr_code,
            ei.uuid,
            ei.user_id
        FROM erp_invoices ei
        LEFT JOIN clients c
            ON c.id = CAST(ei.custom_code AS UNSIGNED)
           AND c.user_id = ei.user_id
        WHERE ei.user_id = ?
        ORDER BY ei.id DESC
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