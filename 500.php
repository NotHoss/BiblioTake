<?php
http_response_code(500);
require_once 'includes/resources.php';

$pageTitle       = 'Errore del server — BiblioTake';
$pageDescription = 'Si è verificato un errore interno del server. Riprova tra qualche minuto.';
$pageKeywords    = 'errore server, 500, problema tecnico';
$currentPage     = '';

require_once 'views/template/header.php';
require_once 'views/show500.php';
require_once 'views/template/footer.php';
?>
