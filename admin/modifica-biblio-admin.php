<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Modifica generalità biblioteca — Admin BiblioTake';
$pageDescription = 'Aggiorna indirizzo, contatti e orari della biblioteca.';
$pageKeywords = 'admin, biblioteca, modifica, generalità, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Modifica generalità', 'href' => ''),
);
$currentPage = 'admin';

$errorMessage = '';
$successMessage = '';

$biblioteca = null;
if ($conn instanceof mysqli) {
    $biblioteca = getBibliotecaInfo($conn);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli) {
    $res = handleAggiornaGeneralita($conn, $_POST);
    $successMessage = $res['successMessage'] ?? '';
    $errorMessage = $res['errorMessage'] ?? '';
    $biblioteca = $res['biblioteca'] ?? $biblioteca;

    if ($successMessage !== '') {
        header('Location: index.php?generalita_success=1');
        exit;
    }
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showModificaBiblioAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
