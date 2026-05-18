<?php
session_start();
require_once '../includes/resources.php';
requireRole('user');

$pageTitle = 'Elimina recensione — User BiblioTake';
$currentPage = 'user';
$errorMessage = '';

$recensioneId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

$recensione = null;
if ($conn instanceof mysqli && $errorMessage === '' && $recensioneId > 0) {
    $recensione = getRecensioneById($conn, $recensioneId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $recensioneId > 0) {
    try {
        if (deleteRecensione($conn, $recensioneId, $_SESSION['user_id'], 'user')) {
            header('Location: ../user/recensioni.php?deleted=1');
            exit;
        }
        $errorMessage = 'Eliminazione non riuscita.';
    } catch (Throwable $e) {
        $errorMessage = 'Impossibile eliminare la recensione: ' . $e->getMessage();
    }
}

// Se la recensione non esiste o non appartiene all'utente, mostra errore
if (!$recensione || $recensione['utente_id'] !== $_SESSION['user_id']) {
    $errorMessage = 'Recensione non trovata o non autorizzata.';
}

// Vista di conferma (da creare, oppure includere una view esistente)
require_once __DIR__ . '/../views/showRecensioniUser.php';

if ($conn instanceof mysqli) {
    $conn->close();
}