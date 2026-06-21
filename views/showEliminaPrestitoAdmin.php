<?php
// Expects: $prestito (array), $utenteId and $message / $errorMessage
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = renderAdminStatusMessage($errorMessage, 'error');
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = renderAdminStatusMessage($message, 'success');
}
$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/admin/showEliminaPrestitoAdmin.html');
$returnUrl = getSafeAdminReturnUrl('prestiti-utente.php');
$returnUrlSafe = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');

if (empty($prestito)) {
    echo strtr($template, [
        '[MESSAGES]' => $messages . renderAdminStatusMessage('Prestito non trovato.', 'error'),
        '[PRESTITO_ID]' => '',
        '[UTENTE_ID]' => '',
        '[LIBRO]' => '',
        '[UTENTE]' => '',
        '[SUMMARY]' => '',
        '[ANNULLA_HREF]' => $returnUrlSafe,
        '[RETURN_URL]' => $returnUrlSafe,
    ]);
    return;
}

$summary = '<ul class="admin-summary-list">'
    . '<li><strong>ID</strong>: ' . htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Utente</strong>: ' . htmlspecialchars((string) ($prestito['username'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Libro</strong>: ' . htmlspecialchars((string) ($prestito['libro_titolo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Stato</strong>: ' . htmlspecialchars((string) ($prestito['stato'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Data inizio</strong>: ' . formatDateDisplay($prestito['data_inizio'] ?? '') . '</li>'
    . '<li><strong>Data fine</strong>: ' . formatDateDisplay($prestito['data_fine'] ?? '') . '</li>'
    . '</ul>';

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[PRESTITO_ID]' => htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8'),
    '[UTENTE_ID]' => htmlspecialchars((string) $prestito['utente_id'], ENT_QUOTES, 'UTF-8'),
    '[LIBRO]' => htmlspecialchars((string) ($prestito['libro_titolo'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '[UTENTE]' => htmlspecialchars((string) ($prestito['username'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '[SUMMARY]' => $summary,
    '[ANNULLA_HREF]' => $returnUrlSafe,
    '[RETURN_URL]' => $returnUrlSafe,
]);
