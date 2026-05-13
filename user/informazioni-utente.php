<?php
require_once '../includes/resources.php';
requireRole('user');

$pageTitle = 'Informazioni Utente — User BiblioTake';
$currentPage = 'user';
//$message = '';
//$errorMessage = '';

function getUserInfo($conn, $userId) {
    $stmt = $conn->prepare("SELECT nome, email, foto_profilo FROM utente WHERE id = ?");
    
    /*if (!$stmt) {                     //dipende da come vogliamo gestire gli errori
        $errorMessage = $conn->error;
        throw new RuntimeException($errorMessage);
    }*/
    if (!$stmt) {throw new RuntimeException("Errore prepare SQL: " . $conn->error);}    //errore inizializzazione query
    if (!$stmt->bind_param('i', $userId)) {throw new RuntimeException("Errore bind_param");}    //errore bind parametri
    if (!$stmt->execute()) {throw new RuntimeException("Errore esecuzione query");}   //errore esecuzione query
    
    $result = $stmt->get_result();
    if (!$result) {throw new RuntimeException("Errore recupero risultati");}
    
    $userInfo = $result->fetch_assoc();
    $stmt->close();
    return $userInfo;
}

?>