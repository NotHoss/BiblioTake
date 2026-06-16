<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Modifica prestito - Amministrazione BiblioTake';
$pageDescription = 'Modifica i dettagli di un prestito.';
$pageKeywords = 'amministrazione, prestiti, modifica, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Gestione prestiti', 'href' => 'prestiti-utente.php?utente_id=0'),
    array('label' => 'Modifica prestito', 'href' => ''),
);
$currentPage = 'admin';
$message = '';
$errorMessage = '';
$returnUrl = getSafeAdminReturnUrl('prestiti-utente.php');

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
            $validation = validatePrestitoAdminData($conn, [
                'data_inizio' => $_POST['data_inizio'] ?? '',
                'data_fine' => $_POST['data_fine'] ?? '',
                'stato' => $_POST['stato'] ?? '',
                'biblioteca_id' => 1,
                'libro_id' => $_POST['libro_id'] ?? 0,
                'nuovo_utente_id' => $_POST['nuovo_utente_id'] ?? 0,
            ]);

            $dati = $validation['dati'];

            if (!$validation['ok']) {
                $res['message'] = $validation['errorMessage'] !== '' ? $validation['errorMessage'] : 'Dati non validi: controlla campi, stato e date.';
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
        header('Location: ' . appendAdminQueryParam($returnUrl, ['updated' => 1]));
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
