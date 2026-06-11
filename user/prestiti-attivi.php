<?php
session_start();
require_once __DIR__ . '/../includes/resources.php';
requireRole('utente');

$pageTitle = 'Prestiti Attivi — User BiblioTake';
$pageDescription = 'Elenco e gestione dei prestiti attualmente attivi.';
$pageKeywords = 'user, prestiti, attivi, catalogo, BiblioTake';
$currentPage = 'prestiti-attivi';
$errorMessage = '';
$prestitiTitle = 'Prestiti Attivi';

$breadcrumb = array(
    array('label' => 'Home',     'href' => 'index.php'),
    array('label' => 'Profilo', 'href' => 'dashboard.php'),
    array('label' => 'Prestiti Attivi', 'href' => ''),
);

$prestiti = [];
$resultsPerPage = 10;
$totalPrestiti = 0;
$totalPagine = 1;
$page = (int) ($_GET['page'] ?? 1);
$pagina = max(1, $page);
if ($conn instanceof mysqli) {
    $prestiti = getPrestitiUser($conn, $_SESSION['user_id'], 'attivi');
    $totalPrestiti = count($prestiti);
    $totalPagine = max(1, (int) ceil($totalPrestiti / $resultsPerPage));
    $pagina = max(1, min($pagina, $totalPagine));
    $prestiti = array_slice($prestiti, ($pagina - 1) * $resultsPerPage, $resultsPerPage);   // Estrae solo i prestiti per la pagina corrente
}
else{$errorMessage = 'Errore di connessione al database.';}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/template/sidebar-user.php';
require_once __DIR__ . '/../views/showPrestitiUser.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
