<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/env.php';

use PHPMailer\PHPMailer\PHPMailer;

if (empty($_ENV['MAIL_HOST'])) {
    loadEnv(__DIR__ . '/../.env');
}

function makeMailer(): PHPMailer
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'] ?? '';
    $mail->Port = (int)($_ENV['MAIL_PORT'] ?? 587);
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['MAIL_USERNAME'] ?? '';
    $mail->Password = $_ENV['MAIL_PASSWORD'] ?? '';
    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';

    $mail->setFrom(
        $_ENV['MAIL_FROM_ADDRESS'] ?? '',
        $_ENV['MAIL_FROM_NAME'] ?? 'Factorator'
    );

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    return $mail;
}

function sendMailMessage(
    string $to,
    string $subject,
    string $htmlBody,
    ?string $attachmentPath = null,
    ?string $attachmentName = null
): void {
    $mail = makeMailer();

    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $htmlBody;
    $mail->AltBody = strip_tags($htmlBody);

    if ($attachmentPath && file_exists($attachmentPath)) {
        $mail->addAttachment(
            $attachmentPath,
            $attachmentName ?? basename($attachmentPath)
        );
    }

    $mail->send();
    function sendMailMessageWithBinaryAttachment(
    string $to,
    string $subject,
    string $htmlBody,
    string $binaryContent,
    string $attachmentName = 'document.pdf',
    string $mimeType = 'application/pdf'
): void {
    $mail = makeMailer();

    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $htmlBody;
    $mail->AltBody = strip_tags($htmlBody);

    $mail->addStringAttachment(
        $binaryContent,
        $attachmentName,
        'base64',
        $mimeType
    );

    $mail->send();
}
}