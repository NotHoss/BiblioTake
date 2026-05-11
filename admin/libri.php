<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione Libri — Admin BiblioTake';
$currentPage = 'admin';

$errorMessage = '';

// Recupera libri usando la funzione
$libri = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $libri = getLibriAdmin($conn, 500);
}
require_once __DIR__ . '/../views/showLibriAdmin.php';

if ($conn instanceof mysqli) {
    $conn->close();
}

