<?php
$adminViewMode = $adminViewMode ?? 'list';

$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}
$messages = $errorMsg . $successMsg;

if ($adminViewMode === 'prestiti') {
    $template = file_get_contents(__DIR__ . '/../html/admin/prestiti-utente.html');
    
    $utentiOptions = '';
    foreach ($utenti as $utente) {
        $selected = ($utenteId === (int) $utente['id']) ? 'selected' : '';
        $val = htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8');
        $utentiOptions .= "<option value=\"{$val}\" {$selected}>{$label}</option>";
    }

    if ($utenteId > 0) {
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
        
        if (!empty($prestitoInModifica)) {
            $modificaPrestitoDisplay = '';
            $statiPrestito = ['attivo', 'in_ritardo', 'concluso'];
            $statiOptions = '';
            foreach ($statiPrestito as $stato) {
                $sel = ($prestitoInModifica['stato'] === $stato) ? 'selected' : '';
                $statiOptions .= '<option value="' . htmlspecialchars($stato, ENT_QUOTES, 'UTF-8') . '" ' . $sel . '>' . htmlspecialchars($stato, ENT_QUOTES, 'UTF-8') . '</option>';
            }
            
            $prestitoEn = [
                '[PRESTITO_IN_MODIFICA_ID]' => htmlspecialchars((string) $prestitoInModifica['id'], ENT_QUOTES, 'UTF-8'),
                '[UTENTE_ID]' => htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8'),
                '[DATA_INIZIO]' => htmlspecialchars(formatDateTimeForInput($prestitoInModifica['data_inizio']), ENT_QUOTES, 'UTF-8'),
                '[DATA_FINE]' => htmlspecialchars(formatDateTimeForInput($prestitoInModifica['data_fine']), ENT_QUOTES, 'UTF-8'),
                '[STATO_OPTIONS]' => $statiOptions,
                '[LIBRO_ID]' => htmlspecialchars((string) $prestitoInModifica['libro_id'], ENT_QUOTES, 'UTF-8'),
                '[NUOVO_UTENTE_ID]' => htmlspecialchars((string) $prestitoInModifica['utente_id'], ENT_QUOTES, 'UTF-8'),
            ];
        }

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
                $actions = '';
                if ($statoPrestito === 'attivo') {
                    $actions = '
                        <button type="submit" name="azione" value="modifica">Modifica</button>
                        <button type="submit" name="azione" value="elimina">Elimina</button>
                        <button type="submit" name="azione" value="concludi">Concludi</button>
                        <button type="submit" name="azione" value="proroga">Proroga</button>
                    ';
                } elseif ($statoPrestito === 'in_ritardo') {
                    $actions = '
                        <button type="submit" name="azione" value="modifica">Modifica</button>
                        <button type="submit" name="azione" value="elimina">Elimina</button>
                        <button type="submit" name="azione" value="concludi">Concludi</button>
                    ';
                } else {
                    $actions = '
                        <button type="submit" name="azione" value="modifica">Modifica</button>
                        <button type="submit" name="azione" value="elimina">Elimina</button>
                    ';
                }

                $tableRows .= "<tr>
                    <td>{$id}</td>
                    <td>{$titolo}</td>
                    <td>{$stato}</td>
                    <td>{$inizio}</td>
                    <td>{$fine}</td>
                    <td>
                        <form method=\"post\">
                            <input type=\"hidden\" name=\"prestito_id\" value=\"{$id}\">
                            <input type=\"hidden\" name=\"utente_id\" value=\"{$utenteIdSafe}\">
                            {$actions}
                        </form>
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
} elseif ($adminViewMode === 'reviews') {
    $template = file_get_contents(__DIR__ . '/../html/admin/showRecensioni.html');
    
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
} else {
    // List mode
    $template = file_get_contents(__DIR__ . '/../html/admin/showUtenti.html');
    
    if (empty($utenti)) {
        echo strtr($template, [
            '[MESSAGES]' => $messages,
            '[EMPTY_MESSAGE]' => '<p>Nessun utente trovato.</p>',
            '[TABLE_DISPLAY]' => 'style="display:none;"',
            '[TABLE_ROWS]' => '',
        ]);
        return;
    }

    $tableRows = '';
    foreach ($utenti as $utente) {
        $id = htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8');
        $username = htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars((string) $utente['email'], ENT_QUOTES, 'UTF-8');
        $ruolo = ((string) $utente['ruolo'] === 'admin') ? 'Amministratore' : 'Utente';
        $attivo = ((int) $utente['attivo'] === 1) ? 'Sì' : 'No';
        $prestitiT = htmlspecialchars((string) $utente['prestiti_totali'], ENT_QUOTES, 'UTF-8');
        $prestitiA = htmlspecialchars((string) $utente['prestiti_attivi'], ENT_QUOTES, 'UTF-8');
        
        $rawPath = (string) ($utente['foto_profilo'] ?? '');
        $fotoSrc = '';
        if ($rawPath === '') {
            $fotoSrc = '/BiblioTake/' . ltrim(DEFAULT_AVATAR, '/');
        } elseif (preg_match('#^(https?://|/)#i', $rawPath)) {
            $fotoSrc = $rawPath;
        } else {
            $fotoSrc = '/BiblioTake/' . ltrim($rawPath, '/');
        }
        $fotoSrcSafe = htmlspecialchars($fotoSrc, ENT_QUOTES, 'UTF-8');

        $tableRows .= "<tr>
            <td>{$id}</td>
            <td>
                <img src=\"{$fotoSrcSafe}\" alt=\"Foto profilo di {$username}\" width=\"48\" height=\"48\">
            </td>
            <td>{$username}</td>
            <td>{$email}</td>
            <td>{$ruolo}</td>
            <td>{$attivo}</td>
            <td>{$prestitiT}</td>
            <td>{$prestitiA}</td>
            <td>
                <a href=\"prestiti-utente.php?utente_id={$id}\">Prestiti</a>
                |
                <a href=\"recensioni.php?utente_id={$id}\">Recensioni</a>
            </td>
        </tr>\n";
    }

    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[EMPTY_MESSAGE]' => '',
        '[TABLE_DISPLAY]' => '',
        '[TABLE_ROWS]' => $tableRows,
    ]);
}


