<?php
//valori semplici
$libroTitolo  = htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8');
$copertinaSrc = htmlspecialchars(!empty($libro['copertina']) ? $libro['copertina'] : 'images/place-holder.jpg', ENT_QUOTES, 'UTF-8');
$copertinAlt  = htmlspecialchars('Copertina del libro ' . $libro['titolo'] . ' di ' . $libro['autore'], ENT_QUOTES, 'UTF-8');
$autore       = htmlspecialchars($libro['autore'], ENT_QUOTES, 'UTF-8');
$casaEditrice = htmlspecialchars($libro['casa_editrice'], ENT_QUOTES, 'UTF-8');
$edizione     = (int) $libro['edizione'];
$anno         = (int) $libro['anno'];
$lingua       = htmlspecialchars($libro['lingua'], ENT_QUOTES, 'UTF-8');
$pagine       = (int) $libro['pagine'];
$categoria    = htmlspecialchars($libro['categoria'], ENT_QUOTES, 'UTF-8');
$isbn         = htmlspecialchars($libro['codice_isbn'], ENT_QUOTES, 'UTF-8');

//disponibilità
$disponibilitaTesto = $disponibile
    ? '<strong>&#10003; Disponibile</strong>'
    : '<strong>&#10007; Non disponibile</strong>';

//riga valutazione media (opzionale)
$rigaValutazioneMedia = '';
if ($libro['media_voti'] !== null) {
    $valutazione          = htmlspecialchars((string) $libro['media_voti'], ENT_QUOTES, 'UTF-8');
    $rigaValutazioneMedia = '<tr><th scope="row">Valutazione media</th><td>' . $valutazione . ' su 5</td></tr>';
}

//sezione descrizione (opzionale)
$sezioneDescrizione = '';
if (!empty($libro['descrizione'])) {
    $descrizione        = htmlspecialchars($libro['descrizione'], ENT_QUOTES, 'UTF-8');
    $sezioneDescrizione = '<section id="descrizione-libro"><h3>Descrizione</h3><p>' . $descrizione . '</p></section>';
}

//sezione tags (opzionale)
$sezioneTags = '';
if (!empty($tags)) {
    $sezioneTags = '<section id="tag-libro"><h3>Etichette</h3><ul>';
    foreach ($tags as $tag) {
        $tagNome     = htmlspecialchars($tag['nome'], ENT_QUOTES, 'UTF-8');
        $sezioneTags .= '<li><a href="catalogo.php?tag=' . urlencode($tag['nome']) . '">' . $tagNome . '</a></li>';
    }
    $sezioneTags .= '</ul></section>';
}

//azione prestito
if ($utenteLoggato && $haPrestitoAttivo) {
    $azionePrestito = '<p role="alert"><strong>Richiesta di prestito effettuata.</strong></p>';
    $azionePrestito .= '<p class="info-prestito">Il prestito inizia il giorno stesso della richiesta e ha una durata di 30 giorni. Se hai bisogno di più tempo, contatta un amministratore entro una settimana dalla scadenza per ottenere una proroga di un mese.</p>';
} elseif (!$disponibile) {
    $azionePrestito = '<p>Questo libro non è al momento disponibile per il prestito.</p>';
} elseif ($utenteLoggato) {
    $azionePrestito  = '<form method="POST" action="user/richiedi-prestito.php">';
    $azionePrestito .= '<input type="hidden" name="libro_id" value="' . (int) $libro['id'] . '" />';
    $azionePrestito .= '<button type="submit" class="btn-primary">Richiedi prestito</button>';
    $azionePrestito .= '</form>';
    $azionePrestito .= '<p class="info-prestito">Il prestito inizia il giorno stesso della richiesta e ha una durata di 30 giorni. Se hai bisogno di più tempo, contatta un amministratore entro una settimana dalla scadenza per ottenere una proroga di un mese.</p>';
} else {
    $azionePrestito  = '<p>Per richiedere il prestito devi aver effettuato l\'accesso.</p>';
    $azionePrestito .= '<a href="login.php?intended=dettaglio-libro.php?id=' . (int) $libro['id'] . '">Accedi per richiedere il prestito</a>';
}

