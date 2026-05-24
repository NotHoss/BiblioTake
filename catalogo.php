<?php
require_once 'includes/resources.php';

$pageTitle       = 'Catalogo libri — BiblioTake';
$pageDescription = 'Sfoglia il catalogo completo della biblioteca. Filtra per categoria, autore, anno o disponibilità.';
$pageKeywords    = 'catalogo, libri, biblioteca, ricerca, filtri, prestito';
$currentPage     = 'catalogo';
$breadcrumb      = array(
    array('label' => 'Home',     'href' => 'index.php'),
    array('label' => 'Catalogo', 'href' => ''),
);

$filtri = [
    'cerca'       => isset($_GET['cerca'])       ? trim($_GET['cerca'])       : '',
    'categoria'   => isset($_GET['categoria'])   ? trim($_GET['categoria'])   : '',
    'autore'      => isset($_GET['autore'])       ? trim($_GET['autore'])      : '',
    'anno'        => isset($_GET['anno']) && $_GET['anno'] !== '' ? (int) $_GET['anno'] : '',
    'disponibile' => isset($_GET['disponibile']) ? $_GET['disponibile']        : '',
    'ordine'      => isset($_GET['ordine'])       ? $_GET['ordine']            : '',
];

$totalLibri  = countLibri($conn, $filtri);
$totalPagine = (int) ceil($totalLibri / MAX_PER_PAGINA);
$totalPagine = max(1, $totalPagine);
$pagina      = max(1, min($page, $totalPagine));

$libri     = getLibri($conn, $filtri, $pagina);
$categorie = getCategorie($conn);

require_once 'views/template/header.php';
require_once 'views/showCatalogo.php';
require_once 'views/template/footer.php';
?>
