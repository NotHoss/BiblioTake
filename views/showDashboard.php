<?php
if (empty($user)) {
    echo '<p>Effettuare il login per visualizzare le informazioni dell\'utente.</p>';
    echo '<a href="../login.php">Vai alla pagina di login</a>';    //ricontrolla il percorso
} else {
    $template = file_get_contents(__DIR__ . '/../html/dashboard.html');
    
    $foto = !empty($user['foto_profilo']) ? htmlspecialchars('/' . $user['foto_profilo'], ENT_QUOTES, 'UTF-8') : '/images/default-avatar.jpg';  // molto importante lo / all'inizio del percorso per assicurarsi che venga risolto correttamente rispetto alla root del sito

    $placeholders = [
        '[TO-LOGIN]' => '',     //Rimuove il link di login poiché l'utente è già loggato
        '[USERNAME]' => htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[EMAIL]' => htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[PASSWORD]' => '********', //Voglio mostrare la password solo nella pagina di modifica delle informazioni 
        '[IMMAGINE_PROFILO]' => $foto,
    ];

    echo strtr($template, $placeholders);
}