<?php
$adminViewMode = $adminViewMode ?? 'prestiti';

$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}
$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/admin/showPrestitiAdmin.html');
    
// Build utenti select options; include option value 0 for 'Tutti gli utenti'
$utentiOptions = '<option value="0"' . ($utenteId === 0 ? ' selected' : '') . '>Tutti gli utenti</option>';
foreach ($utenti as $utente) {
    $selected = ($utenteId === (int) $utente['id']) ? 'selected' : '';
    $val = htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8');
    $label = htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8');
    $utentiOptions .= "<option value=\"{$val}\" {$selected}>{$label}</option>";
}

if ($utenteId >= 0) {
    $modificaPrestitoDisplay = 'style="display:none;"';
    $prestitoEn = [
        '[PRESTITO_IN_MODIFICA_ID]' => '',
        '[UTENTE_ID]' => htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8'),
        '[DATA_INIZIO]' => '',
        '[DATA_FINE]' => '',
        '[STATO_OPTIONS]' => '',
        '[LIBRO_ID]' => '',
        '[NUOVO_UTENTE_ID]' => '',
    ];
    
    // Inline modification removed — use dedicated pages for modify/delete actions.

    $emptyPrestitiMessage = '';
    $tableDisplay = '';
    $tableRows = '';
    
    if (empty($prestiti)) {
        $emptyPrestitiMessage = '<p>Nessun prestito trovato.</p>';
        $tableDisplay = 'style="display:none;"';
    } else {
        foreach ($prestiti as $prestito) {
            $id = htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8');
            $titolo = htmlspecialchars((string) $prestito['libro_titolo'], ENT_QUOTES, 'UTF-8');
            $stato = htmlspecialchars((string) $prestito['stato'], ENT_QUOTES, 'UTF-8');
            $inizio = htmlspecialchars((string) $prestito['data_inizio'], ENT_QUOTES, 'UTF-8');
            $fine = htmlspecialchars((string) $prestito['data_fine'], ENT_QUOTES, 'UTF-8');
            $utenteIdSafe = htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8');
            
            $statoPrestito = (string) $prestito['stato'];

            // Link-based actions for modify/delete (dedicated pages)
            $modificaLink = '<a href="modifica-prestito.php?prestito_id=' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">Modifica</a>';
            $eliminaLink = '<a href="elimina-prestito.php?prestito_id=' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">Elimina</a>';

            // Actions that require POST remain as inline form (concludi/proroga)
            $postActions = '';
            if ($statoPrestito === 'attivo') {
                $postActions = '<button type="submit" name="azione" value="concludi">Concludi</button> <button type="submit" name="azione" value="proroga">Proroga</button>';
            } elseif ($statoPrestito === 'in_ritardo') {
                $postActions = '<button type="submit" name="azione" value="concludi">Concludi</button>';
            }

            $tableRows .= "<tr>
                <td>{$id}</td>
                <td>{$titolo}</td>
                <td>{$stato}</td>
                <td>{$inizio}</td>
                <td>{$fine}</td>
                <td>
                    {$modificaLink} | {$eliminaLink}
                    <form method=\"post\" style=\"display:inline; margin-left:0.5rem;\">\n                        <input type=\"hidden\" name=\"prestito_id\" value=\"{$id}\">\n                        <input type=\"hidden\" name=\"utente_id\" value=\"{$utenteIdSafe}\">\n                        {$postActions}\n                    </form>
                </td>
            </tr>\n";
        }
    }

    echo strtr($template, array_merge([
        '[MESSAGES]' => $messages,
        '[UTENTI_OPTIONS]' => $utentiOptions,
        '[PRESTITI_CONTENT_DISPLAY]' => '',
        '[MODIFICA_PRESTITO_DISPLAY]' => $modificaPrestitoDisplay,
        '[EMPTY_PRESTITI_MESSAGE]' => $emptyPrestitiMessage,
        '[TABLE_DISPLAY]' => $tableDisplay,
        '[TABLE_ROWS]' => $tableRows,
    ], $prestitoEn));

} else {
    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[UTENTI_OPTIONS]' => $utentiOptions,
        '[PRESTITI_CONTENT_DISPLAY]' => 'style="display:none;"',
        '[MODIFICA_PRESTITO_DISPLAY]' => 'style="display:none;"',
        '[PRESTITO_IN_MODIFICA_ID]' => '',
        '[UTENTE_ID]' => '',
        '[DATA_INIZIO]' => '',
        '[DATA_FINE]' => '',
        '[STATO_OPTIONS]' => '',
        '[LIBRO_ID]' => '',
        '[NUOVO_UTENTE_ID]' => '',
        '[EMPTY_PRESTITI_MESSAGE]' => '',
        '[TABLE_DISPLAY]' => 'style="display:none;"',
        '[TABLE_ROWS]' => '',
    ]);
}
