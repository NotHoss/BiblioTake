<?php
session_start();
require_once '../includes/resources.php';
requireRole('utente');

$pageTitle = 'Elimina Recensione — User BiblioTake';
$pageDescription = 'Conferma eliminazione della recensione.';
$pageKeywords = 'user, elimina, recensione, BiblioTake';
$currentPage = 'recensioni';
$errorMessage = '';
$successMessage = '';

$breadcrumb = array(
    array('label' => 'Home',     'href' => 'index.php'),
    array('label' => 'Profilo', 'href' => 'dashboard.php'),
    array('label' => 'Recensioni', 'href' => 'recensioni.php'),
    array('label' => 'Elimina', 'href' => ''),
);

$recensioneId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$recensione = null;

if ($conn instanceof mysqli && $errorMessage === '' && $recensioneId > 0) {
    $recensione = getRecensioneSingolaUser($conn, $_SESSION['user_id'], $recensioneId);
}

// Se la recensione non esiste o non appartiene all'utente, mostra errore
if (!$recensione || (int) $recensione['utente_id'] !== (int) $_SESSION['user_id']) {
    header('Location: recensioni.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $recensioneId > 0) {
    try{
        $result = censuraRecensioneUtente($conn, $recensioneId, $_SESSION['user_id']);
        if ($result === true) {
            //header('Location: ../user/recensioni.php?deleted=1');
            header('Location: recensioni.php'); // ../user/recensioni.php
            exit;
        }
        $errorMessage = is_string($result) ? $result : 'Eliminazione non riuscita.';
    } catch (Throwable $e) {
        $errorMessage = 'Impossibile eliminare la recensione: ' . $e->getMessage();
    }
}

// Vista di conferma (da creare, oppure includere una view esistente)
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/template/sidebar-user.php';
require_once __DIR__ . '/../views/showEliminaRecensioneUser.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}