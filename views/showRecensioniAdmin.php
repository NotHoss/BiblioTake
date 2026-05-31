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

$searchForm = renderSearchForm([
    'action' => 'recensioni.php',
    'class' => 'admin-search-form',
    'submitLabel' => 'Filtra recensioni',
    'resetHref' => 'recensioni.php',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'label' => 'Cerca',
            'value' => (string) ($filtri['cerca'] ?? ''),
            'placeholder' => 'Username, libro o testo',
        ],
    ],
]);

$start = $totalRecensioni > 0 ? (($pagina - 1) * $resultsPerPage) + 1 : 0;
$end = $totalRecensioni > 0 ? min($start + count($recensioni) - 1, $totalRecensioni) : 0;
$resultsInfo = renderResultsInfo($totalRecensioni, $start, $end, 'recensione', 'recensioni', 'Nessuna recensione trovata.');
$pagination = renderPagination('recensioni.php', [
    'cerca' => (string) ($filtri['cerca'] ?? ''),
], $pagina, $totalPagine, 'Paginazione recensioni');
    
if (empty($recensioni)) {
    echo strtr($template, [
        '[SEARCH_FORM]' => $searchForm,
        '[RESULTS_INFO]' => $resultsInfo,
        '[MESSAGES]' => $messages,
        '[EMPTY_MESSAGE]' => '<p>Nessuna recensione trovata.</p>',
        '[TABLE_DISPLAY]' => 'style="display:none;"',
        '[TABLE_ROWS]' => '',
        '[PAGINATION]' => $pagination,
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
            <form method=\"post\" style=\"display:inline\">\n                <input type=\"hidden\" name=\"recensione_id\" value=\"{$id}\">\n                <input type=\"hidden\" name=\"utente_id\" value=\"{$utenteIdSafe}\">\n                <button type=\"submit\" name=\"azione\" value=\"censura\">{$censuraLabel}</button>
            </form>
            <a href=\"elimina-recensione.php?id={$id}&utente_id={$utenteIdSafe}\">Elimina</a>
        </td>
    </tr>\n";
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
