<?php
$pagina = $pagina ?? 1;
$totalPagine = $totalPagine ?? 1;

$errorMsg = '';
if ($errorMessage !== '') {
    $errorMsg = '<div role="alert">' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div role="status">' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$messages = $errorMsg . $successMsg;

$user = null;
if (isset($_SESSION['user_id'])) {
    $user = getUserInfo($conn, $_SESSION['user_id']);
}

if (empty($user)) {
    echo '<div class="empty-result">';
    echo '<p>Effettuare l\'accesso per visualizzare le informazioni utente</p>';
    echo '<a href="../login.php">Vai alla pagina di accesso</a>';
    echo '</div>';
    return;
    
} else {
    $template = file_get_contents(__DIR__ . '/../html/user/showDashboard.html');
    
    $fotoProfilo = !empty($user['foto_profilo']) ? $user['foto_profilo'] : DEFAULT_AVATAR;
    $foto = htmlspecialchars('../' . $fotoProfilo, ENT_QUOTES, 'UTF-8');

    $placeholders = [
        '[USERNAME]' => htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[EMAIL]' => htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'),
        '[PASSWORD]' => '********',
        '[IMMAGINE_PROFILO]' => $foto,
    ];

    echo strtr($template, $placeholders);
}
