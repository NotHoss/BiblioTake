<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Prestiti utente — Admin BiblioTake';
$currentPage = 'admin';
$message = '';
$errorMessage = '';

$utenteId = isset($_GET['utente_id']) ? (int) $_GET['utente_id'] : (isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0);
$prestitoInModifica = null;

$res = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && isset($_POST['prestito_id'], $_POST['azione'])) {
    $res = handlePrestitiActions($conn, $_POST);
    $message = $res['message'] ?? $message;
    $prestitoInModifica = $res['prestitoInModifica'] ?? $prestitoInModifica;
    $utenteId = $res['utenteId'] ?? $utenteId;
}

$utenti = [];
$prestiti = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $utenti = getAllUtenti($conn, 200);

    if ($utenteId > 0) {
        $prestiti = getPrestitiByUtente($conn, $utenteId);
    }
}
$adminViewMode = 'prestiti';
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showUtentiAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}