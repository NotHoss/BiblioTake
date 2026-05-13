<?php
require_once '../includes/resources.php';
requireRole('user');

$currentPage = 'user';
//$message = '';
//$errorMessage = '';

//getPrestiti generale
function getPrestiti($conn, $userId, $tipo = 'attivi') {        //la variabile $tipo viene generata dalla pagina che chiama la funzione, quindi è "attivi" o "passati" a seconda di quale pagina HTML sta invocando la funzione  
    
    if(!in_array($tipo, ['attivi', 'passati'], true)){  //controllo che il tipo sia valido, giusto per evitare errori se ci sono valori strani o se qualcuno prova a manipolare la richiesta
        throw new InvalidArgumentException("Tipo di prestito non valido: $tipo");
    }

    if($tipo === 'passati'){
        $pageTitle = 'Prestiti Passati — User BiblioTake';
        $where = "p.stato = 'concluso'";
    } 
    else{
        $pageTitle = 'Prestiti Attivi— BiblioTake';
        $where = "p.stato IN ('attivo', 'in_ritardo')";
    }

    $stmt = $conn->prepare(
        "SELECT p.id, l.titolo, l.autore, p.data_inizio, p.data_fine
         FROM prestito p
         JOIN libro l ON p.libro_id = l.id
         WHERE p.utente_id = ? AND $where"
    );

    if (!$stmt) {throw new RuntimeException("Errore prepare SQL: " . $conn->error);}            //errore inizializzazione query
    if (!$stmt->bind_param('i', $userId)) {throw new RuntimeException("Errore bind_param");}    //errore bind parametri
    if (!$stmt->execute()) {throw new RuntimeException("Errore esecuzione query");}             //errore esecuzione query

    $result = $stmt->get_result();                                                              //recupero i risultati
    if (!$result) {throw new RuntimeException("Errore recupero risultati");}                    //controllo se il recupero dei risultati è andato a buon fine

    $prestiti = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $prestiti;
}
?>