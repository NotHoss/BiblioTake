<?php
//valori semplici per il form
$filtroCercaValue            = htmlspecialchars($filtri['cerca'], ENT_QUOTES, 'UTF-8');
$filtroAutoreValue           = htmlspecialchars($filtri['autore'], ENT_QUOTES, 'UTF-8');
$annoCorrente                = date('Y');
$filtroAnnoValue             = $filtri['anno'] !== '' ? (int) $filtri['anno'] : '';
$disponibileChecked          = ($filtri['disponibile'] === '1') ? 'checked' : '';
$ordineValutazioneSelected   = ($filtri['ordine'] === 'valutazione') ? 'selected' : '';

//opzioni select categoria
$opzioniCategoria = '';
foreach ($categorie as $cat) {
    $catValue = htmlspecialchars($cat['categoria'], ENT_QUOTES, 'UTF-8');
    $selected = ($filtri['categoria'] === $cat['categoria']) ? ' selected' : '';
    $opzioniCategoria .= '<option value="' . $catValue . '"' . $selected . '>' . $catValue . '</option>';
}

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
        $listaLibri .= '<article class="libro-card">';
        $listaLibri .= '<img src="' . $copertinaSrc . '" alt="' . $copertinAlt . '">';
        $listaLibri .= '<h4><a href="dettaglio-libro.php?id=' . $libroId . '">' . $titolo . '</a></h4>';
        $listaLibri .= '<p><strong>Autore:</strong> ' . $autore . '</p>';
        $listaLibri .= '<p><strong>Categoria:</strong> ' . $categoria . '</p>';
        $listaLibri .= '<p><strong>Anno:</strong> <time datetime="' . $anno . '">' . $anno . '</time></p>';
        $listaLibri .= $valutazioneHtml;
        $listaLibri .= '</article>';
        $listaLibri .= '</li>';
    }
    $listaLibri .= '</ul>';

    $paginazione = '';
    if ($totalPagine > 1) {
        $paginazione .= '<nav aria-label="Navigazione pagine risultati"><ul>';

        if ($pagina > 1) {
            $paginazione .= '<li><a href="catalogo.php?' . http_build_query(array_merge($filtri, ['page' => $pagina - 1])) . '">Pagina precedente</a></li>';
        }

        for ($i = 1; $i <= $totalPagine; $i++) {
            if ($i === $pagina) {
                $paginazione .= '<li aria-current="page">' . $i . '</li>';
            } else {
                $paginazione .= '<li><a href="catalogo.php?' . http_build_query(array_merge($filtri, ['page' => $i])) . '">' . $i . '</a></li>';
            }
        }

        if ($pagina < $totalPagine) {
            $paginazione .= '<li><a href="catalogo.php?' . http_build_query(array_merge($filtri, ['page' => $pagina + 1])) . '">Pagina successiva</a></li>';
        }

        $paginazione .= '</ul></nav>';
    }

    $contenutoRisultati = $listaLibri . $paginazione;
}

$template = file_get_contents(__DIR__ . '/showCatalogo.html');
$template = str_replace('[FILTRO_CERCA_VALUE]',          $filtroCercaValue,          $template);
$template = str_replace('[OPZIONI_CATEGORIA]',           $opzioniCategoria,          $template);
$template = str_replace('[FILTRO_AUTORE_VALUE]',         $filtroAutoreValue,         $template);
$template = str_replace('[ANNO_CORRENTE]',               $annoCorrente,              $template);
$template = str_replace('[FILTRO_ANNO_VALUE]',           $filtroAnnoValue,           $template);
$template = str_replace('[DISPONIBILE_CHECKED]',         $disponibileChecked,        $template);
$template = str_replace('[ORDINE_VALUTAZIONE_SELECTED]', $ordineValutazioneSelected, $template);
$template = str_replace('[RISULTATI_HEADER]',            $risultatiHeader,           $template);
$template = str_replace('[CONTENUTO_RISULTATI]',         $contenutoRisultati,        $template);
echo $template;
