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

            // List mode only — prestiti/reviews moved to dedicated views
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
                            {$actions}

                        </form>

