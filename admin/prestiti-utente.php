<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione prestiti utenti — Admin BiblioTake';
$pageDescription = 'Visualizza e gestisci i prestiti di uno specifico utente.';
$pageKeywords = 'admin, prestiti, utente, gestione, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Utenti', 'href' => 'utenti.php'),
    array('label' => 'Prestiti', 'href' => ''),
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
    $res = handlePrestitiActions($conn, $_POST);
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