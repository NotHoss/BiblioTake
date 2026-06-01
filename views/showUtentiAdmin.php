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

$filtri = $filtri ?? [];
$pagina = isset($pagina) ? (int) $pagina : 1;
$totalUtenti = isset($totalUtenti) ? (int) $totalUtenti : 0;
$totalPagine = isset($totalPagine) ? (int) $totalPagine : 1;
$resultsPerPage = isset($resultsPerPage) ? (int) $resultsPerPage : 10;

$searchForm = renderSearchForm([
    'action' => 'utenti.php',
    'class' => 'admin-search-form',
    'submitLabel' => 'Filtra',
    'resetHref' => 'utenti.php',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'label' => 'Cerca',
            'value' => (string) ($filtri['cerca'] ?? ''),
            'placeholder' => 'ID, username o email dell\'utente',
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

$pagination = renderPagination('utenti.php', $filtri, $pagina, $totalPagine, 'Paginazione utenti');

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
        '[TABLE_DISPLAY]' => 'style="display:none;"',
        '[TABLE_ROWS]' => '',
        '[PAGINATION]' => $pagination,
    ]);
    return;
}

$tableRows = '';
foreach ($utenti as $utente) {
    $azioni = [];
    if ((int) ($utente['prestiti_totali'] ?? 0) > 0) {
        $azioni[] = '<a href="prestiti-utente.php?cerca=' . rawurlencode((string) $utente['username']) . '">Prestiti</a>';
    }
    if ((int) ($utente['recensioni_totali'] ?? 0) > 0) {
        $azioni[] = '<a href="recensioni.php?cerca=' . rawurlencode((string) $utente['username']) . '">Recensioni</a>';
    }
    $tableRows .= strtr($rowTemplate, [
        '[USERNAME]' => htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8'),
        '[EMAIL]' => htmlspecialchars((string) $utente['email'], ENT_QUOTES, 'UTF-8'),
        '[PRESTITI_TOTALI]' => htmlspecialchars((string) ($utente['prestiti_totali'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[PRESTITI_ATTIVI]' => htmlspecialchars((string) ($utente['prestiti_attivi'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[RECENSIONI_TOTALI]' => htmlspecialchars((string) ($utente['recensioni_totali'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[STATO]' => ((int) ($utente['attivo'] ?? 0) === 1) ? 'Attivo' : 'Non attivo',
        '[AZIONI]' => empty($azioni) ? 'Nessuna azione disponibile' : implode(' | ', $azioni),
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

