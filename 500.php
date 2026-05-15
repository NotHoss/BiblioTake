<?php
http_response_code(500);
require_once 'includes/resources.php';

$pageTitle       = 'Errore del server — BiblioTake';
$pageDescription = 'Si è verificato un errore interno del server. Riprova tra qualche minuto.';
$pageKeywords    = '500, errore server, problema tecnico';
$currentPage     = '';
$breadcrumb      = array(
    array('label' => 'Home',       'href' => 'index.php'),
    array('label' => 'Errore 500', 'href' => ''),
);

require_once 'views/template/header.php';
require_once 'views/show500.php';
require_once 'views/template/footer.php';
?>
