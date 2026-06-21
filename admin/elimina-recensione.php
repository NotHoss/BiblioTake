<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Elimina recensione - Amministrazione BiblioTake';
$pageDescription = 'Conferma eliminazione di una recensione.';
$pageKeywords = 'amministrazione, recensioni, eliminazione, BiblioTake';
$breadcrumb = array(
	array('label' => 'Home', 'href' => '../index.php', 'lang' => 'en'),
	array('label_parts' => array(array('text' => 'Dashboard', 'lang' => 'en'), array('text' => ' amministrazione')), 'href' => 'index.php'),
	array('label' => 'Gestione recensioni', 'href' => 'recensioni.php'),
	array('label' => 'Elimina recensione', 'href' => ''),
);
$currentPage = 'admin';
$returnUrl = getSafeAdminReturnUrl('recensioni.php');

$recensione = null;
$recensioneId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $recensioneId > 0) {
	deleteRecensione($conn, $recensioneId);
	header('Location: ' . appendAdminQueryParam($returnUrl, ['deleted' => 1]));
	exit;
}

if ($conn instanceof mysqli && $recensioneId > 0) {
	$recensione = getRecensioneById($conn, $recensioneId);
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showEliminaRecensioneAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
	$conn->close();
}
