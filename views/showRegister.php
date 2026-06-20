<?php
$errorsHtml = '';
if (!empty($errors)) {
    $errorsHtml = '<div role="alert" class="auth-errors"><ul>';
    foreach ($errors as $err) {
        $errorsHtml .= '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    $errorsHtml .= '</ul></div>';
}

$template = file_get_contents(__DIR__ . '/../html/showRegister.html');

echo strtr($template, [
    '[ERRORS]'         => $errorsHtml,
    '[EMAIL_VALUE]'    => htmlspecialchars($emailValore, ENT_QUOTES, 'UTF-8'),
    '[USERNAME_VALUE]' => htmlspecialchars($usernameValore, ENT_QUOTES, 'UTF-8'),
]);
