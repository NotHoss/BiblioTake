<?php
require_once '../includes/resources.php';
requireRole('user');

$currentPage = 'user';
$errorMessage = '';

$tipo = $_GET['tipo'] ?? 'attivi';
if (!in_array($tipo, ['attivi', 'passati'], true)) {
    $tipo = 'attivi';
}

$prestiti = [];
if ($conn instanceof mysqli) {
    $prestiti = getPrestitiUser($conn, $_SESSION['user_id'], $tipo);
}
else{$errorMessage = 'Errore di connessione al database.';}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . "/../views/showPrestiti{$tipo}User.php";
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
