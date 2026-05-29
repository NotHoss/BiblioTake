<?php
session_start();
require_once '../includes/resources.php';
//require_once '../includes/functions/user_functions.php';
requireRole('utente');

$pageTitle = 'Profilo | BiblioTake';
$currentPage = 'dashboard';
$errorMessage = '';
$successMessage = '';

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0  ;
if ($conn instanceof mysqli && $errorMessage === '' && $userId > 0) {
    $userInfo = getUserInfo($conn, $userId);
}
else {
    $errorMessage = 'Connessione al database non disponibile.';
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $userId > 0) {
    //if(isset($_POST['delete_account']) && $_POST['delete_account'] === 'on' && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'on'){
        $result = deleteUtente($conn, $userInfo['id']);
        if($result === true){
            session_destroy();
            header('Location: /index.php');
            exit();
        }
        else{
            $errorMessage = is_string($result) ? $result : 'Si è verificato un errore durante l\'eliminazione dell\'account.';
        }
    //}
}