<?php
$adminViewMode = $adminViewMode ?? 'list';

$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = renderAdminStatusMessage($errorMessage, 'error');
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = renderAdminStatusMessage($message, 'success');
}
$messages = $errorMsg . $successMsg;

$filtri = $filtri ?? [];
$pagina = isset($pagina) ? (int) $pagina : 1;
$totalUtenti = isset($totalUtenti) ? (int) $totalUtenti : 0;
$totalPagine = isset($totalPagine) ? (int) $totalPagine : 1;
$resultsPerPage = isset($resultsPerPage) ? (int) $resultsPerPage : 10;

$searchForm = renderSearchForm([
    'action' => 'utenti.php',
    'class' => 'form admin-search-form',
    'validate' => true,
    'submitLabel' => 'Applica filtri',
    'resetHref' => 'utenti.php',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'label' => 'Cerca',
            'value' => (string) ($filtri['cerca'] ?? ''),
            'placeholder' => 'ID, nome utente o indirizzo email dell\'utente',
        ],
        [
            'type' => 'select',
            'name' => 'stato_utenti',
            'label' => 'Stato utente',
            'selected' => (string) ($filtri['stato_utenti'] ?? 'tutti'),
            'options' => [
                'tutti' => 'Tutti',
                'attivi' => 'Attivo',
                'non_attivi' => 'Non attivo',
            ],
        ],
    ],
]);

$start = $totalUtenti > 0 ? (($pagina - 1) * $resultsPerPage) + 1 : 0;
$end = $totalUtenti > 0 ? min($start + count($utenti) - 1, $totalUtenti) : 0;
$resultsInfo = '';
if ($totalUtenti === 0) {
    $resultsInfo = '<p>Nessun utente trovato.</p>';
} else {
    $resultsInfo = '<p>Mostrati ' . $start . '-' . $end . ' di ' . $totalUtenti . ' utenti.</p>';
}

$pagination = renderPagination('utenti.php', $filtri, $pagina, $totalPagine, 'Navigazione pagine risultati', 'Pagina precedente', 'Pagina successiva');
$returnUrl = getCurrentAdminReturnUrl('utenti.php');
$returnUrlSafe = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');

// The admin users page currently renders only the list view.
$template = file_get_contents(__DIR__ . '/../html/admin/showUtentiAdmin.html');
preg_match('/<!-- ROW_TEMPLATE_START -->(.*?)<!-- ROW_TEMPLATE_END -->/s', $template, $rowTemplateMatch);
$rowTemplate = trim((string) ($rowTemplateMatch[1] ?? ''));
$template = preg_replace('/<!-- ROW_TEMPLATE_START -->.*?<!-- ROW_TEMPLATE_END -->/s', '', $template, 1);

if (empty($utenti)) {
    echo strtr($template, [
        '[SEARCH_FORM]' => $searchForm,
        '[RESULTS_INFO]' => $resultsInfo,
        '[MESSAGES]' => $messages,
        '[EMPTY_MESSAGE]' => '',
        '[TABLE_DISPLAY]' => 'class="none"',
        '[TABLE_ROWS]' => '',
        '[PAGINATION]' => $pagination,
    ]);
    return;
}

$tableRows = '';
foreach ($utenti as $utente) {
    $azioni = [];
    $utenteId = (int) ($utente['id'] ?? 0);
    $utenteIdSafe = htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8');
    $usernameRaw = (string) ($utente['username'] ?? '');
    $emailRaw = (string) ($utente['email'] ?? '');
    $usernameSafe = htmlspecialchars($usernameRaw, ENT_QUOTES, 'UTF-8');
    $prestitiAttivi = (int) ($utente['prestiti_attivi'] ?? 0);
    $utenteAttivo = (int) ($utente['attivo'] ?? 0) === 1;
    $deletedEmailSuffix = '@deleted.local';
    $deletedUsername = trim($usernameRaw);
    $utenteEliminato = !$utenteAttivo
        && (
            strcasecmp($deletedUsername, 'Utente Eliminato') === 0
            || preg_match('/^Utente\s+\d+\s+Eliminato$/i', $deletedUsername)
        )
        && substr(strtolower(trim($emailRaw)), -strlen($deletedEmailSuffix)) === $deletedEmailSuffix;

    if ((int) ($utente['prestiti_totali'] ?? 0) > 0) {
        $azioni[] = '<a class="table-action" href="prestiti-utente.php?utente_id=' . $utenteIdSafe . '" aria-label="Mostra prestiti di ' . $usernameSafe . '">Prestiti</a>';
    }
    if ((int) ($utente['recensioni_totali'] ?? 0) > 0) {
        $azioni[] = '<a class="table-action" href="recensioni.php?utente_id=' . $utenteIdSafe . '" aria-label="Mostra recensioni di ' . $usernameSafe . '">Recensioni</a>';
    }
    if ($utenteAttivo && $prestitiAttivi === 0) {
        $azioni[] = '<form class="table-action-form" action="' . $returnUrlSafe . '" method="post">'
            . '<input type="hidden" name="utente_id" value="' . $utenteIdSafe . '" />'
            . '<input type="hidden" name="return" value="' . $returnUrlSafe . '" />'
            . '<button class="table-action table-action-primary" type="submit" name="azione" value="disattiva" aria-label="Disattiva utente ' . $usernameSafe . '">Disattiva</button>'
            . '</form>';
    } elseif (!$utenteAttivo && !$utenteEliminato) {
        $azioni[] = '<form class="table-action-form" action="' . $returnUrlSafe . '" method="post">'
            . '<input type="hidden" name="utente_id" value="' . $utenteIdSafe . '" />'
            . '<input type="hidden" name="return" value="' . $returnUrlSafe . '" />'
            . '<button class="table-action table-action-primary" type="submit" name="azione" value="riattiva" aria-label="Riattiva utente ' . $usernameSafe . '">Riattiva</button>'
            . '</form>';
    }
    if (empty($azioni)) {
        $azioni[] = '<span class="table-action-unavailable">Nessuna azione</span>';
    }
    $tableRows .= strtr($rowTemplate, [
        '[USERNAME]' => $usernameSafe,
        '[EMAIL]' => htmlspecialchars($emailRaw, ENT_QUOTES, 'UTF-8'),
        '[PRESTITI_TOTALI]' => htmlspecialchars((string) ($utente['prestiti_totali'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[PRESTITI_ATTIVI]' => htmlspecialchars((string) $prestitiAttivi, ENT_QUOTES, 'UTF-8'),
        '[RECENSIONI_TOTALI]' => htmlspecialchars((string) ($utente['recensioni_totali'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[STATO]' => $utenteAttivo ? 'Attivo' : 'Non attivo',
        '[AZIONI]' => '<div class="table-actions-list">' . implode('', $azioni) . '</div>',
    ]) . "\n";
}

echo strtr($template, [
    '[SEARCH_FORM]' => $searchForm,
    '[RESULTS_INFO]' => $resultsInfo,
    '[MESSAGES]' => $messages,
    '[EMPTY_MESSAGE]' => '',
    '[TABLE_DISPLAY]' => '',
    '[TABLE_ROWS]' => $tableRows,
    '[PAGINATION]' => $pagination,
]);

