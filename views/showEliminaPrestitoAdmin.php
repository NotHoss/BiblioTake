<?php
// Expects: $prestito (array), $utenteId and $message / $errorMessage
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
        '[LIBRO]' => '',
        '[UTENTE]' => '',
        '[SUMMARY]' => '',
        '[ANNULLA_HREF]' => 'prestiti-utente.php?utente_id=' . (int) ($utenteId ?? 0),
    ]);
    return;
}

$summary = '<ul>'
    . '<li><strong>ID</strong>: ' . htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Utente</strong>: ' . htmlspecialchars((string) ($prestito['username'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Libro</strong>: ' . htmlspecialchars((string) ($prestito['libro_titolo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Stato</strong>: ' . htmlspecialchars((string) ($prestito['stato'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Data inizio</strong>: ' . htmlspecialchars((string) ($prestito['data_inizio'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Data fine</strong>: ' . htmlspecialchars((string) ($prestito['data_fine'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '</ul>';

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[PRESTITO_ID]' => htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8'),
    '[UTENTE_ID]' => htmlspecialchars((string) $prestito['utente_id'], ENT_QUOTES, 'UTF-8'),
    '[LIBRO]' => htmlspecialchars((string) ($prestito['libro_titolo'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '[UTENTE]' => htmlspecialchars((string) ($prestito['username'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '[SUMMARY]' => $summary,
    '[ANNULLA_HREF]' => 'prestiti-utente.php?utente_id=' . (int) ($prestito['utente_id'] ?? 0),
]);
