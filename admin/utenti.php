<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione Utenti — Admin BiblioTake';
$currentPage = 'admin';

$errorMessage = '';

// Recupera utenti usando la funzione
$utenti = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $utenti = getUtentiConPrestiti($conn, 500);
}
$adminViewMode = 'list';
require_once __DIR__ . '/../views/showUtentiAdmin.php';

if ($conn instanceof mysqli) {
    $conn->close();
}

