<?php
if (empty($userInfo)) {
    echo '<p>Effettuare il login per visualizzare le informazioni dell\'utente.</p>';
    echo '<a href="../login.php">Vai alla pagina di login</a>';    //ricontrolla il percorso
} else {
    $template = file_get_contents(__DIR__ . '/../html/user/showEliminaUser.html');

    $fotoProfilo = !empty($userInfo['foto_profilo']) ? $userInfo['foto_profilo'] : DEFAULT_AVATAR;
    $foto = htmlspecialchars('../' . $fotoProfilo, ENT_QUOTES, 'UTF-8');

    $placeholders = [
        '[USERNAME]' => htmlspecialchars($userInfo['username'] ?? '', ENT_QUOTES, 'UTF-8'),
    ];

    echo strtr($template, $placeholders);
}
