<?php
session_start();
require_once __DIR__ . '/../includes/resources.php';
requireRole('utente');

$pageTitle = 'Aggiungi Recensione — User BiblioTake';
$pageDescription = 'Aggiungi una recensione per un libro.';
$pageKeywords = 'user, recensione, libro, BiblioTake';
$currentPage = 'recensioni';
$errorMessage = '';
$successMessage = '';

$breadcrumb = array(
    array('label' => 'Home',     'href' => '../index.php', 'lang' => 'en'),
    array('label' => 'Libro', 'href' => 'dashboard.php'),
    array('label' => 'Aggiungi Recensione', 'href' => ''),
);

// Mostra il form
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $libroId = isset($_GET['libro_id']) ? (int) $_GET['libro_id'] : 0;

    if ($libroId <= 0) {
        header('Location: ../404.php');
        exit;
    }

    $libro = getLibroById($conn, $libroId);
    if (!$libro) {
        header('Location: ../404.php');
        exit;
    }

    require_once __DIR__ . '/../views/template/header.php';
    require_once __DIR__ . '/../views/showAggiungiRecensione.php';
    require_once __DIR__ . '/../views/template/footer.php';

    if ($conn instanceof mysqli) { $conn->close(); }
    exit;
}

// Gestisce l'invio del form
$libroId = isset($_POST['libro_id']) ? (int) $_POST['libro_id'] : 0;
$testo   = isset($_POST['testo']) ? trim($_POST['testo']) : '';
$voto    = isset($_POST['valutazione']) ? (int) $_POST['valutazione'] : null;

if ($libroId <= 0) {
    header('Location: ../404.php');
    exit;
}

$libro = getLibroById($conn, $libroId);
if (!$libro) {
    header('Location: ../404.php');
    exit;
}

if ($voto < 1 || $voto > 5 || $testo === '') {
    $errorMessage = 'Compila tutti i campi e seleziona una valutazione valida.';
} else {
    $risultato = aggiungiRecensione($conn, $_SESSION['user_id'], $libroId, $testo, $voto);

    if ($risultato === true) {
        //torna alla pagina del libro cosi' l'utente vede subito la recensione appena scritta
        header('Location: ../dettaglio-libro.php?id=' . $libroId);
        exit;
    } else {
        $errorMessage = $risultato;
    }
}

// Se c'è un errore, mostra di nuovo il form
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showAggiungiRecensione.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) { $conn->close(); }
