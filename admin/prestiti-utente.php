<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione prestiti — Admin BiblioTake';
$pageDescription = 'Visualizza e gestisci i prestiti di uno specifico utente.';
$pageKeywords = 'admin, prestiti, utente, gestione, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Gestione utenti', 'href' => 'utenti.php'),
    array('label' => 'Gestione prestiti', 'href' => ''),
);
$currentPage = 'admin';
$message = '';
$errorMessage = '';

$prestitoId = isset($_GET['prestito_id']) ? (int) $_GET['prestito_id'] : 0;
$utenteId = isset($_GET['utente_id']) ? (int) $_GET['utente_id'] : (isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0);
$filtri = [
    'cerca' => isset($_GET['cerca']) ? trim((string) $_GET['cerca']) : '',
];
$utenteFiltro = null;
if ($filtri['cerca'] === '' && $utenteId > 0 && $conn instanceof mysqli) {
    $utenteFiltro = getUserInfo($conn, $utenteId);
    if (!empty($utenteFiltro['username'])) {
        $filtri['cerca'] = (string) $utenteFiltro['username'];
    }
}
$resultsPerPage = 10;
$totalPrestiti = 0;
$totalPagine = 1;
$pagina = max(1, (int) $page);
$prestitoInRequest = null;
$prestitoInModifica = null;

$res = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && isset($_POST['prestito_id'], $_POST['azione'])) {
    $res = ['message' => '', 'prestitoInModifica' => null, 'utenteId' => isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0, 'cerca' => trim((string) ($_POST['cerca'] ?? ''))];

    $prestitoId = (int) ($_POST['prestito_id'] ?? 0);
    $azione = (string) ($_POST['azione'] ?? '');

    try {
        if ($azione === 'concludi') {
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
        } elseif ($azione === 'modifica') {
            $prestito = getPrestitoById($conn, $prestitoId);
            if (!$prestito) {
                $res['message'] = 'Prestito non trovato.';
            } else {
                $res['prestitoInModifica'] = $prestito;
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
        }
    } catch (Throwable $e) {
        $res['message'] = 'Operazione non riuscita: ' . $e->getMessage();
    }

    $message = $res['message'] ?? $message;
    $prestitoInModifica = $res['prestitoInModifica'] ?? $prestitoInModifica;
    $utenteId = $res['utenteId'] ?? $utenteId;
}

$utenti = [];
$prestiti = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    // If a specific prestito is requested, load its owner and later filter results
    if ($prestitoId > 0) {
        $prestitoInRequest = getPrestitoById($conn, $prestitoId);
        if (!empty($prestitoInRequest) && !empty($prestitoInRequest['utente_id'])) {
            $utenteId = (int) $prestitoInRequest['utente_id'];
        }
        // If a specific prestito was requested, prefill the search box with its ID
        if (empty($filtri['cerca'])) {
            $filtri['cerca'] = (string) $prestitoId;
        }
    }

    $totalPrestiti = countPrestitiAdminFiltrati($conn, $filtri);
    $totalPagine = max(1, (int) ceil($totalPrestiti / $resultsPerPage));
    $pagina = max(1, min($pagina, $totalPagine));
    $prestiti = getPrestitiAdminFiltrati($conn, $filtri, $pagina, $resultsPerPage);

    // If a specific prestito was requested, filter the list to only that prestito
    if ($prestitoId > 0) {
        $prestiti = array_values(array_filter($prestiti, function ($p) use ($prestitoId) {
            return (int) ($p['id'] ?? 0) === $prestitoId;
        }));
        $totalPrestiti = count($prestiti);
        $totalPagine = 1;
        // Also set the prestitoInModifica to the requested one so the form can populate
        if (!empty($prestiti)) {
            // Prefer the full record fetched by ID (contains libro_id, utente_id)
            if (!empty($prestitoInRequest)) {
                $prestitoInModifica = $prestitoInRequest;
            } else {
                $prestitoInModifica = $prestiti[0];
            }
        }
    }
}
$adminViewMode = 'prestiti';
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showPrestitiAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}