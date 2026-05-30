<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Modifica prestito — Admin BiblioTake';
$message = '';
$errorMessage = '';

$prestitoId = isset($_GET['prestito_id']) ? (int) $_GET['prestito_id'] : (isset($_POST['prestito_id']) ? (int) $_POST['prestito_id'] : 0);

if ($prestitoId <= 0) {
    $errorMessage = 'ID prestito non valido.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $prestitoId > 0 && $conn instanceof mysqli) {
    $res = handlePrestitiActions($conn, $_POST);
    $message = $res['message'] ?? $message;
    // If handler returned a prestitoInModifica (validation error), use it for rendering
    $prestitoInModifica = $res['prestitoInModifica'] ?? null;
    // If save succeeded (no prestitoInModifica) and we have a target utenteId, redirect
    if (empty($prestitoInModifica) && !empty($res['utenteId'])) {
        header('Location: ../admin/prestiti-utente.php?utente_id=' . (int) $res['utenteId']);
        exit;
    }
}

// Load prestito for display (if not set by POST handler)
$prestito = null;
if ($conn instanceof mysqli && $prestitoId > 0 && empty($prestitoInModifica)) {
    $prestito = getPrestitoById($conn, $prestitoId);
    // set the variable expected by the view
    $prestitoInModifica = $prestito;
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showModificaPrestitoAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) $conn->close();
