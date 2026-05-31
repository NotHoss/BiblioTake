<?php
$adminViewMode = $adminViewMode ?? 'list';

$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
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
    'class' => 'admin-search-form',
    'submitLabel' => 'Filtra',
    'resetHref' => 'libri.php',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'label' => 'Cerca',
            'value' => (string) ($filtri['cerca'] ?? ''),
            'placeholder' => 'ID, ISBN o titolo del libro',
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

$pagination = renderPagination('libri.php', $filtri, $pagina, $totalPagine, 'Paginazione libri');

// List mode only — create/edit/delete moved to dedicated views
$template = file_get_contents(__DIR__ . '/../html/admin/showLibriAdmin.html');

if (empty($libri)) {
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
foreach ($libri as $libroRow) {
    $id = htmlspecialchars((string) $libroRow['id'], ENT_QUOTES, 'UTF-8');
    $isbn = htmlspecialchars((string) $libroRow['codice_isbn'], ENT_QUOTES, 'UTF-8');
    $titolo = htmlspecialchars((string) $libroRow['titolo'], ENT_QUOTES, 'UTF-8');
    $autore = htmlspecialchars((string) $libroRow['autore'], ENT_QUOTES, 'UTF-8');
    $anno = htmlspecialchars((string) $libroRow['anno'], ENT_QUOTES, 'UTF-8');
    $categoria = htmlspecialchars((string) $libroRow['categoria'], ENT_QUOTES, 'UTF-8');
    $prestitiAttivi = (int) ($libroRow['prestiti_attivi'] ?? 0);
    $prestiti = 'Disponibile';
    $azioni = '<a href="modifica-libro.php?id=' . $id . '">Modifica</a> |
        <a href="elimina-libro.php?id=' . $id . '">Elimina</a>';
    if ($prestitiAttivi > 0) {
        $prestiti = 'Prestato';
            $prestitoId = (int) ($libroRow['prestito_id'] ?? 0);
            if ($prestitoId > 0) {
                $prestitoIdSafe = htmlspecialchars((string) $prestitoId, ENT_QUOTES, 'UTF-8');
                $azioni = '<a href="prestiti-utente.php?prestito_id=' . $prestitoIdSafe . '">Vai al prestito</a>';
            }
    }

    $tagsArr = getTagsByLibroId($conn, (int) $libroRow['id']);
    $tagsList = [];
    foreach ($tagsArr as $t) { $tagsList[] = htmlspecialchars((string) $t['nome'], ENT_QUOTES, 'UTF-8'); }
    $tagsHtml = implode(', ', $tagsList);

    $tableRows .= "<tr>
        <td>{$id}</td>
        <td>{$isbn}</td>
        <td>{$titolo}</td>
        <td>{$autore}</td>
        <td>{$anno}</td>
        <td>{$categoria}</td>
        <td>{$tagsHtml}</td>
        <td>{$prestiti}</td>
        <td>{$azioni}</td>
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



