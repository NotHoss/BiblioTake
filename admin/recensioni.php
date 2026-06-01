<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione recensioni — Admin BiblioTake';
$pageDescription = 'Controlla e gestisci le recensioni inviate dagli utenti.';
$pageKeywords = 'admin, recensioni, utenti, gestione, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Gestione utenti', 'href' => 'utenti.php'),
    array('label' => 'Gestione recensioni', 'href' => ''),
);
$currentPage = 'admin';
$message = '';
$errorMessage = '';

$utenteId = isset($_GET['utente_id']) ? (int) $_GET['utente_id'] : (isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0);
$filtri = [
    'cerca' => isset($_GET['cerca']) ? trim((string) $_GET['cerca']) : '',
    'voto' => isset($_GET['voto']) ? (int) $_GET['voto'] : 0,
    'stato_recensione' => isset($_GET['stato_recensione']) ? trim((string) $_GET['stato_recensione']) : 'tutte',
];
$utenteFiltro = null;
if ($filtri['cerca'] === '' && $utenteId > 0 && $conn instanceof mysqli) {
    $utenteFiltro = getUserInfo($conn, $utenteId);
    if (!empty($utenteFiltro['username'])) {
        $filtri['cerca'] = (string) $utenteFiltro['username'];
    }
}
$resultsPerPage = 10;
$totalRecensioni = 0;
$totalPagine = 1;
$pagina = max(1, (int) $page);

$res = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '') {
    $res = ['message' => ''];
    if (!isset($_POST['recensione_id'], $_POST['azione'])) {
        $res['message'] = '';
    } else {
        $recensioneId = (int) $_POST['recensione_id'];
        $azione = (string) $_POST['azione'];
        try {
            if ($azione === 'elimina') {
                $res['message'] = deleteRecensione($conn, $recensioneId) ? 'Operazione completata con successo.' : 'Operazione non riuscita.';
            } elseif ($azione === 'censura') {
                $res['message'] = censuraRecensione($conn, $recensioneId) ? 'Operazione completata con successo.' : 'Operazione non riuscita.';
            }
        } catch (Throwable $e) {
            $res['message'] = 'Operazione non riuscita: ' . $e->getMessage();
        }
    }
    $message = $res['message'] ?? $message;
}

$utenti = [];
$recensioni = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $totalRecensioni = countRecensioniAdminFiltrate($conn, $filtri);
    $totalPagine = max(1, (int) ceil($totalRecensioni / $resultsPerPage));
    $pagina = max(1, min($pagina, $totalPagine));
    $recensioni = getRecensioniAdminFiltrate($conn, $filtri, $pagina, $resultsPerPage);
}
$adminViewMode = 'reviews';
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showRecensioniAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}