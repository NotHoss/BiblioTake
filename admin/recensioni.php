<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Recensioni utenti — Admin BiblioTake';
$currentPage = 'admin';
$message = '';
$errorMessage = '';

$utenteId = isset($_GET['utente_id']) ? (int) $_GET['utente_id'] : (isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0);

$res = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '') {
    $res = handleRecensioniActions($conn, $_POST);
    $message = $res['message'] ?? $message;
}

$utenti = [];
$recensioni = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $utenti = getAllUtenti($conn, 200);
    $recensioni = getRecensioniAdmin($conn, $utenteId);
}
$adminViewMode = 'reviews';
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showUtentiAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}