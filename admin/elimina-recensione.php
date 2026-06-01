<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Elimina recensione — Admin BiblioTake';
$pageDescription = 'Conferma eliminazione di una recensione.';
$pageKeywords = 'admin, recensioni, eliminazione, BiblioTake';
$breadcrumb = array(
	array('label' => 'Home', 'href' => '../index.php'),
	array('label' => 'Admin', 'href' => 'index.php'),
	array('label' => 'Gestione recensioni', 'href' => 'recensioni.php'),
	array('label' => 'Elimina recensione', 'href' => ''),
);
$currentPage = 'admin';

$recensione = null;
$utenteId = isset($_GET['utente_id']) ? (int) $_GET['utente_id'] : 0;
if ($conn instanceof mysqli && isset($_GET['id'])) {
	$recensione = getRecensioneById($conn, (int) $_GET['id']);
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showEliminaRecensioneAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
	$conn->close();
}
