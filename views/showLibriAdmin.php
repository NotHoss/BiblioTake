<?php
$adminViewMode = $adminViewMode ?? 'list';

$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = renderAdminStatusMessage($errorMessage, 'error');
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = renderAdminStatusMessage($successMessage, 'success');
}
$messages = $errorMsg . $successMsg;

$filtri = $filtri ?? [];
$pagina = isset($pagina) ? (int) $pagina : 1;
$totalLibri = isset($totalLibri) ? (int) $totalLibri : 0;
$totalPagine = isset($totalPagine) ? (int) $totalPagine : 1;
$resultsPerPage = isset($resultsPerPage) ? (int) $resultsPerPage : 20;

$filtroAnnoValue = ($filtri['anno'] ?? '') !== '' ? (int) $filtri['anno'] : '';

$opzioniCategoria = ['' => 'Tutte'];
foreach (($categorie ?? []) as $cat) {
    $opzioniCategoria[(string) $cat] = (string) $cat;
}

$opzioniTag = ['' => 'Tutti'];
foreach (($tagsDisponibili ?? []) as $tag) {
    $nome = (string) ($tag['nome'] ?? '');
    if ($nome === '') {
        continue;
    }
    $opzioniTag[$nome] = $nome;
}

$searchForm = renderSearchForm([
    'action' => 'libri.php',
    'class' => 'form admin-search-form',
    'submitLabel' => 'Applica filtri',
    'resetHref' => 'libri.php',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'label' => 'Cerca',
            'value' => (string) ($filtri['cerca'] ?? ''),
            'placeholder' => 'ISBN o titolo del libro',
        ],
        [
            'type' => 'text',
            'name' => 'autore',
            'label' => 'Autore',
            'value' => (string) ($filtri['autore'] ?? ''),
        ],
        [
            'type' => 'select',
            'name' => 'categoria',
            'label' => 'Categoria',
            'group_start' => true,
            'selected' => (string) ($filtri['categoria'] ?? ''),
            'options' => $opzioniCategoria,
        ],
        [
            'type' => 'select',
            'name' => 'tag',
            'label' => 'Tag',
            'selected' => (string) ($filtri['tag'] ?? ''),
            'options' => $opzioniTag,
        ],
        [
            'type' => 'number',
            'name' => 'anno',
            'label' => 'Anno',
            'value' => ($filtroAnnoValue !== '') ? (string) $filtroAnnoValue : '',
            'min' => 0,
            'max' => date('Y'),
        ],
        [
            'type' => 'select',
            'name' => 'stato_libri',
            'label' => 'Stato libro',
            'group_end' => true,
            'selected' => (string) ($filtri['stato_libri'] ?? 'tutti'),
            'options' => [
                'tutti' => 'Tutti',
                'disponibili' => 'Disponibile',
                'prestati' => 'Prestato',
            ],
        ],
    ],
]);

$start = $totalLibri > 0 ? (($pagina - 1) * $resultsPerPage) + 1 : 0;
$end = $totalLibri > 0 ? min($start + count($libri) - 1, $totalLibri) : 0;
$resultsInfo = '';
if ($totalLibri === 0) {
    $resultsInfo = '<p>Nessun libro trovato.</p>';
} else {
    $resultsInfo = '<p>Mostrati ' . $start . '-' . $end . ' di ' . $totalLibri . ' libri.</p>';
}

$pagination = renderPagination('libri.php', $filtri, $pagina, $totalPagine, 'Navigazione pagine risultati', 'Pagina precedente', 'Pagina successiva');
$returnUrl = getCurrentAdminReturnUrl('libri.php');
$returnParam = rawurlencode($returnUrl);

// List mode only — create/edit/delete moved to dedicated views
$template = file_get_contents(__DIR__ . '/../html/admin/showLibriAdmin.html');
preg_match('/<!-- ROW_TEMPLATE_START -->(.*?)<!-- ROW_TEMPLATE_END -->/s', $template, $rowTemplateMatch);
$rowTemplate = trim((string) ($rowTemplateMatch[1] ?? ''));
$template = preg_replace('/<!-- ROW_TEMPLATE_START -->.*?<!-- ROW_TEMPLATE_END -->/s', '', $template, 1);

if (empty($libri)) {
    echo strtr($template, [
        '[SEARCH_FORM]' => $searchForm,
        '[RESULTS_INFO]' => $resultsInfo,
        '[MESSAGES]' => $messages,
        '[EMPTY_MESSAGE]' => '',
        '[TABLE_DISPLAY]' => 'class="none"',
        '[TABLE_ROWS]' => '',
        '[PAGINATION]' => $pagination,
        '[RETURN_PARAM]' => $returnParam,
    ]);
    return;
}

$tableRows = '';
foreach ($libri as $libroRow) {
    $id = htmlspecialchars((string) $libroRow['id'], ENT_QUOTES, 'UTF-8');
    $titoloLibro = htmlspecialchars((string) $libroRow['titolo'], ENT_QUOTES, 'UTF-8');
    $prestitiAttivi = (int) ($libroRow['prestiti_attivi'] ?? 0);
    $prestiti = 'Disponibile';
    $azioni = '<div class="table-actions-list"><a class="table-action" href="modifica-libro.php?id=' . $id . '&return=' . $returnParam . '" aria-label="Modifica libro ' . $titoloLibro . '">Modifica</a>'
        . '<a class="table-action" href="elimina-libro.php?id=' . $id . '&return=' . $returnParam . '" aria-label="Elimina libro ' . $titoloLibro . '">Elimina</a></div>';
    if ($prestitiAttivi > 0) {
        $prestiti = 'Prestato';
            $prestitoId = (int) ($libroRow['prestito_id'] ?? 0);
            if ($prestitoId > 0) {
                $prestitoIdSafe = htmlspecialchars((string) $prestitoId, ENT_QUOTES, 'UTF-8');
                $azioni = '<div class="table-actions-list"><a class="table-action" href="prestiti-utente.php?prestito_id=' . $prestitoIdSafe . '" aria-label="Vai al prestito attivo del libro ' . $titoloLibro . '">Vai al prestito</a></div>';
            }
    }

    $isbn = htmlspecialchars((string) $libroRow['codice_isbn'], ENT_QUOTES, 'UTF-8');

    $tableRows .= strtr($rowTemplate, [
        '[ISBN]' => $isbn,
        '[TITOLO]' => $titoloLibro,
        '[AUTORE]' => htmlspecialchars((string) $libroRow['autore'], ENT_QUOTES, 'UTF-8'),
        '[ANNO]' => htmlspecialchars((string) $libroRow['anno'], ENT_QUOTES, 'UTF-8'),
        '[CATEGORIA]' => htmlspecialchars((string) $libroRow['categoria'], ENT_QUOTES, 'UTF-8'),
        '[STATO_LIBRO]' => $prestiti,
        '[AZIONI]' => $azioni,
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
    '[RETURN_PARAM]' => $returnParam,
]);



