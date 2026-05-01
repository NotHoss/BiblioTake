<?php
http_response_code(403);
require_once 'includes/resources.php';

$pageTitle       = 'Accesso negato — BiblioTake';
$pageDescription = 'Non hai i permessi necessari per accedere a questa pagina.';
$pageKeywords    = 'accesso negato, errore, 403';
$currentPage     = '';

require_once 'views/template/header.php';
require_once 'views/show403.php';
require_once 'views/template/footer.php';
?>
