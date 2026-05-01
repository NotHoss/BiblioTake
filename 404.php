<?php
http_response_code(404);
require_once 'includes/resources.php';

$pageTitle       = 'Pagina non trovata — BiblioTake';
$pageDescription = 'La pagina che stai cercando non esiste o è stata spostata.';
$pageKeywords    = 'pagina non trovata, errore, 404';
$currentPage     = '';

require_once 'views/template/header.php';
require_once 'views/show404.php';
require_once 'views/template/footer.php';
?>
