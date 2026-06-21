<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione utenti - Amministrazione BiblioTake';
$pageDescription = 'Gestione degli account utenti, prestiti e ruoli della biblioteca.';
$pageKeywords = 'amministrazione, utenti, gestione, prestiti, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php', 'lang' => 'en'),
    array('label_parts' => array(array('text' => 'Dashboard', 'lang' => 'en'), array('text' => ' amministrazione')), 'href' => 'index.php'),
    array('label' => 'Gestione utenti', 'href' => ''),
);
$currentPage = 'admin';

$errorMessage = '';
$message = '';

$filtri = [
    'cerca' => isset($_GET['cerca']) ? trim((string) $_GET['cerca']) : '',
    'stato_utenti' => isset($_GET['stato_utenti']) ? trim((string) $_GET['stato_utenti']) : 'tutti',
];

$resultsPerPage = 10;
$totalUtenti = 0;
$totalPagine = 1;
$pagina = max(1, (int) $page);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '') {
    $utenteId = isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0;
    $azione = isset($_POST['azione']) ? (string) $_POST['azione'] : '';

    if ($utenteId > 0 && in_array($azione, ['disattiva', 'riattiva'], true)) {
        $attivo = $azione === 'riattiva';
        try {
            $result = setUtenteAttivoAdmin($conn, $utenteId, $attivo);
            if ($result === true) {
                $message = $attivo ? 'Utente riattivato con successo.' : 'Utente disattivato con successo.';
            } else {
                $errorMessage = is_string($result) ? $result : 'Operazione non riuscita.';
            }
        } catch (Throwable $e) {
            $errorMessage = 'Operazione non riuscita: ' . $e->getMessage();
        }
    } else {
        $errorMessage = 'Azione utente non valida.';
    }
}

$utenti = [];
if ($conn instanceof mysqli) {
    $totalUtenti = countUtentiConPrestitiFiltrati($conn, $filtri);
    $totalPagine = max(1, (int) ceil($totalUtenti / $resultsPerPage));
    $pagina = max(1, min($pagina, $totalPagine));
    $utenti = getUtentiConPrestitiFiltrati($conn, $filtri, $pagina, $resultsPerPage);
}
$adminViewMode = 'list';
$hideAdminNav = true;
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showUtentiAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}

