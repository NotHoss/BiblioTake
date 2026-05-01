<?php
require_once 'includes/resources.php';

$pageTitle       = 'BiblioTake — Biblioteca digitale';
$pageDescription = 'Scopri il catalogo della biblioteca: cerca libri, consulta le schede e richiedi un prestito.';
$pageKeywords    = 'biblioteca, libri, catalogo, prestito, BiblioTake';
$currentPage     = 'home';

$libriRecenti = getLibriRecenti($conn, 6);
$categorie    = getCategorie($conn);

require_once 'views/template/header.php';
require_once 'views/showIndex.php';
require_once 'views/template/footer.php';
?>
