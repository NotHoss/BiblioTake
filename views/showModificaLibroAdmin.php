<?php
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = renderAdminStatusMessage($errorMessage, 'error');
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = renderAdminStatusMessage($successMessage, 'success');
}
$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/admin/showModificaLibroAdmin.html');
$returnUrl = getSafeAdminReturnUrl('libri.php');
$returnUrlSafe = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');

if (!$libro) {
    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[NOT_FOUND_MESSAGE]' => renderAdminStatusMessage('Libro non trovato.', 'error'),
        '[FORM_DISPLAY]' => 'class="none"',
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
        '[DELETE_COPERTINA_DESCRIBEDBY]' => '',
        '[DELETE_COPERTINA_HELP]' => '',
        '[CATEGORIE_OPTIONS]' => '',
        '[TAGS]' => '',
        '[CURRENT_YEAR]' => date('Y'),
        '[RETURN_URL]' => $returnUrlSafe,
        '[ANNULLA_HREF]' => $returnUrlSafe,
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
$deleteCoverDescribedBy = '';
$deleteCoverHelp = '';
if (!$canDeleteCover) {
    $deleteCoverDescribedBy = 'aria-describedby="delete_copertina_help"';
    $deleteCoverHelp = '<span id="delete_copertina_help">Non &egrave; possibile eliminare la copertina placeholder.</span>';
}

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
    '[DELETE_COPERTINA_DESCRIBEDBY]' => $deleteCoverDescribedBy,
    '[DELETE_COPERTINA_HELP]' => $deleteCoverHelp,
    '[CATEGORIE_OPTIONS]' => $categorieOptions,
    '[CURRENT_YEAR]' => date('Y'),
    '[RETURN_URL]' => $returnUrlSafe,
    '[ANNULLA_HREF]' => $returnUrlSafe,
]);
