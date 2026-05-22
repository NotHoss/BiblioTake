<?php
require_once 'includes/resources.php';

$pageTitle       = 'Chi siamo — BiblioTake';
$pageDescription = 'Informazioni su BiblioTake, il sistema di gestione della biblioteca sviluppato per il corso di Tecnologie Web di UNIPD.';
$pageKeywords    = 'BiblioTake, biblioteca, chi siamo, informazioni, UNIPD';
$currentPage     = 'about';
$breadcrumb      = array(
    array('label' => 'Home',      'href' => 'index.php'),
    array('label' => 'Chi siamo', 'href' => ''),
);

require_once 'views/template/header.php';
require_once 'views/showAbout.php';
require_once 'views/template/footer.php';
?>
