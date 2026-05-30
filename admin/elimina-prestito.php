<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Elimina prestito — Admin BiblioTake';
$pageDescription = 'Elimina un prestito dal pannello amministrativo.';
$pageKeywords = 'admin, prestiti, eliminazione, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Prestiti', 'href' => 'prestiti-utente.php?utente_id=0'),
    array('label' => 'Elimina prestito', 'href' => ''),
);
$currentPage = 'admin';
$message = '';
$errorMessage = '';

$prestitoId = isset($_GET['prestito_id']) ? (int) $_GET['prestito_id'] : (isset($_POST['prestito_id']) ? (int) $_POST['prestito_id'] : 0);

if ($prestitoId <= 0) {
    $errorMessage = 'ID prestito non valido.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $prestitoId > 0 && $conn instanceof mysqli) {
    // forward to handler
    $res = handlePrestitiActions($conn, $_POST);
    $message = $res['message'] ?? $message;
    // after deletion redirect to prestiti list for the user
    header('Location: ../admin/prestiti-utente.php?utente_id=' . (int) ($res['utenteId'] ?? 0));
    exit;
}

$prestito = null;
if ($conn instanceof mysqli && $prestitoId > 0) {
    $prestito = getPrestitoById($conn, $prestitoId);
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showEliminaPrestitoAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) $conn->close();
