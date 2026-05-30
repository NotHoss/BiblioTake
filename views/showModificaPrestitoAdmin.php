<?php
// Expects: $prestitoInModifica (array), $message, $errorMessage
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
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
    ]);
    return;
}

$statiPrestito = ['attivo', 'in_ritardo', 'concluso'];
$statiOptions = '';
foreach ($statiPrestito as $stato) {
    $sel = ($prestitoInModifica['stato'] === $stato) ? ' selected' : '';
    $statiOptions .= '<option value="' . htmlspecialchars($stato, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($stato, ENT_QUOTES, 'UTF-8') . '</option>';
}

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[PRESTITO_IN_MODIFICA_ID]' => htmlspecialchars((string) $prestitoInModifica['id'], ENT_QUOTES, 'UTF-8'),
    '[UTENTE_ID]' => htmlspecialchars((string) $prestitoInModifica['utente_id'], ENT_QUOTES, 'UTF-8'),
    '[DATA_INIZIO]' => htmlspecialchars(formatDateTimeForInput($prestitoInModifica['data_inizio']), ENT_QUOTES, 'UTF-8'),
    '[DATA_FINE]' => htmlspecialchars(formatDateTimeForInput($prestitoInModifica['data_fine']), ENT_QUOTES, 'UTF-8'),
    '[STATO_OPTIONS]' => $statiOptions,
    '[LIBRO_ID]' => htmlspecialchars((string) $prestitoInModifica['libro_id'], ENT_QUOTES, 'UTF-8'),
    '[NUOVO_UTENTE_ID]' => htmlspecialchars((string) $prestitoInModifica['utente_id'], ENT_QUOTES, 'UTF-8'),
]);
