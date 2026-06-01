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

$template = file_get_contents(__DIR__ . '/../html/admin/showAggiungiLibroAdmin.html');

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
