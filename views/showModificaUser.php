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

if (empty($userInfo)) {
    echo '<p>Effettuare l\'accesso per visualizzare le informazioni dell\'utente.</p>';
    echo '<a href="../login.php">Vai alla pagina di accesso</a>';    //ricontrolla il percorso
} else {
    $template = file_get_contents(__DIR__ . '/../html/user/showModificaUser.html');

    $fotoProfilo = !empty($userInfo['foto_profilo']) ? $userInfo['foto_profilo'] : DEFAULT_AVATAR;
    $foto = htmlspecialchars('../' . $fotoProfilo, ENT_QUOTES, 'UTF-8');

    $messagesHtml = '';
    if (!empty($errorMessage)) {
        $messagesHtml = '<div role="alert" class="auth-errors"><ul><li>'
            . btMarkEnglishTerms($errorMessage)
            . '</li></ul></div>';
    } elseif (!empty($successMessage)) {
        $messagesHtml = '<p class="admin-message admin-message-success" role="status" aria-live="polite">'
            . btMarkEnglishTerms($successMessage)
            . '</p>';
    }

    $placeholders = [
        '[TO-LOGIN]' => '',     //Rimuove il link di login poiché l'utente è già loggato
        '[MESSAGES]' => $messagesHtml,
        '[USERNAME]' => htmlspecialchars($userInfo['Nome Utente'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[EMAIL]' => htmlspecialchars($userInfo['email'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[PASSWORD]' => '********', //Voglio mostrare la password solo nella pagina di modifica delle informazioni 
        '[IMMAGINE_PROFILO]' => $foto,
    ];

    echo strtr($template, $placeholders);
}
