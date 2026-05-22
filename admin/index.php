<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Dashboard Admin — BiblioTake';
$pageDescription = 'Pannello amministrativo: statistiche e gestione della biblioteca.';
$pageKeywords = 'admin, dashboard, statistiche, gestione, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => ''),
);
$currentPage = 'admin';

$errorMessage = '';
$successMessage = '';
$isEditGeneralita = isset($_GET['mode']) && $_GET['mode'] === 'edit';

$biblioteca = null;
if ($conn instanceof mysqli && $errorMessage === '') {
    $biblioteca = getBibliotecaInfo($conn);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '') {
    $res = handleAggiornaGeneralita($conn, $_POST);
    $successMessage = $res['successMessage'] ?? '';
    $errorMessage = $res['errorMessage'] ?? '';
    $biblioteca = $res['biblioteca'] ?? $biblioteca;
    $isEditGeneralita = $res['isEditGeneralita'] ?? $isEditGeneralita;
}

// Calcola statistiche usando la funzione
$stats = ['libri' => 0, 'utenti' => 0];
if ($conn instanceof mysqli && $errorMessage === '') {
    $allStats = getStatisticheGenerali($conn);
    $stats['libri'] = $allStats['libri_totali'];
    $stats['utenti'] = $allStats['utenti_totali'];
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showDashboardAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}