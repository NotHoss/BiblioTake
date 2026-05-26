<?php
$adminViewMode = $adminViewMode ?? 'list';

$errorMsg = '';
if ($errorMessage !== '') {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$messages = $errorMsg . $successMsg;

if ($adminViewMode === 'create') {
    $template = file_get_contents(__DIR__ . '/../html/admin/aggiungi-libro.html');
    
    $categorieOptions = '';
    foreach (($categorie ?? []) as $cat) {
        $selected = ($dati['categoria'] === $cat ? 'selected' : '');
        $catHtml = htmlspecialchars($cat, ENT_QUOTES, 'UTF-8');
        $categorieOptions .= '<option value="' . $catHtml . '" ' . $selected . '>' . $catHtml . '</option>';
    }

    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[CODICE_ISBN]' => htmlspecialchars($dati['codice_isbn'], ENT_QUOTES, 'UTF-8'),
        '[TAGS]' => htmlspecialchars((string) ($dati['tags'] ?? ''), ENT_QUOTES, 'UTF-8'),
        '[TITOLO]' => htmlspecialchars($dati['titolo'], ENT_QUOTES, 'UTF-8'),
        '[AUTORE]' => htmlspecialchars($dati['autore'], ENT_QUOTES, 'UTF-8'),
        '[CASA_EDITRICE]' => htmlspecialchars($dati['casa_editrice'], ENT_QUOTES, 'UTF-8'),
        '[EDIZIONE]' => htmlspecialchars($dati['edizione'], ENT_QUOTES, 'UTF-8'),
        '[ANNO]' => htmlspecialchars($dati['anno'], ENT_QUOTES, 'UTF-8'),
        '[LINGUA]' => htmlspecialchars($dati['lingua'], ENT_QUOTES, 'UTF-8'),
        '[DESCRIZIONE]' => htmlspecialchars($dati['descrizione'], ENT_QUOTES, 'UTF-8'),
        '[PAGINE]' => htmlspecialchars($dati['pagine'], ENT_QUOTES, 'UTF-8'),
        '[CATEGORIE_OPTIONS]' => $categorieOptions,
    ]);
} elseif ($adminViewMode === 'edit') {
    $template = file_get_contents(__DIR__ . '/../html/admin/modifica-libro.html');
    
    if (!$libro) {
        echo strtr($template, [
            '[MESSAGES]' => $messages,
            '[NOT_FOUND_MESSAGE]' => '<p>Libro non trovato.</p>',
            '[FORM_DISPLAY]' => 'style="display:none;"',
            '[LIBRO_ID]' => '',
            
            '[CODICE_ISBN]' => '',
            '[TITOLO]' => '',
            '[AUTORE]' => '',
            '[CASA_EDITRICE]' => '',
            '[EDIZIONE]' => '',
            '[ANNO]' => '',
            '[LINGUA]' => '',
            '[DESCRIZIONE]' => '',
            '[PAGINE]' => '',
            '[COPERTINA_CORRENTE]' => '',
            '[DELETE_COPERTINA_DISABLED]' => '',
            '[DELETE_COPERTINA_TITLE]' => '',
            '[CATEGORIE_OPTIONS]' => '',
            '[TAGS]' => '',
        ]);
        return;
    }

    $categorieOptions = '';
    foreach (($categorie ?? []) as $cat) {
        $selected = ($libro['categoria'] === $cat ? 'selected' : '');
        $catHtml = htmlspecialchars($cat, ENT_QUOTES, 'UTF-8');
        $categorieOptions .= '<option value="' . $catHtml . '" ' . $selected . '>' . $catHtml . '</option>';
    }

    $copertinaHtml = '';
    if (!empty($libro['copertina'])) {
        $copertinaHtml = '<span>' . htmlspecialchars((string) $libro['copertina'], ENT_QUOTES, 'UTF-8') . '</span>';
    } else {
        $copertinaHtml = '<span>---</span>';
    }

    $canDeleteCover = false;
    if (!empty($libro['copertina']) && basename($libro['copertina']) !== basename(DEFAULT_COVER)) {
        $canDeleteCover = true;
    }
    
    $disabledAttr = !$canDeleteCover ? 'disabled' : '';
    $titleAttr = !$canDeleteCover ? 'title="Non è possibile eliminare la copertina placeholder"' : '';

    $tagsArr = getTagsByLibroId($conn, $libroId);
    $tagsCsv = '';
    if (!empty($tagsArr)) {
        $tagsCsv = implode(', ', array_column($tagsArr, 'nome'));
    }

    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[NOT_FOUND_MESSAGE]' => '',
        '[FORM_DISPLAY]' => '',
        '[LIBRO_ID]' => htmlspecialchars((string) $libroId, ENT_QUOTES, 'UTF-8'),
        '[TAGS]' => htmlspecialchars($tagsCsv, ENT_QUOTES, 'UTF-8'),
        '[CODICE_ISBN]' => htmlspecialchars((string) $libro['codice_isbn'], ENT_QUOTES, 'UTF-8'),
        '[TITOLO]' => htmlspecialchars((string) $libro['titolo'], ENT_QUOTES, 'UTF-8'),
        '[AUTORE]' => htmlspecialchars((string) $libro['autore'], ENT_QUOTES, 'UTF-8'),
        '[CASA_EDITRICE]' => htmlspecialchars((string) ($libro['casa_editrice'] ?? ''), ENT_QUOTES, 'UTF-8'),
        '[EDIZIONE]' => htmlspecialchars((string) ($libro['edizione'] ?? ''), ENT_QUOTES, 'UTF-8'),
        '[ANNO]' => htmlspecialchars((string) ($libro['anno'] ?? ''), ENT_QUOTES, 'UTF-8'),
        '[LINGUA]' => htmlspecialchars((string) ($libro['lingua'] ?? ''), ENT_QUOTES, 'UTF-8'),
        '[DESCRIZIONE]' => htmlspecialchars((string) ($libro['descrizione'] ?? ''), ENT_QUOTES, 'UTF-8'),
        '[PAGINE]' => htmlspecialchars((string) ($libro['pagine'] ?? ''), ENT_QUOTES, 'UTF-8'),
        '[COPERTINA_CORRENTE]' => $copertinaHtml,
        '[DELETE_COPERTINA_DISABLED]' => $disabledAttr,
        '[DELETE_COPERTINA_TITLE]' => $titleAttr,
        '[CATEGORIE_OPTIONS]' => $categorieOptions,
    ]);
} elseif ($adminViewMode === 'delete') {
    $template = file_get_contents(__DIR__ . '/../html/admin/elimina-libro.html');

    if (!$libro) {
        echo strtr($template, [
            '[MESSAGES]' => $messages,
            '[NOT_FOUND_MESSAGE]' => '<p>Libro non trovato.</p>',
            '[FORM_DISPLAY]' => 'style="display:none;"',
            '[TITOLO]' => '',
            '[WARNING_MESSAGE]' => '',
            '[LIBRO_ID]' => '',
            
        ]);
        return;
    }

    $warningMsg = '';
    if (!empty($message)) {
        $warningMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[NOT_FOUND_MESSAGE]' => '',
        '[FORM_DISPLAY]' => '',
        '[TITOLO]' => htmlspecialchars((string) $libro['titolo'], ENT_QUOTES, 'UTF-8'),
        '[WARNING_MESSAGE]' => $warningMsg,
        '[LIBRO_ID]' => htmlspecialchars((string) $libroId, ENT_QUOTES, 'UTF-8'),
        
    ]);
} else {
    // List mode
    $template = file_get_contents(__DIR__ . '/../html/admin/showLibri.html');
    
    if (empty($libri)) {
        echo strtr($template, [
            '[MESSAGES]' => $messages,
            '[EMPTY_MESSAGE]' => '<p>Nessun libro trovato.</p>',
            '[TABLE_DISPLAY]' => 'style="display:none;"',
            '[TABLE_ROWS]' => '',
        ]);
        return;
    }

    $tableRows = '';
    foreach ($libri as $libroRow) {
        $id = htmlspecialchars((string) $libroRow['id'], ENT_QUOTES, 'UTF-8');
        $titolo = htmlspecialchars((string) $libroRow['titolo'], ENT_QUOTES, 'UTF-8');
        $autore = htmlspecialchars((string) $libroRow['autore'], ENT_QUOTES, 'UTF-8');
        $anno = htmlspecialchars((string) $libroRow['anno'], ENT_QUOTES, 'UTF-8');
        $categoria = htmlspecialchars((string) $libroRow['categoria'], ENT_QUOTES, 'UTF-8');
        $prestiti = htmlspecialchars((string) $libroRow['prestiti_attivi'], ENT_QUOTES, 'UTF-8');

        $tagsArr = getTagsByLibroId($conn, (int) $libroRow['id']);
        $tagsList = [];
        foreach ($tagsArr as $t) { $tagsList[] = htmlspecialchars((string) $t['nome'], ENT_QUOTES, 'UTF-8'); }
        $tagsHtml = implode(', ', $tagsList);

        $tableRows .= "<tr>
            <td>{$id}</td>
            <td>{$titolo}</td>
            <td>{$autore}</td>
            <td>{$anno}</td>
            <td>{$categoria}</td>
            <td>{$tagsHtml}</td>
            <td>{$prestiti}</td>
            <td>
                <a href=\"modifica-libro.php?id={$id}\">Modifica</a>
                |
                <a href=\"elimina-libro.php?id={$id}\">Elimina</a>
            </td>
        </tr>\n";
    }
    
    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[EMPTY_MESSAGE]' => '',
        '[TABLE_DISPLAY]' => '',
        '[TABLE_ROWS]' => $tableRows,
    ]);
}


