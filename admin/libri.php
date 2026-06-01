<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Gestione Libri — Admin BiblioTake';
$pageDescription = 'Elenco e gestione dei libri nel catalogo della biblioteca.';
$pageKeywords = 'admin, libri, gestione, catalogo, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Gestione libri', 'href' => ''),
);
$currentPage = 'admin';

$errorMessage = '';

$filtri = [
    'cerca' => isset($_GET['cerca']) ? trim((string) $_GET['cerca']) : '',
    'autore' => isset($_GET['autore']) ? trim((string) $_GET['autore']) : '',
    'categoria' => isset($_GET['categoria']) ? trim((string) $_GET['categoria']) : '',
    'tag' => isset($_GET['tag']) ? trim((string) $_GET['tag']) : '',
    'anno' => isset($_GET['anno']) && $_GET['anno'] !== '' ? (int) $_GET['anno'] : '',
    'stato_libri' => isset($_GET['stato_libri']) ? trim((string) $_GET['stato_libri']) : 'tutti',
];

$resultsPerPage = 10;
$totalLibri = 0;
$totalPagine = 1;
$pagina = max(1, (int) $page);

$libri = [];
if ($conn instanceof mysqli && $errorMessage === '') {
    $totalLibri = countLibriAdminFiltrati($conn, $filtri);
    $totalPagine = max(1, (int) ceil($totalLibri / $resultsPerPage));
    $pagina = max(1, min($pagina, $totalPagine));
    $libri = getLibriAdminFiltrati($conn, $filtri, $pagina, $resultsPerPage);
}

$categorie = [];
if ($conn instanceof mysqli) {
    $categorie = getCategorieLibri($conn);
}
$tagsDisponibili = [];
if ($conn instanceof mysqli) {
    $tagsDisponibili = getAllTags($conn);
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showLibriAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}

