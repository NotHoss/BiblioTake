<?php
// Expects: $prestitoInModifica (array), $message, $errorMessage
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<span>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</span>';
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}
$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/admin/showModificaPrestitoAdmin.html');

if (empty($prestitoInModifica)) {
    echo strtr($template, [
        '[MESSAGES]' => $messages . '<p>Prestito non trovato.</p>',
        '[PRESTITO_IN_MODIFICA_ID]' => '',
        '[UTENTE_ID]' => '',
        '[DATA_INIZIO]' => '',
        '[DATA_FINE]' => '',
        '[STATO_OPTIONS]' => '',
        '[LIBRO_ID]' => '',
        '[NUOVO_UTENTE_ID]' => '',
        '[PRESTITO_SUMMARY]' => '',
        '[LIBRO_HIDDEN]' => '',
        '[UTENTE_HIDDEN]' => '',
    ]);
    return;
}

$statiPrestito = ['attivo', 'in_ritardo', 'concluso'];
$statiOptions = '';
foreach ($statiPrestito as $stato) {
    $sel = ($prestitoInModifica['stato'] === $stato) ? ' selected' : '';
    $label = [
        'attivo' => 'Attivo',
        'in_ritardo' => 'In ritardo',
        'concluso' => 'Concluso',
    ][$stato] ?? $stato;
    $statiOptions .= '<option value="' . htmlspecialchars($stato, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
}

$prestitoSummary = '<ul >'
    . '<li><strong>ID</strong>: ' . htmlspecialchars((string) $prestitoInModifica['id'], ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Stato</strong>: ' . htmlspecialchars(["attivo" => 'Attivo', "in_ritardo" => 'In ritardo', "concluso" => 'Concluso'][$prestitoInModifica['stato']] ?? (string) $prestitoInModifica['stato'], ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Libro</strong>: ' . htmlspecialchars((string) ($prestitoInModifica['libro_titolo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Utente</strong>: ' . htmlspecialchars((string) ($prestitoInModifica['username'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '</ul>';

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[PRESTITO_IN_MODIFICA_ID]' => htmlspecialchars((string) $prestitoInModifica['id'], ENT_QUOTES, 'UTF-8'),
    '[UTENTE_ID]' => htmlspecialchars((string) $prestitoInModifica['utente_id'], ENT_QUOTES, 'UTF-8'),
    '[DATA_INIZIO]' => htmlspecialchars(formatDateTimeForInput($prestitoInModifica['data_inizio']), ENT_QUOTES, 'UTF-8'),
    '[DATA_FINE]' => htmlspecialchars(formatDateTimeForInput($prestitoInModifica['data_fine']), ENT_QUOTES, 'UTF-8'),
    '[STATO_OPTIONS]' => $statiOptions,
    '[LIBRO_ID]' => htmlspecialchars((string) $prestitoInModifica['libro_id'], ENT_QUOTES, 'UTF-8'),
    '[NUOVO_UTENTE_ID]' => htmlspecialchars((string) $prestitoInModifica['utente_id'], ENT_QUOTES, 'UTF-8'),
    '[TESTO_MODIFICA]' => 'Stai modificando il prestito:',
    '[TESTO_DETTAGLI]' => $prestitoSummary,
    '[PRESTITO_SUMMARY]' => $prestitoSummary,
    '[LIBRO_HIDDEN]' => '<input type="hidden" name="libro_id" value="' . htmlspecialchars((string) $prestitoInModifica['libro_id'], ENT_QUOTES, 'UTF-8') . '">',
    '[UTENTE_HIDDEN]' => '<input type="hidden" name="nuovo_utente_id" value="' . htmlspecialchars((string) $prestitoInModifica['utente_id'], ENT_QUOTES, 'UTF-8') . '">',
]);
