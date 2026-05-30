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

// The admin users page currently renders only the list view.
$template = file_get_contents(__DIR__ . '/../html/admin/showUtentiAdmin.html');

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

