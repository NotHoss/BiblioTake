<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione utenti - Amministrazione BiblioTake';
$pageDescription = 'Gestione degli account utenti, prestiti e ruoli della biblioteca.';
$pageKeywords = 'amministrazione, utenti, gestione, prestiti, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Gestione utenti', 'href' => ''),
);
$currentPage = 'admin';

$errorMessage = '';

$filtri = [
    'cerca' => isset($_GET['cerca']) ? trim((string) $_GET['cerca']) : '',
    'stato_utenti' => isset($_GET['stato_utenti']) ? trim((string) $_GET['stato_utenti']) : 'tutti',
];

$resultsPerPage = 10;
$totalUtenti = 0;
$totalPagine = 1;
$pagina = max(1, (int) $page);

$utenti = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $totalUtenti = countUtentiConPrestitiFiltrati($conn, $filtri);
    $totalPagine = max(1, (int) ceil($totalUtenti / $resultsPerPage));
    $pagina = max(1, min($pagina, $totalPagine));
    $utenti = getUtentiConPrestitiFiltrati($conn, $filtri, $pagina, $resultsPerPage);
}
$adminViewMode = 'list';
$hideAdminNav = true;
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showUtentiAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}

