<?php
require_once 'includes/resources.php';

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

$pageTitle       = htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8') . ' — BiblioTake';
$pageDescription = 'Scheda del libro ' . htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8') . ' di ' . htmlspecialchars($libro['autore'], ENT_QUOTES, 'UTF-8') . '. Informazioni, disponibilità e recensioni.';
$pageKeywords    = htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8') . ', ' . htmlspecialchars($libro['autore'], ENT_QUOTES, 'UTF-8') . ', biblioteca, prestito';
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
