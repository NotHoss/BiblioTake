<?php
session_start();
require_once __DIR__ . '/../includes/resources.php';
requireRole('utente');

$pageTitle = 'Le mie recensioni — User BiblioTake';
$pageDescription = 'Elenco delle recensioni scritte dall\'utente.';
$pageKeywords = 'user, recensioni, libri, BiblioTake';
$currentPage = 'recensioni';
$errorMessage = '';

$breadcrumb = array(
    array('label' => 'Home',     'href' => '../index.php'),
    array('label' => 'Profilo', 'href' => 'dashboard.php'),
    array('label' => 'Recensioni', 'href' => ''),
);

$recensioni = [];
$resultsPerPage = 10;
$totalRecensioni = 0;
$totalPagine = 1;
$pagina = max(1, (int) ($_GET['page'] ?? 1));

if ($conn instanceof mysqli) {
    $recensioni = getRecensioniUser($conn, $_SESSION['user_id']);
    $totalRecensioni = count($recensioni);
    $totalPagine = max(1, (int) ceil($totalRecensioni / $resultsPerPage));
    $pagina = max(1, min($pagina, $totalPagine));
    $recensioni = array_slice($recensioni, ($pagina - 1) * $resultsPerPage, $resultsPerPage);
}
else{$errorMessage = 'Errore di connessione al database.';}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/template/sidebar-user.php';
require_once __DIR__ . "/../views/showRecensioniUser.php";
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
