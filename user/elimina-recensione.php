<?php
require_once '../includes/resources.php';
requireRole('user');

$pageTitle = 'Recensione — User BiblioTake';
$currentPage = 'user';
//$message = '';
$errorMessage = '';

function eliminaRecensione($conn, $recensioneId, $utenteId) {
    
    $stmt = $conn->prepare(
        'DELETE FROM recensione WHERE id = ? AND utente_id = ?'
    );

    if (!$stmt) return false;

    $stmt->bind_param('ii', $recensioneId, $utenteId);
    $eseguito = $stmt->execute();
    $righe = $stmt->affected_rows;
    $stmt->close();
    return $eseguito && $righe > 0;
}

?>