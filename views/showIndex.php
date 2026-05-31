<?php
if (empty($categorie)) {
    $listaCategorie = '<p>Nessuna categoria disponibile</p>';
} else {
    $listaCategorie = '<ul>';
    foreach ($categorie as $categoria) {
        $listaCategorie .= '<li><a href="catalogo.php?categoria=' . urlencode($categoria['categoria']) . '">' . htmlspecialchars($categoria['categoria'], ENT_QUOTES, 'UTF-8') . '</a></li>';
    }
    $listaCategorie .= '</ul>';
}

if (empty($libriRecenti)) {
    $listaLibriRecenti = '<p>Nessun libro disponibile al momento</p>';
} else {
    $listaLibriRecenti = '<ul>';
    foreach ($libriRecenti as $libro) {
        $listaLibriRecenti .= '<li>';
        $listaLibriRecenti .= '<article>';
        $listaLibriRecenti .= '<h4><a href="dettaglio-libro.php?id=' . (int) $libro['id'] . '">' . htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8') . '</a></h4>';
        $listaLibriRecenti .= '<p>' . htmlspecialchars($libro['autore'], ENT_QUOTES, 'UTF-8') . '</p>';
        $listaLibriRecenti .= '<p>' . htmlspecialchars($libro['categoria'], ENT_QUOTES, 'UTF-8') . '</p>';
        $listaLibriRecenti .= '</article>';
        $listaLibriRecenti .= '</li>';
    }
    $listaLibriRecenti .= '</ul>';
}

$template = file_get_contents(__DIR__ . '/../html/showIndex.html');

$searchForm = renderSearchForm([
    'action' => 'catalogo.php',
    'method' => 'get',
    'id' => 'ricerca-rapida',
    'class' => 'search-hero',
    'submitLabel' => 'Cerca',
    // No reset link on the homepage quick search
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'label' => 'Cerca per titolo',
            'value' => '',
            'placeholder' => 'Es. Il nome della rosa',
        ],
    ],
]);

$template = str_replace('[LISTA_CATEGORIE]',     $listaCategorie,    $template);
$template = str_replace('[LISTA_LIBRI_RECENTI]', $listaLibriRecenti, $template);
$template = str_replace('[SEARCH_FORM]', $searchForm, $template);
echo $template;
