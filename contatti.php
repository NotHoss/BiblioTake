<?php
require_once 'includes/resources.php';

$pageTitle       = 'Contatti — BiblioTake';
$pageDescription = 'Contatta la biblioteca BiblioTake per informazioni su prestiti, catalogo e servizi offerti.';
$pageKeywords    = 'contatti, biblioteca, BiblioTake, informazioni, email, telefono';
$currentPage     = 'contatti';

$breadcrumb = array(
    array('label' => 'Home',     'href' => 'index.php', 'lang' => 'en'),
    array('label' => 'Contatti', 'href' => ''),
);

$biblioteca = null;
if ($conn instanceof mysqli) {
    $biblioteca = getBibliotecaInfo($conn);
}

require_once 'views/template/header.php';
require_once 'views/showContatti.php';
require_once 'views/template/footer.php';
?>
