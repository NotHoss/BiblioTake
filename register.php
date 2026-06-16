<?php
session_start();
require_once 'includes/resources.php';

// Link relativo per mantenere il progetto portabile.
if (isLoggedIn()) {
    header('Location: user/dashboard.php');
    exit;
}

$errors          = array();
$emailValore     = '';
$usernameValore  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailValore     = isset($_POST['email'])     ? trim($_POST['email'])      : '';
    $usernameValore  = isset($_POST['username'])  ? trim($_POST['username'])   : '';
    $password        = isset($_POST['password'])  ? (string) $_POST['password']        : '';
    $passwordConferma = isset($_POST['password-conferma']) ? (string) $_POST['password-conferma'] : '';

    if (!filter_var($emailValore, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Indirizzo email non valido.';
    }
    if (
        strlen($usernameValore) < MIN_USERNAME_LENGTH
        || strlen($usernameValore) > MAX_USERNAME_LENGTH
        || !preg_match('/^[a-zA-Z0-9._-]+$/', $usernameValore)
    ) {
        $errors[] = 'Username non valido: da ' . MIN_USERNAME_LENGTH . ' a ' . MAX_USERNAME_LENGTH
                  . ' caratteri, solo lettere, numeri e i simboli . _ -';
    }
    if (strlen($password) < MIN_PASSWORD_LENGTH) {
        $errors[] = 'La password deve contenere almeno ' . MIN_PASSWORD_LENGTH . ' caratteri.';
    }
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'La password deve contenere almeno una lettera e un numero.';
    }
    if ($password !== $passwordConferma) {
        $errors[] = 'Le due password inserite non coincidono.';
    }

    if (empty($errors)) {
        if (registerUser($conn, $emailValore, $usernameValore, $password)) {
                if (loginUser($conn, $emailValore, $password)) {
                header('Location: user/dashboard.php?benvenuto=1');
                exit;
            }
            header('Location: login.php?registrato=1');
            exit;
        }
        $errors[] = 'Esiste gia un account con questa email.';
    }
}

$pageTitle       = 'Registrazione a BiblioTake';
$pageDescription = 'Crea un nuovo account su BiblioTake per richiedere prestiti e scrivere recensioni.';
$pageKeywords    = 'registrazione, nuovo account, iscriviti, biblioteca';
$currentPage     = 'register';
$breadcrumb      = array(
    array('label' => 'Home',        'href' => 'index.php', 'lang' => 'en'),
    array('label' => 'Registrati',  'href' => ''),
);

require_once 'views/template/header.php';
require_once 'views/showRegister.php';
require_once 'views/template/footer.php';
?>
