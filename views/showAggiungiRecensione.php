<?php
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div role="alert">' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}

$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div role="status">' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}

$messages = $errorMsg . $successMsg;

$template = file_get_contents(__DIR__ . '/../html/user/showAggiungiRecensione.html');

echo strtr($template, [
    '[MESSAGES]'     => $messages,
    '[LIBRO_ID]'     => (int) $libro['id'],
    '[LIBRO_TITOLO]' => htmlspecialchars((string) $libro['titolo'], ENT_QUOTES, 'UTF-8'),
]);