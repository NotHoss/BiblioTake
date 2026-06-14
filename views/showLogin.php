<?php
$errorsHtml = '';
if (!empty($errors)) {
    $errorsHtml = '<div role="alert" class="auth-errors"><ul>';
    foreach ($errors as $err) {
        $errorsHtml .= '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    $errorsHtml .= '</ul></div>';
}

$actionUrl = 'login.php';
if (!empty($intended)) {
    $actionUrl .= '?intended=' . urlencode($intended);
}

$template = file_get_contents(__DIR__ . '/../html/showLogin.html');

echo strtr($template, [
    '[ERRORS]'      => $errorsHtml,
    '[FORM_ACTION]' => htmlspecialchars($actionUrl, ENT_QUOTES, 'UTF-8'),
    '[LOGIN_VALUE]' => htmlspecialchars($loginValore, ENT_QUOTES, 'UTF-8'),
]);
