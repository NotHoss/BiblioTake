<?php
session_start();
require_once __DIR__ . '/../includes/resources.php';
requireRole('utente');

$pageTitle = 'Prestiti Passati — User BiblioTake';
$pageDescription = 'Elenco e gestione dei prestiti passati.';
$pageKeywords = 'user, prestiti, passati, catalogo, BiblioTake';
$currentPage = 'prestiti-passati';
$errorMessage = '';
$prestitiTitle = 'Prestiti Passati';

$breadcrumb = array(
    array('label' => 'Home',     'href' => '../index.php', 'lang' => 'en'),
    array('label' => 'Profilo', 'href' => 'dashboard.php'),
    array('label' => 'Prestiti Passati', 'href' => ''),
);

$prestiti = [];
$resultsPerPage = 10;
$totalPrestiti = 0;
$totalPagine = 1;
$pagina = max(1, (int) ($_GET['page'] ?? 1));
if ($conn instanceof mysqli) {
    $prestiti = getPrestitiUser($conn, $_SESSION['user_id'], 'passati');
    $totalPrestiti = count($prestiti);
    $totalPagine = max(1, (int) ceil($totalPrestiti / $resultsPerPage));
    $pagina = max(1, min($pagina, $totalPagine));
    $prestiti = array_slice($prestiti, ($pagina - 1) * $resultsPerPage, $resultsPerPage);   // Estrae solo i prestiti per la pagina corrente
}
else{$errorMessage = 'Errore di connessione al database.';}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/template/sidebar-user.php';
require_once __DIR__ . "/../views/showPrestitiUser.php";
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
