<?php
http_response_code(404);
require_once 'includes/resources.php';

$pageTitle       = 'Pagina non trovata';
$pageDescription = 'La pagina richiesta non esiste o e stata spostata.';
$pageKeywords    = '404, pagina non trovata, errore';
$currentPage     = '';
$breadcrumb      = array(
    array('label' => 'Home',     'href' => 'index.php'),
    array('label' => 'Errore 404', 'href' => ''),
);

require_once 'views/template/header.php';
require_once 'views/show404.php';
require_once 'views/template/footer.php';
?>
