<?php
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}
$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/admin/showRecensioniAdmin.html');
    
$utentiOptions = '';
foreach ($utenti as $utente) {
    $selected = ($utenteId === (int) $utente['id']) ? 'selected' : '';
    $val = htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8');
    $label = htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8');
    $utentiOptions .= "<option value=\"{$val}\" {$selected}>{$label}</option>";
}
    
if (empty($recensioni)) {
    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[UTENTI_OPTIONS]' => $utentiOptions,
        '[EMPTY_MESSAGE]' => '<p>Nessuna recensione trovata.</p>',
        '[TABLE_DISPLAY]' => 'style="display:none;"',
        '[TABLE_ROWS]' => '',
    ]);
    return;
}

$tableRows = '';
$utenteIdSafe = htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8');
foreach ($recensioni as $recensione) {
    $id = htmlspecialchars((string) $recensione['id'], ENT_QUOTES, 'UTF-8');
    $username = htmlspecialchars((string) $recensione['username'], ENT_QUOTES, 'UTF-8');
    $titolo = htmlspecialchars((string) $recensione['libro_titolo'], ENT_QUOTES, 'UTF-8');
    $voto = htmlspecialchars((string) $recensione['valutazione'], ENT_QUOTES, 'UTF-8');
    $testo = htmlspecialchars((string) mb_substr($recensione['testo'], 0, 80), ENT_QUOTES, 'UTF-8') . '...';
    $data = htmlspecialchars((string) $recensione['data'], ENT_QUOTES, 'UTF-8');
    $censurata = $recensione['censura'] ? 'Sì' : 'No';
    
    $censuraLabel = $recensione['censura'] ? 'Mostra' : 'Censura';

    $tableRows .= "<tr>
        <td>{$id}</td>
        <td>{$username}</td>
        <td>{$titolo}</td>
        <td>{$voto}</td>
        <td>{$testo}</td>
        <td>{$data}</td>
        <td>{$censurata}</td>
        <td>
            <form method=\"post\">
                <input type=\"hidden\" name=\"recensione_id\" value=\"{$id}\">
                <input type=\"hidden\" name=\"utente_id\" value=\"{$utenteIdSafe}\">
                <button type=\"submit\" name=\"azione\" value=\"censura\">{$censuraLabel}</button>
                <button type=\"submit\" name=\"azione\" value=\"elimina\">Elimina</button>
            </form>
        </td>
    </tr>\n";
}

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[UTENTI_OPTIONS]' => $utentiOptions,
    '[EMPTY_MESSAGE]' => '',
    '[TABLE_DISPLAY]' => '',
    '[TABLE_ROWS]' => $tableRows,
]);
