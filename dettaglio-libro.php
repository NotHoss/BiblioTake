<?php
require_once 'includes/resources.php';

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

$tags        = getTagsByLibroId($conn, $libroId);
$recensioni  = getRecensioniByLibroId($conn, $libroId);
$disponibile = isLibroDisponibile($conn, $libroId);

require_once 'views/template/header.php';
require_once 'views/showDettaglioLibro.php';
require_once 'views/template/footer.php';
?>
