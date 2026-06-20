<?php
if (empty($userInfo)) {
    echo '<p>Effettuare l\'accesso per visualizzare le informazioni dell\'utente.</p>';
    echo '<a href="../login.php">Vai alla pagina di accesso</a>';    //ricontrolla il percorso
} else {
    $template = file_get_contents(__DIR__ . '/../html/user/showEliminaUser.html');

    $messages = '';
    if (!empty($errorMessage)) {
    $messages = !empty($errorMessage) ? '<div class="delete-error">' . htmlspecialchars($errorMessage) . '</div>' : '';
    }

    $fotoProfilo = !empty($userInfo['foto_profilo']) ? $userInfo['foto_profilo'] : DEFAULT_AVATAR;
    $foto = htmlspecialchars('../' . $fotoProfilo, ENT_QUOTES, 'UTF-8');

    $placeholders = [
        '[USERNAME]' => htmlspecialchars($userInfo['username'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[IMMAGINE_PROFILO]' => $foto,
        '[MESSAGES]'  => $messages,
    ];

    echo strtr($template, $placeholders);
}
