<?php
session_start();
require_once 'includes/resources.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors      = array();
$emailValore = '';
$intended    = isset($_GET['intended']) ? $_GET['intended'] : 'dashboard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailValore = isset($_POST['email'])    ? trim($_POST['email'])     : '';
    $password    = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if (!filter_var($emailValore, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Indirizzo email non valido.';
    }
    if (strlen($password) < MIN_PASSWORD_LENGTH) {
        $errors[] = 'La password deve contenere almeno ' . MIN_PASSWORD_LENGTH . ' caratteri.';
    }

    if (empty($errors)) {
        if (loginUser($conn, $emailValore, $password)) {
            // Sanifica intended per prevenire open redirect
            $safe = 'dashboard.php';
            if (
                preg_match('#^[a-zA-Z0-9_./?=&%-]+$#', $intended)
                && strpos($intended, '//') === false
                && strpos($intended, ':') === false
            ) {
                $safe = $intended;
            }
            header('Location: ' . $safe);
            exit;
        }
        $errors[] = 'Credenziali non valide o account non attivo.';
    }
}

$pageTitle       = 'Accedi a BiblioTake';
$pageDescription = 'Accedi al tuo account BiblioTake per gestire prestiti e recensioni.';
$pageKeywords    = 'login, accesso, account, biblioteca';
$currentPage     = 'login';
$breadcrumb      = array(
    array('label' => 'Home',   'href' => 'index.php'),
    array('label' => 'Accedi', 'href' => ''),
);

require_once 'views/template/header.php';
require_once 'views/showLogin.php';
require_once 'views/template/footer.php';
?>
