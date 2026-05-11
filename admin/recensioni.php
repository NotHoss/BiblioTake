<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Recensioni utenti — Admin BiblioTake';
$currentPage = 'admin';
$message = '';
$errorMessage = '';

$utenteId = isset($_GET['utente_id']) ? (int) $_GET['utente_id'] : (isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && isset($_POST['recensione_id'], $_POST['azione'])) {
    $recensioneId = (int) $_POST['recensione_id'];
    $azione = (string) $_POST['azione'];

    if ($azione === 'elimina' || $azione === 'censura') {
        try {
            if ($azione === 'elimina') {
                if (deleteRecensione($conn, $recensioneId)) {
                    $message = 'Operazione completata con successo.';
                } else {
                    $message = 'Operazione non riuscita.';
                }
            } else {
                if (censuraRecensione($conn, $recensioneId)) {
                    $message = 'Operazione completata con successo.';
                } else {
                    $message = 'Operazione non riuscita.';
                }
            }
        } catch (Throwable $e) {
            $message = 'Operazione non riuscita: ' . $e->getMessage();
        }
    }
}

$utenti = [];
$recensioni = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $utenti = getAllUtenti($conn, 200);
    $recensioni = getRecensioniAdmin($conn, $utenteId);
}
$adminViewMode = 'reviews';
require_once __DIR__ . '/../views/showUtentiAdmin.php';

if ($conn instanceof mysqli) {
    $conn->close();
}