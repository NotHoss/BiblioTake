<?php
if (empty($userInfo)) {
    echo '<p>Effettuare il login per visualizzare le informazioni dell\'utente.</p>';
    echo '<a href="../login.php">Vai alla pagina di login</a>';    //ricontrolla il percorso
} else {
    $template = file_get_contents(__DIR__ . '/../html/user/showModificaUser.html');

    $foto = !empty($userInfo['foto_profilo']) ? htmlspecialchars($userInfo['foto_profilo'], ENT_QUOTES, 'UTF-8') : 'images/default-avatar.jpg';

    $placeholders = [
        '[TO-LOGIN]' => '',     //Rimuove il link di login poiché l'utente è già loggato
        '[UTENTE]' => htmlspecialchars($userInfo['Nome Utente'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[EMAIL]' => htmlspecialchars($userInfo['email'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[PASSWORD]' => '********', //Voglio mostrare la password solo nella pagina di modifica delle informazioni 
        '[IMMAGINE_PROFILO]' => $foto,
    ];

    echo strtr($template, $placeholders);
}