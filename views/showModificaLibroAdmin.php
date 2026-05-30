<?php
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$messages = $errorMsg . $successMsg;

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
