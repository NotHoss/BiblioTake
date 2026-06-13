<?php
require_once 'includes/resources.php';

//sessione avviata qui perche isLoggedIn() viene chiamato prima di header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$libroId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($libroId <= 0) {
    header('Location: 404.php');
    exit;
}

$libro = getLibroById($conn, $libroId);

if ($libro === null) {
    header('Location: 404.php');
    exit;
}

//niente htmlspecialchars qui, ci pensa header.php a fare l'escape
$pageTitle       = $libro['titolo'] . ' — BiblioTake';
$pageDescription = 'Scheda del libro ' . $libro['titolo'] . ' di ' . $libro['autore'] . '. Informazioni, disponibilità e recensioni.';
$pageKeywords    = $libro['titolo'] . ', ' . $libro['autore'] . ', biblioteca, prestito';
$currentPage     = 'catalogo';
$breadcrumb      = array(
    array('label' => 'Home',                           'href' => 'index.php'),
    array('label' => 'Catalogo',                       'href' => 'catalogo.php'),
    array('label' => $libro['titolo'],                 'href' => ''),
);

$tags          = getTagsByLibroId($conn, $libroId);
$recensioni    = getRecensioniByLibroId($conn, $libroId);
$disponibile   = isLibroDisponibile($conn, $libroId);
$utenteLoggato = isLoggedIn();

require_once 'views/template/header.php';
require_once 'views/showDettaglioLibro.php';
require_once 'views/template/footer.php';
?>
