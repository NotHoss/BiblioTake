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

$template = file_get_contents(__DIR__ . '/../html/admin/showEliminaLibroAdmin.html');

if (!$libro) {
    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[NOT_FOUND_MESSAGE]' => '<p>Libro non trovato.</p>',
        '[FORM_DISPLAY]' => 'class="none"',
        '[TITOLO]' => '',
        '[SUMMARY]' => '',
        '[WARNING_MESSAGE]' => '',
        '[LIBRO_ID]' => '',
    ]);
    return;
}

$warningMsg = '';
if (!empty($message)) {
    $warningMsg = '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}

$summary = '<ul>'
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
]);
