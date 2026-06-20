<?php
if (!function_exists('btMarkEnglishTerms')) {
    function btMarkEnglishTerms($text) {
        $safe = htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
        return str_replace(
            ['Password', 'password', 'Email', 'email', 'Username', 'username', 'Login', 'login'],
            [
                '<span lang="en">Password</span>',
                '<span lang="en">password</span>',
                '<span lang="en">Email</span>',
                '<span lang="en">email</span>',
                '<span lang="en">Username</span>',
                '<span lang="en">username</span>',
                '<span lang="en">Login</span>',
                '<span lang="en">login</span>',
            ],
            $safe
        );
    }
}

$errorsHtml = '';
if (!empty($errors)) {
    $errorsHtml = '<div role="alert" class="auth-errors"><ul>';
    foreach ($errors as $err) {
        $errorsHtml .= '<li>' . btMarkEnglishTerms($err) . '</li>';
    }
    $errorsHtml .= '</ul></div>';
}

$template = file_get_contents(__DIR__ . '/../html/showRegister.html');

echo strtr($template, [
    '[ERRORS]'         => $errorsHtml,
    '[EMAIL_VALUE]'    => htmlspecialchars($emailValore, ENT_QUOTES, 'UTF-8'),
    '[USERNAME_VALUE]' => htmlspecialchars($usernameValore, ENT_QUOTES, 'UTF-8'),
]);
