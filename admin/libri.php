<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione Libri — Admin BiblioTake';
$pageDescription = 'Elenco e gestione dei libri nel catalogo della biblioteca.';
$pageKeywords = 'admin, libri, gestione, catalogo, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Libri', 'href' => ''),
);
$currentPage = 'admin';

$errorMessage = '';

// Recupera libri usando la funzione
$libri = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $libri = getLibriAdmin($conn, 500);
}
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showLibriAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}

