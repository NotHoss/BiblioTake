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

$filtroCercaValue = htmlspecialchars((string) ($filtri['cerca'] ?? ''), ENT_QUOTES, 'UTF-8');
$filtroAutoreValue = htmlspecialchars((string) ($filtri['autore'] ?? ''), ENT_QUOTES, 'UTF-8');
$filtroAnnoValue = ($filtri['anno'] ?? '') !== '' ? (int) $filtri['anno'] : '';
$statoLibriValue = (string) ($filtri['stato_libri'] ?? 'tutti');
$opzioniStatoLibri = '';
foreach ([
    'tutti' => 'Tutti',
    'disponibili' => 'Disponibile',
    'prestati' => 'Prestato',
] as $value => $label) {
    $selected = ($statoLibriValue === $value) ? ' selected' : '';
    $opzioniStatoLibri .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
}

$opzioniCategoria = '<option value="">Tutte</option>';
foreach (($categorie ?? []) as $cat) {
    $catValue = htmlspecialchars((string) $cat, ENT_QUOTES, 'UTF-8');
    $selected = (($filtri['categoria'] ?? '') === $cat) ? ' selected' : '';
    $opzioniCategoria .= '<option value="' . $catValue . '"' . $selected . '>' . $catValue . '</option>';
}

$opzioniTag = '<option value="">Tutti</option>';
foreach (($tagsDisponibili ?? []) as $tag) {
    $tagValue = htmlspecialchars((string) ($tag['nome'] ?? ''), ENT_QUOTES, 'UTF-8');
    if ($tagValue === '') {
        continue;
    }
    $selected = (($filtri['tag'] ?? '') === (string) $tag['nome']) ? ' selected' : '';
    $opzioniTag .= '<option value="' . $tagValue . '"' . $selected . '>' . $tagValue . '</option>';
}

$searchForm = '
<form method="get" action="libri.php" class="admin-search-form">
    <div>
        <label for="cerca">Cerca</label>
        <input type="search" id="cerca" name="cerca" value="' . $filtroCercaValue . '" placeholder="ID, ISBN o titolo del libro">
    </div>
    <div>
        <label for="autore">Autore</label>
        <input type="text" id="autore" name="autore" value="' . $filtroAutoreValue . '">
    </div>
    <div>
        <label for="categoria">Categoria</label>
        <select id="categoria" name="categoria">' . $opzioniCategoria . '</select>
    </div>
    <div>
        <label for="tag">Tag</label>
        <select id="tag" name="tag">' . $opzioniTag . '</select>
    </div>
    <div>
        <label for="anno">Anno</label>
        <input type="number" id="anno" name="anno" min="0" max="' . date('Y') . '" value="' . htmlspecialchars((string) $filtroAnnoValue, ENT_QUOTES, 'UTF-8') . '">
    </div>
    <div>
        <label for="stato_libri">Stato libro</label>
        <select id="stato_libri" name="stato_libri">' . $opzioniStatoLibri . '</select>
    </div>
    <div>
        <button type="submit">Filtra</button>
        <a href="libri.php">Reset</a>
    </div>
</form>';

$start = $totalLibri > 0 ? (($pagina - 1) * $resultsPerPage) + 1 : 0;
$end = $totalLibri > 0 ? min($start + count($libri) - 1, $totalLibri) : 0;
$resultsInfo = '';
if ($totalLibri === 0) {
    $resultsInfo = '<p>Nessun libro trovato.</p>';
} else {
    $resultsInfo = '<p>Mostrati ' . $start . '-' . $end . ' di ' . $totalLibri . ' libri.</p>';
}

$pagination = '';
if ($totalPagine > 1) {
    $pagination .= '<nav aria-label="Paginazione libri"><ul>';
    if ($pagina > 1) {
        $pagination .= '<li><a href="libri.php?' . http_build_query(array_merge($filtri, ['page' => $pagina - 1])) . '">Precedente</a></li>';
    }
    for ($i = 1; $i <= $totalPagine; $i++) {
        if ($i === $pagina) {
            $pagination .= '<li aria-current="page">' . $i . '</li>';
        } else {
            $pagination .= '<li><a href="libri.php?' . http_build_query(array_merge($filtri, ['page' => $i])) . '">' . $i . '</a></li>';
        }
    }
    if ($pagina < $totalPagine) {
        $pagination .= '<li><a href="libri.php?' . http_build_query(array_merge($filtri, ['page' => $pagina + 1])) . '">Successiva</a></li>';
    }
    $pagination .= '</ul></nav>';
}

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



