<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Modifica prestito — Admin BiblioTake';
$pageDescription = 'Modifica i dettagli di un prestito.';
$pageKeywords = 'admin, prestiti, modifica, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Prestiti', 'href' => 'prestiti-utente.php?utente_id=0'),
    array('label' => 'Modifica prestito', 'href' => ''),
);
$currentPage = 'admin';
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
    // If save succeeded, redirect preserving the current search context when available
    if (empty($prestitoInModifica)) {
        $redirectQuery = !empty($res['cerca'])
            ? 'cerca=' . rawurlencode((string) $res['cerca'])
            : 'utente_id=' . (int) ($res['utenteId'] ?? 0);
        header('Location: ../admin/prestiti-utente.php?' . $redirectQuery);
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
