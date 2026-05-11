<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Prestiti utente — Admin BiblioTake';
$currentPage = 'admin';
$message = '';
$errorMessage = '';

$utenteId = isset($_GET['utente_id']) ? (int) $_GET['utente_id'] : (isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0);
$prestitoInModifica = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && isset($_POST['prestito_id'], $_POST['azione'])) {
    $prestitoId = (int) $_POST['prestito_id'];
    $azione = (string) $_POST['azione'];

    try {
        if ($azione === 'concludi') {
            $message = concludePrestito($conn, $prestitoId)
                ? 'Prestito concluso con successo.'
                : 'Operazione non riuscita.';
        } elseif ($azione === 'proroga') {
            $message = prorogaPrestito($conn, $prestitoId)
                ? 'Prestito prorogato di 30 giorni.'
                : 'Operazione non riuscita.';
        } elseif ($azione === 'elimina') {
            $message = deletePrestitoWithReferences($conn, $prestitoId)
                ? 'Prestito eliminato con successo.'
                : 'Operazione non riuscita.';
        } elseif ($azione === 'modifica') {
            $prestitoInModifica = getPrestitoById($conn, $prestitoId);
            if (!$prestitoInModifica) {
                $message = 'Prestito non trovato.';
            }
        } elseif ($azione === 'salva_modifica') {
            $dataInizioRaw = trim((string) ($_POST['data_inizio'] ?? ''));
            $dataFineRaw = trim((string) ($_POST['data_fine'] ?? ''));
            $dataInizio = str_replace('T', ' ', $dataInizioRaw);
            $dataFine = str_replace('T', ' ', $dataFineRaw);
            if (strlen($dataInizio) === 16) {
                $dataInizio .= ':00';
            }
            if (strlen($dataFine) === 16) {
                $dataFine .= ':00';
            }

            $dati = [
                'data_inizio' => $dataInizio,
                'data_fine' => $dataFine,
                'stato' => trim((string) ($_POST['stato'] ?? '')),
                'biblioteca_id' => 1,
                'libro_id' => (int) ($_POST['libro_id'] ?? 0),
                'utente_id' => (int) ($_POST['nuovo_utente_id'] ?? 0),
            ];

            $statiValidi = ['attivo', 'concluso', 'in_ritardo'];
            $formValido =
                $dati['data_inizio'] !== ''
                && $dati['data_fine'] !== ''
                && in_array($dati['stato'], $statiValidi, true)
                && $dati['biblioteca_id'] > 0
                && $dati['libro_id'] > 0
                && $dati['utente_id'] > 0
                && strtotime($dati['data_fine']) >= strtotime($dati['data_inizio']);

            if (!$formValido) {
                $message = 'Dati non validi: controlla campi, stato e date.';
                $prestitoInModifica = getPrestitoById($conn, $prestitoId);
            } else {
                if (updatePrestito($conn, $prestitoId, $dati)) {
                    $message = 'Prestito aggiornato con successo.';
                    $utenteId = $dati['utente_id'];
                } else {
                    $message = 'Aggiornamento non riuscito.';
                    $prestitoInModifica = getPrestitoById($conn, $prestitoId);
                }
            }
        }
    } catch (Throwable $e) {
        $message = 'Operazione non riuscita: ' . $e->getMessage();
    }
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
require_once __DIR__ . '/../views/showUtentiAdmin.php';

if ($conn instanceof mysqli) {
    $conn->close();
}