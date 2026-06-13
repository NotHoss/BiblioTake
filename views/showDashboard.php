<?php
$pagina = $pagina ?? 1;
$totalPagine = $totalPagine ?? 1;

$errorMsg = '';
if ($errorMessage !== '') {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$messages = $errorMsg . $successMsg;

$user = null;
if (isset($_SESSION['user_id'])) {
    $user = getUserInfo($conn, $_SESSION['user_id']);
}

$template = file_get_contents(__DIR__ . '/../html/user/showDashboard.html');

if (empty($user)) {
    echo '<div class="empty-result">';
    echo '<p>Effettuare il login per visualizzare le informazioni utente</p>';
    echo '<a href="../login.php">Vai alla pagina di login</a>';
    echo '</div>';
    return;
    
} else {
    $template = file_get_contents(__DIR__ . '/../html/user/showDashboard.html');
    
    $foto = !empty($user['foto_profilo']) ? htmlspecialchars('/' . $user['foto_profilo'], ENT_QUOTES, 'UTF-8') : '/images/default-avatar.jpg';  // molto importante lo / all'inizio del percorso per assicurarsi che venga risolto correttamente rispetto alla root del sito

    $placeholders = [
        '[USERNAME]' => htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[EMAIL]' => htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[PASSWORD]' => '********', //Voglio mostrare la password solo nella pagina di modifica delle informazioni 
        '[IMMAGINE_PROFILO]' => $foto,
    ];

    $placeholders = [
        '[USERNAME]' => htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[EMAIL]' => htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[PASSWORD]' => '********',
        '[IMMAGINE_PROFILO]' => $foto,
    ];

    echo strtr($template, $placeholders);
}