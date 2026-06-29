<?php
session_start();
require_once __DIR__ . '/../includes/resources.php';
requireRole('utente');

$pageTitle = 'Modifica Recensione — User BiblioTake';
$pageDescription = 'Modifica una recensione che hai scritto.';
$pageKeywords = 'user, modifica, recensione, libro, BiblioTake';
$currentPage = 'recensioni';
$errorMessage = '';
$successMessage = '';

$breadcrumb = array(
    array('label' => 'Home',     'href' => '../index.php', 'lang' => 'en'),
    array('label' => 'Profilo', 'href' => 'dashboard.php'),
    array('label' => 'Recensioni', 'href' => 'recensioni.php'),
    array('label' => 'Modifica', 'href' => ''),
);

$recensioneId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$recensione = null;

if ($conn instanceof mysqli && $recensioneId > 0) {
    $recensione = getRecensioneSingolaUser($conn, $_SESSION['user_id'], $recensioneId);
}

//se la recensione non esiste, non e' dell'utente o e' censurata, torna alla lista
if (!$recensione || (int) $recensione['utente_id'] !== (int) $_SESSION['user_id']) {
    header('Location: recensioni.php');
    exit;
}

//return=libro: l'azione e' partita dalla pagina del libro, quindi a fine operazione si torna li. l'id libro viene dal record (fidato), non dall'input
$return = isset($_GET['return']) ? $_GET['return'] : (isset($_POST['return']) ? $_POST['return'] : '');
if ($return === 'libro') {
    $redirectSuccesso = '../dettaglio-libro.php?id=' . (int) $recensione['libro_id'];
} else {
    $redirectSuccesso = 'recensioni.php?msg=recensione_modificata';
}

//gestisce l'invio del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testo = isset($_POST['testo']) ? trim($_POST['testo']) : '';
    $voto  = isset($_POST['valutazione']) ? (int) $_POST['valutazione'] : 0;

    $validazione = validateRecensioneUserData(['testo' => $testo, 'valutazione' => $voto]);
    if (!$validazione['ok']) {
        $errorMessage = $validazione['errorMessage'];
        $recensione['testo'] = $testo;
        $recensione['valutazione'] = $voto;
    } else {
        $risultato = updateRecensioneUtente($conn, $recensioneId, $_SESSION['user_id'], $validazione['dati']['testo'], $validazione['dati']['valutazione']);
        if ($risultato === true) {
            header('Location: ' . $redirectSuccesso);
            exit;
        }
        $errorMessage = is_string($risultato) ? $risultato : 'Modifica non riuscita.';
        $recensione['testo'] = $testo;
        $recensione['valutazione'] = $voto;
    }
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/template/sidebar-user.php';
require_once __DIR__ . '/../views/showModificaRecensione.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
