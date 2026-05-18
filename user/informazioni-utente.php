<?php
require_once '../includes/resources.php';
requireRole('user');

$pageTitle = 'Informazioni Utente — User BiblioTake';
$currentPage = 'user';
$errorMessage = '';
$successMessage = '';

// Recupera le informazioni dell'utente loggato
if($conn instanceof mysqli && $errorMessage === ''){
    $userInfo = getUserInfo($conn, $_SESSION['user_id']);
    if(!$userInfo){
        $errorMessage = 'Impossibile recuperare le informazioni dell\'utente.';
    }
} 
else{
    $errorMessage = 'Connessione al database non disponibile.';
}   

// Carica la dashboard
require_once __DIR__ . '/../dashboard.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
?>