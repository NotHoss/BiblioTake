<?php
$adminViewMode = $adminViewMode ?? 'prestiti';

$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<span>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</span>';
}
$successMsg = '';
if (!empty($message)) {
    $successMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}
$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/admin/showPrestitiAdmin.html');
preg_match('/<!-- ROW_TEMPLATE_START -->(.*?)<!-- ROW_TEMPLATE_END -->/s', $template, $rowTemplateMatch);
$rowTemplate = trim((string) ($rowTemplateMatch[1] ?? ''));
$template = preg_replace('/<!-- ROW_TEMPLATE_START -->.*?<!-- ROW_TEMPLATE_END -->/s', '', $template, 1);

$searchForm = renderSearchForm([
    'action' => 'prestiti-utente.php',
    'class' => 'form admin-search-form',
    'submitLabel' => 'Applica filtri',
    'resetHref' => 'prestiti-utente.php',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'label' => 'Cerca',
            'value' => (string) ($filtri['cerca'] ?? ''),
            'placeholder' => 'ID, Username, titolo o autore',
        ],
        [
            'type' => 'select',
            'name' => 'stato',
            'label' => 'Stato',
            'selected' => (string) ($filtri['stato'] ?? 'tutti'),
            'options' => [
                'tutti' => 'Tutti',
                'attivo' => 'Attivo',
                'in_ritardo' => 'In ritardo',
                'concluso' => 'Concluso',
            ],
        ],
    ],
]);

$start = $totalPrestiti > 0 ? (($pagina - 1) * $resultsPerPage) + 1 : 0;
$end = $totalPrestiti > 0 ? min($start + count($prestiti) - 1, $totalPrestiti) : 0;
$resultsInfo = renderResultsInfo($totalPrestiti, $start, $end, 'prestito', 'prestiti', 'Nessun prestito trovato.');
$pagination = renderPagination('prestiti-utente.php', [
    'cerca' => (string) ($filtri['cerca'] ?? ''),
    'stato' => (string) ($filtri['stato'] ?? 'tutti'),
], $pagina, $totalPagine, 'Navigazione pagine risultati', 'Pagina precedente', 'Pagina successiva');
$cercaSafe = htmlspecialchars((string) ($filtri['cerca'] ?? ''), ENT_QUOTES, 'UTF-8');

if ($utenteId >= 0) {
    $modificaPrestitoDisplay = 'class="none"';
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
        $emptyPrestitiMessage = '';
        $tableDisplay = 'class="none"';
    } else {
        foreach ($prestiti as $prestito) {
            $id = htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8');
            $username = htmlspecialchars((string) ($prestito['username'] ?? ''), ENT_QUOTES, 'UTF-8');
            $usernameLink = '<a href="utenti.php?cerca=' . rawurlencode((string) ($prestito['username'] ?? '')) . '">' . $username . '</a>';
            $titolo = htmlspecialchars((string) $prestito['libro_titolo'], ENT_QUOTES, 'UTF-8');
            $libroLink = '<a href="../dettaglio-libro.php?id=' . htmlspecialchars((string) ($prestito['libro_id'] ?? 0), ENT_QUOTES, 'UTF-8') . '">' . $titolo . '</a>';
            $statoRaw = (string) $prestito['stato'];
            $stato = [
                'attivo' => 'Attivo',
                'in_ritardo' => 'In ritardo',
                'concluso' => 'Concluso',
            ][$statoRaw] ?? htmlspecialchars($statoRaw, ENT_QUOTES, 'UTF-8');
            $inizio = formatDateForAdminDisplay($prestito['data_inizio']);
            $fine = formatDateForAdminDisplay($prestito['data_fine']);
            $utenteIdSafe = htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8');
            
            $statoPrestito = $statoRaw;

            // Link-based actions for modify/delete (dedicated pages)
            $modificaLink = '<a class="table-action" href="modifica-prestito.php?prestito_id=' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">Modifica</a>';
            $eliminaLink = '<a class="table-action" href="elimina-prestito.php?prestito_id=' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">Elimina</a>';

            // Actions that require POST remain as inline form (concludi/proroga)
            $postActions = '';
            if ($statoPrestito === 'attivo') {
                $postActions = '<div class="table-action-row"><button class="table-action" type="submit" name="azione" value="concludi">Concludi</button><button class="table-action" type="submit" name="azione" value="proroga">Proroga</button></div>';
            } elseif ($statoPrestito === 'in_ritardo') {
                $postActions = '<div class="table-action-row"><button class="table-action" type="submit" name="azione" value="concludi">Concludi</button></div>';
            }

            $tableActions = '<div class="table-action">'
                . '<div class="table-action-row">' . $modificaLink . $eliminaLink . '</div>'
                . '<form class="table-action-form" method="post"><input type="hidden" name="prestito_id" value="' . $id . '"><input type="hidden" name="utente_id" value="' . $utenteIdSafe . '"><input type="hidden" name="cerca" value="' . $cercaSafe . '">' . $postActions . '</form>'
                . '</div>';

            $tableRows .= strtr($rowTemplate, [
                '[ID]' => $id,
                '[UTENTE]' => $usernameLink,
                '[LIBRO]' => $libroLink,
                '[STATO]' => $stato,
                '[INIZIO]' => $inizio,
                '[FINE]' => $fine,
                '[AZIONI]' => $tableActions,
            ]) . "\n";
        }
    }

    echo strtr($template, array_merge([
        '[SEARCH_FORM]' => $searchForm,
        '[RESULTS_INFO]' => $resultsInfo,
        '[MESSAGES]' => $messages,
        '[PRESTITI_CONTENT_DISPLAY]' => '',
        '[MODIFICA_PRESTITO_DISPLAY]' => $modificaPrestitoDisplay,
        '[EMPTY_PRESTITI_MESSAGE]' => $emptyPrestitiMessage,
        '[TABLE_DISPLAY]' => $tableDisplay,
        '[TABLE_ROWS]' => $tableRows,
        '[PAGINATION]' => $pagination,
    ], $prestitoEn));

} else {
    echo strtr($template, [
        '[SEARCH_FORM]' => $searchForm,
        '[RESULTS_INFO]' => $resultsInfo,
        '[MESSAGES]' => $messages,
        '[PRESTITI_CONTENT_DISPLAY]' => 'class="none"',
        '[MODIFICA_PRESTITO_DISPLAY]' => 'class="none"',
        '[PRESTITO_IN_MODIFICA_ID]' => '',
        '[UTENTE_ID]' => '',
        '[DATA_INIZIO]' => '',
        '[DATA_FINE]' => '',
        '[STATO_OPTIONS]' => '',
        '[LIBRO_ID]' => '',
        '[NUOVO_UTENTE_ID]' => '',
        '[EMPTY_PRESTITI_MESSAGE]' => '',
        '[TABLE_DISPLAY]' => 'class="none"',
        '[TABLE_ROWS]' => '',
        '[PAGINATION]' => $pagination,
    ]);
}
