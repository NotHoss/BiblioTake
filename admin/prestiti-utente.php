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
            $res = prorogaPrestito($conn, $prestitoId);
            if (is_array($res)) {
                $message = $res['message'] ?? 'Operazione non riuscita.';
            } elseif ($res === true) {
                $message = 'Prestito prorogato di 30 giorni.';
            } else {
                $message = 'Operazione non riuscita.';
            }
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
            $dataInizioNorm = normalizeDateTimeForDb(trim((string) ($_POST['data_inizio'] ?? '')));
            $dataFineNorm = normalizeDateTimeForDb(trim((string) ($_POST['data_fine'] ?? '')));

            $dati = [
                'data_inizio' => $dataInizioNorm ?? '',
                'data_fine' => $dataFineNorm ?? '',
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

            if ($dataInizioNorm === null || $dataFineNorm === null) {
                $message = 'Formato data non valido.';
                $formValido = false;
            }

            // Validazione aggiuntiva: verifica che libro e utente esistano
            if ($formValido) {
                if (!isLibroExists($conn, $dati['libro_id'])) {
                    $message = 'Errore: il libro specificato non esiste.';
                    $formValido = false;
                } elseif (!isUtenteExists($conn, $dati['utente_id'])) {
                    $message = 'Errore: l\'utente specificato non esiste.';
                    $formValido = false;
                }
            }

            if (!$formValido) {
                if ($message === '') {
                    $message = 'Dati non validi: controlla campi, stato e date.';
                }
                $prestitoInModifica = getPrestitoById($conn, $prestitoId);
            } else {
                if (updatePrestito($conn, $prestitoId, $dati)) {
                    $message = 'Prestito aggiornato con successo.';
                    $utenteId = $dati['utente_id'];
                    if (function_exists('aggiornaPrestitiScaduti')) {
                        aggiornaPrestitiScaduti($conn);
                    }
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
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showUtentiAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}