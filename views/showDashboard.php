<?php
//l'accesso e' gia' garantito da requireRole('utente') in user/dashboard.php: $user e' sempre valorizzato
$template = file_get_contents(__DIR__ . '/../html/user/showDashboard.html');

$foto = !empty($user['foto_profilo']) ? htmlspecialchars('/' . $user['foto_profilo'], ENT_QUOTES, 'UTF-8') : '/images/default-avatar.jpg';

$placeholders = [
    '[USERNAME]' => htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'),
    '[EMAIL]' => htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'),
    '[PASSWORD]' => '********',
    '[IMMAGINE_PROFILO]' => $foto,
];

echo strtr($template, $placeholders);