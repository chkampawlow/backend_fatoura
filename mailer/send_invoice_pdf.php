<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/response.php';
require_once __DIR__ . '/../static_token.php';
require_once __DIR__ . '/../auth/auth_required.php';
require_once __DIR__ . '/mailer.php';

requireStaticToken();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            'success' => false,
            'message' => 'Method not allowed. Use POST.'
        ], 405);
        exit;
    }

    $authUser = requireAuth();

    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data)) {
        throw new Exception('Invalid JSON body.');
    }

    $pdfBase64 = trim($data['pdf'] ?? '');
    $filename = trim($data['filename'] ?? 'invoice.pdf');
    $to = trim($data['email'] ?? '');

    if ($pdfBase64 === '') {
        throw new Exception('Missing PDF data.');
    }

    if ($to === '') {
        // fallback: send to logged-in user email
        $to = trim($authUser->email ?? '');
    }

    if ($to === '') {
        throw new Exception('Recipient email is required.');
    }

    $pdfBinary = base64_decode($pdfBase64, true);

    if ($pdfBinary === false) {
        throw new Exception('Invalid PDF encoding.');
    }

    if (!str_ends_with(strtolower($filename), '.pdf')) {
        $filename .= '.pdf';
    }

    $tmpDir = __DIR__ . '/../tmp';

    if (!is_dir($tmpDir)) {
        if (!mkdir($tmpDir, 0775, true) && !is_dir($tmpDir)) {
            throw new Exception('Unable to create temporary directory.');
        }
    }

    if (!is_writable($tmpDir)) {
        throw new Exception('Temporary directory is not writable.');
    }

    $finalTmpFile = $tmpDir . '/inv_pdf_' . uniqid('', true) . '.pdf';

    if (file_put_contents($finalTmpFile, $pdfBinary) === false) {
        throw new Exception('Unable to write temporary PDF file.');
    }

    try {
        sendMailMessage(
            $to,
            'Your invoice PDF',
            '<p>Please find your invoice attached.</p>',
            $finalTmpFile,
            $filename
        );
    } finally {
        if (file_exists($finalTmpFile)) {
            @unlink($finalTmpFile);
        }
    }

    jsonResponse([
        'success' => true,
        'message' => 'PDF sent successfully.'
    ]);
} catch (Throwable $e) {
    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}