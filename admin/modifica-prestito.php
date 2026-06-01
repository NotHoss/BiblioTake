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
    $res = ['message' => '', 'prestitoInModifica' => null, 'utenteId' => isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0, 'cerca' => trim((string) ($_POST['cerca'] ?? ''))];

    $prestitoIdPost = (int) ($_POST['prestito_id'] ?? 0);
    $azione = (string) ($_POST['azione'] ?? '');

    try {
        if ($azione === 'salva_modifica') {
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
                $res['message'] = 'Formato data non valido.';
                $formValido = false;
            }

            if ($formValido) {
                if (!isLibroExists($conn, $dati['libro_id'])) {
                    $res['message'] = 'Errore: il libro specificato non esiste.';
                    $formValido = false;
                } elseif (!isUtenteExists($conn, $dati['utente_id'])) {
                    $res['message'] = 'Errore: l\'utente specificato non esiste.';
                    $formValido = false;
                }
            }

            if (!$formValido) {
                if ($res['message'] === '') $res['message'] = 'Dati non validi: controlla campi, stato e date.';
                $res['prestitoInModifica'] = getPrestitoById($conn, $prestitoId);
            } else {
                if (updatePrestito($conn, $prestitoId, $dati)) {
                    $res['message'] = 'Prestito aggiornato con successo.';
                    $res['utenteId'] = $dati['utente_id'];
                    if (function_exists('aggiornaPrestitiScaduti')) {
                        aggiornaPrestitiScaduti($conn);
                    }
                } else {
                    $res['message'] = 'Aggiornamento non riuscito.';
                    $res['prestitoInModifica'] = getPrestitoById($conn, $prestitoId);
                }
            }
        } elseif ($azione === 'concludi') {
            $res['message'] = concludePrestito($conn, $prestitoId) ? 'Prestito concluso con successo.' : 'Operazione non riuscita.';
        } elseif ($azione === 'proroga') {
            $tmp = prorogaPrestito($conn, $prestitoId);
            if (is_array($tmp)) {
                $res['message'] = $tmp['message'] ?? 'Operazione non riuscita.';
            } elseif ($tmp === true) {
                $res['message'] = 'Prestito prorogato di 30 giorni.';
            } else {
                $res['message'] = 'Operazione non riuscita.';
            }
        } elseif ($azione === 'elimina') {
            $res['message'] = deletePrestitoWithReferences($conn, $prestitoId) ? 'Prestito eliminato con successo.' : 'Operazione non riuscita.';
        }
    } catch (Throwable $e) {
        $res['message'] = 'Operazione non riuscita: ' . $e->getMessage();
    }

    $message = $res['message'] ?? $message;
    $prestitoInModifica = $res['prestitoInModifica'] ?? null;

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