//lista recensioni
if (empty($recensioni)) {
    //utente loggato: niente invito ad accedere (il bottone "aggiungi recensione" e' gia mostrato sotto). anonimo: invito al login
    if (isset($_SESSION['user_id'])) {
        $listaRecensioni = '<p>Nessuna recensione ancora. Sii il primo a recensire questo libro!</p>';
    } else {
        $listaRecensioni = '<p>Nessuna recensione ancora. <a href="login.php">Accedi</a> per essere il primo a recensire questo libro.</p>';
    }
} else {
    $listaRecensioni = '<ul>';
    foreach ($recensioni as $recensione) {
        $username     = htmlspecialchars($recensione['username'], ENT_QUOTES, 'UTF-8');
        $dataDatetime = date('Y-m-d\TH:i:s', strtotime($recensione['data']));
        $dataFormated = htmlspecialchars(date('d/m/Y', strtotime($recensione['data'])), ENT_QUOTES, 'UTF-8');
        $valutazione  = (int) $recensione['valutazione'];

        $testoHtml = '';
        if (!empty($recensione['testo'])) {
            $testoHtml = '<p>' . htmlspecialchars($recensione['testo'], ENT_QUOTES, 'UTF-8') . '</p>';
        }

        //link modifica/elimina mostrati solo all'autore della recensione (loggato) e se non censurata
        $azioniAutore = '';
        if (isset($_SESSION['user_id'])
            && (int) $_SESSION['user_id'] === (int) $recensione['utente_id']
            && (int) $recensione['censura'] === 0) {
            $recensioneId = (int) $recensione['recensione_id'];
            $azioniAutore  = '<p class="recensione-azioni">';
            $azioniAutore .= '<p class="modifica-card btn-secondary"><a href="user/modifica-recensione.php?id=' . $recensioneId . '&amp;return=libro">Modifica</a></p>';
            $azioniAutore .= '<p class="elimina-card btn-elimina"><a href="user/elimina-recensione.php?id=' . $recensioneId . '&amp;return=libro">Elimina</a></p>';
            $azioniAutore .= '</p>';
        }

        $listaRecensioni .= '<li>';
        $listaRecensioni .= '<article class="recensione-card">';
        $listaRecensioni .= '<header>';
        $listaRecensioni .= '<h4 class="recensione-titolo username">Recensione di ' . $username . '</h4>';
        $listaRecensioni .= '<p><time datetime="' . $dataDatetime . '">' . $dataFormated . '</time></p>';
        $listaRecensioni .= '<p>Valutazione: ' . $valutazione . ' su 5</p>';
        $listaRecensioni .= $azioniAutore;
        $listaRecensioni .= '</header>';
        $listaRecensioni .= $testoHtml;
        $listaRecensioni .= '</article>';
        $listaRecensioni .= '</li>';
    }
    $listaRecensioni .= '</ul>';
}

$template = file_get_contents(__DIR__ . '/../html/showDettaglioLibro.html');
$template = str_replace('[LIBRO_TITOLO]',           $libroTitolo,           $template);
$template = str_replace('[COPERTINA_SRC]',          $copertinaSrc,          $template);
$template = str_replace('[COPERTINA_ALT]',          $copertinAlt,           $template);
$template = str_replace('[AUTORE]',                 $autore,                $template);
$template = str_replace('[CASA_EDITRICE]',          $casaEditrice,          $template);
$template = str_replace('[EDIZIONE]',               $edizione,              $template);
$template = str_replace('[ANNO_DATETIME]',          $anno,                  $template);
$template = str_replace('[LINGUA]',                 $lingua,                $template);
$template = str_replace('[PAGINE]',                 $pagine,                $template);
$template = str_replace('[CATEGORIA]',              $categoria,             $template);
$template = str_replace('[ISBN]',                   $isbn,                  $template);
//aggiungi recensione
$linkRecensione = '';
if (isset($_SESSION['user_id'])) {
    $linkRecensione = '<a class="btn-primary aggiungi-recensione" href="user/aggiungi-recensione.php?libro_id=' . (int) $libro['id'] . '">Aggiungi recensione</a>';
}
$template = str_replace('[LINK_RECENSIONE]', $linkRecensione, $template);
$template = str_replace('[DISPONIBILITA]',          $disponibilitaTesto,    $template);
$template = str_replace('[RIGA_VALUTAZIONE_MEDIA]', $rigaValutazioneMedia,  $template);
$template = str_replace('[SEZIONE_DESCRIZIONE]',    $sezioneDescrizione,    $template);
$template = str_replace('[SEZIONE_TAGS]',           $sezioneTags,           $template);
$template = str_replace('[AZIONE_PRESTITO]',        $azionePrestito,        $template);
$template = str_replace('[LISTA_RECENSIONI]',       $listaRecensioni,       $template);
echo $template;
