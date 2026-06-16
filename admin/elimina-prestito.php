<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Elimina prestito - Amministrazione BiblioTake';
$pageDescription = 'Elimina un prestito dal pannello amministrativo.';
$pageKeywords = 'amministrazione, prestiti, eliminazione, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php', 'lang' => 'en'),
    array('label' => 'Admin', 'href' => 'index.php', 'lang' => 'en'),
    array('label' => 'Gestione prestiti', 'href' => 'prestiti-utente.php?utente_id=0'),
    array('label' => 'Elimina prestito', 'href' => ''),
);
$currentPage = 'admin';
$message = '';
$errorMessage = '';
$returnUrl = getSafeAdminReturnUrl('prestiti-utente.php');

$prestitoId = isset($_GET['prestito_id']) ? (int) $_GET['prestito_id'] : (isset($_POST['prestito_id']) ? (int) $_POST['prestito_id'] : 0);
$utenteId = isset($_GET['utente_id']) ? (int) $_GET['utente_id'] : 0;

if ($prestitoId <= 0) {
    $errorMessage = 'ID prestito non valido.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $prestitoId > 0 && $conn instanceof mysqli) {
    $res = ['message' => '', 'utenteId' => isset($_POST['utente_id']) ? (int) $_POST['utente_id'] : 0, 'cerca' => trim((string) ($_POST['cerca'] ?? ''))];
    $azione = (string) ($_POST['azione'] ?? '');

    try {
        if ($azione === 'elimina') {
            $res['message'] = deletePrestitoWithReferences($conn, $prestitoId) ? 'Prestito eliminato con successo.' : 'Operazione non riuscita.';
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
        }
    } catch (Throwable $e) {
        $res['message'] = 'Operazione non riuscita: ' . $e->getMessage();
    }

    $message = $res['message'] ?? $message;
    header('Location: ' . appendAdminQueryParam($returnUrl, ['deleted' => 1]));
    exit;
}

$prestito = null;
if ($conn instanceof mysqli && $prestitoId > 0) {
    $prestito = getPrestitoById($conn, $prestitoId);
    if (!empty($prestito['utente_id'])) {
        $utenteId = (int) $prestito['utente_id'];
    }
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showEliminaPrestitoAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) $conn->close();
