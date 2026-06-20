<?php
session_start();
require_once __DIR__ . '/../includes/resources.php';
//pagina protetta: solo utenti loggati con ruolo utente. coerente con le altre pagine in user/.
requireRole('utente');

$pageTitle = 'Area personale — BiblioTake';
$pageDescription = 'Area personale di BiblioTake: gestisci il tuo profilo, consulta i prestiti attivi e passati e le recensioni dei libri della biblioteca.';
$pageKeywords = 'BiblioTake, area personale, profilo utente, prestiti, recensioni';
$currentPage = 'dashboard';
$errorMessage = '';
$successMessage = '';

$user = getUserInfo($conn, $_SESSION['user_id']);

$breadcrumb = array(
    array('label' => 'Home',    'href' => '../index.php', 'lang' => 'en'),
    array('label' => 'Profilo', 'href' => ''),
);

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/template/sidebar-user.php';
require_once __DIR__ . '/../views/showDashboard.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
