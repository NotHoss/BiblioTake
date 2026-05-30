<?php
// Expects: $prestito (array) and $message / $errorMessage
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}
$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/admin/showEliminaPrestitoAdmin.html');

if (empty($prestito)) {
    echo strtr($template, [
        '[MESSAGES]' => $messages . '<p>Prestito non trovato.</p>',
        '[PRESTITO_ID]' => '',
        '[UTENTE_ID]' => '',
    ]);
    return;
}

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[PRESTITO_ID]' => htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8'),
    '[UTENTE_ID]' => htmlspecialchars((string) $prestito['utente_id'], ENT_QUOTES, 'UTF-8'),
]);
