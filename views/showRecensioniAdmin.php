<?php
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<span>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</span>';
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}
$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/admin/showRecensioniAdmin.html');
preg_match('/<!-- ROW_TEMPLATE_START -->(.*?)<!-- ROW_TEMPLATE_END -->/s', $template, $rowTemplateMatch);
$rowTemplate = trim((string) ($rowTemplateMatch[1] ?? ''));
$template = preg_replace('/<!-- ROW_TEMPLATE_START -->.*?<!-- ROW_TEMPLATE_END -->/s', '', $template, 1);

$searchForm = renderSearchForm([
    'action' => 'recensioni.php',
    'class' => 'form admin-search-form',
    'submitLabel' => 'Applica filtri',
    'resetHref' => 'recensioni.php',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'label' => 'Cerca',
            'value' => (string) ($filtri['cerca'] ?? ''),
            'placeholder' => 'Username, libro o testo',
        ],
        [
            'type' => 'number',
            'name' => 'voto',
            'label' => 'Voto',
            'value' => (string) ($filtri['voto'] ?? ''),
            'min' => 0,
            'max' => 5,
            'step' => 1,
        ],
        [
            'type' => 'select',
            'name' => 'stato_recensione',
            'label' => 'Stato recensione',
            'selected' => (string) ($filtri['stato_recensione'] ?? 'tutte'),
            'options' => [
                'tutte' => 'Tutte',
                'visibile' => 'Visibile',
                'censurata' => 'Censurata',
            ],
        ],
    ],
]);

$start = $totalRecensioni > 0 ? (($pagina - 1) * $resultsPerPage) + 1 : 0;
$end = $totalRecensioni > 0 ? min($start + count($recensioni) - 1, $totalRecensioni) : 0;
$resultsInfo = renderResultsInfo($totalRecensioni, $start, $end, 'recensione', 'recensioni', 'Nessuna recensione trovata.');
$pagination = renderPagination('recensioni.php', [
    'cerca' => (string) ($filtri['cerca'] ?? ''),
    'voto' => (string) ($filtri['voto'] ?? ''),
    'stato_recensione' => (string) ($filtri['stato_recensione'] ?? 'tutte'),
], $pagina, $totalPagine, 'Navigazione pagine risultati', 'Pagina precedente', 'Pagina successiva');
    
if (empty($recensioni)) {
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
foreach ($recensioni as $recensione) {
    $id = htmlspecialchars((string) $recensione['id'], ENT_QUOTES, 'UTF-8');
    $libroIdRow = htmlspecialchars((string) ($recensione['libro_id'] ?? 0), ENT_QUOTES, 'UTF-8');
    $censuraLabel = $recensione['censura'] ? 'Mostra' : 'Censura';

    $tableRows .= strtr($rowTemplate, [
        '[ID]' => $id,
        '[UTENTE]' => '<a href="utenti.php?cerca=' . rawurlencode((string) $recensione['username']) . '">' . htmlspecialchars((string) $recensione['username'], ENT_QUOTES, 'UTF-8') . '</a>',
        '[LIBRO]' => '<a href="../dettaglio-libro.php?id=' . $libroIdRow . '">' . htmlspecialchars((string) $recensione['libro_titolo'], ENT_QUOTES, 'UTF-8') . '</a>',
        '[VOTO]' => htmlspecialchars((string) $recensione['valutazione'], ENT_QUOTES, 'UTF-8'),
        '[TESTO]' => htmlspecialchars((string) mb_substr($recensione['testo'], 0, 80), ENT_QUOTES, 'UTF-8') . '...',
        '[DATA]' => htmlspecialchars((string) $recensione['data'], ENT_QUOTES, 'UTF-8'),
        '[STATO]' => $recensione['censura'] ? 'Censurata' : 'Visibile',
        '[AZIONI]' => '<div class="table-actions-list"><form class="table-action-form" action="recensioni.php" method="post"><input type="hidden" name="recensione_id" value="' . $id . '"><button class="table-action" type="submit" name="azione" value="censura">' . $censuraLabel . '</button></form><a class="table-action" href="elimina-recensione.php?id=' . $id . '">Elimina</a></div>',
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
