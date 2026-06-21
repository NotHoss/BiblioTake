<?php
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
            . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8')
            . '</li></ul></div>';
    } elseif (!empty($successMessage)) {
        $messagesHtml = '<p class="admin-message admin-message-success" role="status" aria-live="polite">'
            . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8')
            . '</p>';
    }

    $placeholders = [
        '[TO-LOGIN]' => '',     //Rimuove il link di login poiché l'utente è già loggato
        '[MESSAGES]' => $messagesHtml,
        '[USERNAME]' => htmlspecialchars($userInfo['username'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[EMAIL]' => htmlspecialchars($userInfo['email'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[PASSWORD]' => '********', //Voglio mostrare la password solo nella pagina di modifica delle informazioni 
        '[IMMAGINE_PROFILO]' => $foto,
    ];

    echo strtr($template, $placeholders);
}
