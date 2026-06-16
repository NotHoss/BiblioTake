<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Dashboard amministrazione - BiblioTake';
$pageDescription = 'Pannello amministrativo: statistiche e gestione della biblioteca.';
$pageKeywords = 'amministrazione, statistiche, gestione, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php', 'lang' => 'en'),
    array('label' => 'Admin', 'href' => '', 'lang' => 'en'),
);
$currentPage = 'admin';

$errorMessage = '';
$successMessage = '';

if (isset($_GET['generalita_success']) && $_GET['generalita_success'] === '1') {
    $successMessage = 'Generalità della biblioteca aggiornate con successo.';
}

$biblioteca = null;
if ($conn instanceof mysqli && $errorMessage === '') {
    $biblioteca = getBibliotecaInfo($conn);
}

// Calcola statistiche usando la funzione
$stats = [
    'libri_totali' => 0,
    'libri_prenotati' => 0,
    'libri_non_prenotati' => 0,
    'utenti_totali' => 0,
    'nuovi_iscritti' => null,
    'prestiti_attivi' => 0,
    'prestiti_ritardo' => 0,
    'recensioni_totali' => 0,
];
if ($conn instanceof mysqli && $errorMessage === '') {
    $stats = getStatisticheGenerali($conn);
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showDashboardAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
