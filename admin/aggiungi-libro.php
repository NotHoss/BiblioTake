<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Aggiungi libro — Admin BiblioTake';
$currentPage = 'admin';
$errorMessage = '';
$successMessage = '';

$dati = [
    'codice_isbn' => '',
    'titolo' => '',
    'autore' => '',
    'casa_editrice' => '',
    'edizione' => '',
    'anno' => '',
    'lingua' => '',
    'descrizione' => '',
    'pagine' => '',
    'copertina' => '',
    'categoria' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '') {
    $res = handleAggiungiLibro($conn, $_POST, $_FILES);
    $errorMessage = $res['errorMessage'] ?? '';
    $successMessage = $res['successMessage'] ?? '';
    $dati = $res['dati'] ?? $dati;
}
$adminViewMode = 'create';
$categorie = getCategorieLibri($conn);
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showLibriAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}