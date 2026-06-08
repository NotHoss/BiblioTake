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

$template = file_get_contents(__DIR__ . '/../html/admin/showEliminaLibroAdmin.html');
$returnUrl = getSafeAdminReturnUrl('libri.php');
$returnUrlSafe = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');

if (!$libro) {
    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[NOT_FOUND_MESSAGE]' => renderAdminStatusMessage('Libro non trovato.', 'error'),
        '[FORM_DISPLAY]' => 'class="none"',
        '[TITOLO]' => '',
        '[SUMMARY]' => '',
        '[WARNING_MESSAGE]' => '',
        '[LIBRO_ID]' => '',
        '[RETURN_URL]' => $returnUrlSafe,
        '[ANNULLA_HREF]' => $returnUrlSafe,
    ]);
    return;
}

$warningMsg = '';
if (!empty($message)) {
    $warningMsg = renderAdminStatusMessage($message, 'warning');
}

$summary = '<ul class="admin-summary-list">'
    . '<li><strong>ID</strong>: ' . htmlspecialchars((string) $libroId, ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>ISBN</strong>: ' . htmlspecialchars((string) ($libro['codice_isbn'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Titolo</strong>: ' . htmlspecialchars((string) ($libro['titolo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Autore</strong>: ' . htmlspecialchars((string) ($libro['autore'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Editore</strong>: ' . htmlspecialchars((string) ($libro['casa_editrice'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Anno</strong>: ' . htmlspecialchars((string) ($libro['anno'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Pagine</strong>: ' . htmlspecialchars((string) ($libro['pagine'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '<li><strong>Categoria</strong>: ' . htmlspecialchars((string) ($libro['categoria'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
    . '</ul>';

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[NOT_FOUND_MESSAGE]' => '',
    '[FORM_DISPLAY]' => '',
    '[TITOLO]' => htmlspecialchars((string) $libro['titolo'], ENT_QUOTES, 'UTF-8'),
    '[SUMMARY]' => $summary,
    '[WARNING_MESSAGE]' => $warningMsg,
    '[LIBRO_ID]' => htmlspecialchars((string) $libroId, ENT_QUOTES, 'UTF-8'),
    '[RETURN_URL]' => $returnUrlSafe,
    '[ANNULLA_HREF]' => $returnUrlSafe,
]);
