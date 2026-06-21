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

$template = file_get_contents(__DIR__ . '/../html/admin/showAggiungiLibroAdmin.html');
$returnUrl = getSafeAdminReturnUrl('libri.php');
$returnUrlSafe = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');
$isbnExistingValues = htmlspecialchars(implode(',', $conn instanceof mysqli ? getCodiciIsbnLibri($conn) : []), ENT_QUOTES, 'UTF-8');

$categorieOptions = '';
$categoriaIsNew = ($dati['categoria'] ?? '') !== '' && !in_array((string) $dati['categoria'], array_map('strval', $categorie ?? []), true);
$categoriaNuovaValue = $categoriaIsNew ? (string) $dati['categoria'] : '';
foreach (($categorie ?? []) as $cat) {
    $selected = (!$categoriaIsNew && $dati['categoria'] === $cat ? 'selected="selected"' : '');
    $catHtml = htmlspecialchars($cat, ENT_QUOTES, 'UTF-8');
    $categorieOptions .= '<option value="' . $catHtml . '" ' . $selected . '>' . $catHtml . '</option>';
}

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[CODICE_ISBN]' => htmlspecialchars($dati['codice_isbn'], ENT_QUOTES, 'UTF-8'),
    '[ISBN_EXISTING_VALUES]' => $isbnExistingValues,
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
    '[CATEGORIA_NUOVA]' => htmlspecialchars($categoriaNuovaValue, ENT_QUOTES, 'UTF-8'),
    '[CATEGORIA_NUOVA_SELECTED]' => $categoriaIsNew ? 'selected="selected"' : '',
    '[CURRENT_YEAR]' => date('Y'),
    '[RETURN_URL]' => $returnUrlSafe,
    '[ANNULLA_HREF]' => $returnUrlSafe,
]);
