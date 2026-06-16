<?php
session_start();
require_once 'includes/resources.php';

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin/index.php' : 'index.php'));
    exit;
}

$errors      = array();
$loginValore = '';
$intended    = isset($_GET['intended']) ? $_GET['intended'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //campo login accetta email o username per permettere accesso con admin/admin
    $loginValore = isset($_POST['login'])    ? trim($_POST['login'])     : '';
    $password    = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($loginValore === '') {
        $errors[] = 'Inserisci email o username.';
    }
    //niente check su lunghezza minima password: bloccherebbe credenziali corte come admin/admin richieste dalla prof
    if ($password === '') {
        $errors[] = 'Inserisci la password.';
    }

    if (empty($errors)) {
        if (loginUser($conn, $loginValore, $password)) {
            // Sanifica intended per prevenire open redirect
            $safe = isAdmin() ? 'admin/index.php' : 'index.php';
            if ($intended !== '' &&
                preg_match('#^[a-zA-Z0-9_./?=&%-]+$#', $intended)
                && $intended[0] !== '/'
                && strpos($intended, '..') === false
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
    array('label' => 'Home',   'href' => 'index.php', 'lang' => 'en'),
    array('label' => 'Accedi', 'href' => ''),
);

require_once 'views/template/header.php';
require_once 'views/showLogin.php';
require_once 'views/template/footer.php';
?>
