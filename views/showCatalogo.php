<?php
$annoCorrente = date('Y');

$opzioniCategoria = ['' => 'Tutte le categorie'];
foreach ($categorie as $cat) {
    $opzioniCategoria[(string) $cat['categoria']] = (string) $cat['categoria'];
}

$opzioniTag = ['' => 'Tutti i tag'];
foreach ($tags as $t) {
    $opzioniTag[(string) $t['nome']] = (string) $t['nome'];
}

$searchForm = renderSearchForm([
    'action' => 'catalogo.php',
    'class' => 'form search-form',
    'submitLabel' => 'Applica filtri',
    'resetHref' => 'catalogo.php',
    'resetLabel' => 'Azzera filtri',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'cerca',
            'id' => 'cerca',
            'label' => 'Cerca',
            'value' => (string) ($filtri['cerca'] ?? ''),
            'placeholder' => 'Codice ISBN del libro o titolo',
        ],
        [
            'type' => 'select',
            'name' => 'categoria',
            'id' => 'categoria',
            'label' => 'Categoria',
            'selected' => (string) ($filtri['categoria'] ?? ''),
            'options' => $opzioniCategoria,
        ],
        [
            'type' => 'select',
            'name' => 'tag',
            'id' => 'tag',
            'label' => 'Tag',
            'selected' => (string) ($filtri['tag'] ?? ''),
            'options' => $opzioniTag,
        ],
        [
            'type' => 'text',
            'name' => 'autore',
            'id' => 'autore',
            'label' => 'Autore',
            'value' => (string) ($filtri['autore'] ?? ''),
        ],
        [
            'type' => 'number',
            'name' => 'anno',
            'id' => 'anno',
            'label' => 'Anno',
            'value' => ($filtri['anno'] !== '') ? (string) $filtri['anno'] : '',
            'min' => 1901,
            'max' => $annoCorrente,
        ],
        [
            'type' => 'checkbox',
            'name' => 'disponibile',
            'id' => 'disponibile',
            'label' => 'Solo disponibili',
            'checked' => ($filtri['disponibile'] === '1'),
        ],
        [
            'type' => 'select',
            'name' => 'ordine',
            'id' => 'ordine',
            'label' => 'Ordina per',
            'selected' => (string) ($filtri['ordine'] ?? ''),
            'options' => [
                '' => 'Più recenti',
                'valutazione' => 'Valutazione',
            ],
        ],
    ],
]);

//header conteggio risultati
if ($totalLibri === 0) {
    $risultatiHeader = 'Nessun risultato trovato';
} elseif ($totalLibri === 1) {
    $risultatiHeader = '1 libro trovato';
} else {
    $risultatiHeader = $totalLibri . ' libri trovati';
}

//contenuto risultati (lista + paginazione oppure messaggio vuoto)
if (empty($libri)) {
    $contenutoRisultati = '<p>Nessun libro corrisponde ai criteri di ricerca. <a href="catalogo.php">Mostra tutti i libri</a>.</p>';
} else {
    $listaLibri = '<ul id="lista-libri">';
    foreach ($libri as $libro) {
        $copertinaSrc = htmlspecialchars(!empty($libro['copertina']) ? $libro['copertina'] : 'images/place-holder.jpg', ENT_QUOTES, 'UTF-8');
        $copertinAlt  = htmlspecialchars('Copertina del libro ' . $libro['titolo'] . ' di ' . $libro['autore'], ENT_QUOTES, 'UTF-8');
        $titolo       = htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8');
        $autore       = htmlspecialchars($libro['autore'], ENT_QUOTES, 'UTF-8');
        $categoria    = htmlspecialchars($libro['categoria'], ENT_QUOTES, 'UTF-8');
        $anno         = (int) $libro['anno'];
        $libroId      = (int) $libro['id'];

        $valutazioneHtml = '';
        if ($libro['media_voti'] !== null) {
            $valutazione     = htmlspecialchars((string) $libro['media_voti'], ENT_QUOTES, 'UTF-8');
            $valutazioneHtml = '<p><strong>Valutazione:</strong> ' . $valutazione . ' su 5</p>';
        }

        $listaLibri .= '<li>';
        $listaLibri .= '<a href="dettaglio-libro.php?id=' . $libroId . '" class="libro-card">';
        $listaLibri .= '<img src="' . $copertinaSrc . '" alt="' . $copertinAlt . '" />';
        $listaLibri .= '<h4>' . $titolo . '</h4>';
        $listaLibri .= '<p><strong>Autore:</strong> ' . $autore . '</p>';
        $listaLibri .= '<p><strong>Categoria:</strong> ' . $categoria . '</p>';
        $listaLibri .= '<p><strong>Anno:</strong> <time datetime="' . $anno . '">' . $anno . '</time></p>';
        $listaLibri .= $valutazioneHtml;
        $listaLibri .= '</li>';
    }
    $listaLibri .= '</ul>';

    $paginazione = renderPagination('catalogo.php', $filtri, $pagina, $totalPagine, 'Navigazione pagine risultati', 'Pagina precedente', 'Pagina successiva');

    $contenutoRisultati = $listaLibri . $paginazione;
}

$template = file_get_contents(__DIR__ . '/../html/showCatalogo.html');
$template = str_replace('[SEARCH_FORM]',                 $searchForm,                $template);
$template = str_replace('[ANNO_CORRENTE]',               $annoCorrente,              $template);
$template = str_replace('[RISULTATI_HEADER]',            $risultatiHeader,           $template);
$template = str_replace('[CONTENUTO_RISULTATI]',         $contenutoRisultati,        $template);
echo $template;
