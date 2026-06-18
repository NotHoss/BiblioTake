<?php

function getPrestitiUser($conn, $userId, $tipo = 'attivi') {        //la variabile $tipo viene generata dalla pagina che chiama la funzione, quindi è "attivi" o "passati" a seconda di quale pagina HTML sta invocando la funzione  
    
    if(!in_array($tipo, ['attivi', 'passati'], true)){  //controllo che il tipo sia valido, giusto per evitare errori se ci sono valori strani o se qualcuno prova a manipolare la richiesta
        throw new InvalidArgumentException("Tipo di prestito non valido: $tipo");
    }

    if($tipo === 'passati'){
        $pageTitle = 'Prestiti Passati — User BiblioinTake';
        $where = "p.stato = 'concluso'";
    } 
    else{
        $pageTitle = 'Prestiti Attivi— BiblioTake';
        $where = "p.stato IN ('attivo', 'in_ritardo')";
    }

    $stmt = $conn->prepare(
        "SELECT l.titolo, l.autore, l.anno, l.categoria, p.data_inizio, p.data_fine, p.stato
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

function getUserInfo($conn, $userId) {
    $stmt = $conn->prepare("SELECT id, email, username, foto_profilo, ruolo FROM utente WHERE id = ?");
    
    if (!$stmt) {
        $errorMessage = $conn->error;
        throw new RuntimeException($errorMessage);
    }
    
    if (!$stmt) {throw new RuntimeException("Errore prepare SQL: " . $conn->error);}    //errore inizializzazione query
    if (!$stmt->bind_param('i', $userId)) {throw new RuntimeException("Errore bind_param");}    //errore bind parametri
    if (!$stmt->execute()) {throw new RuntimeException("Errore esecuzione query");}   //errore esecuzione query
    
    $result = $stmt->get_result();
    if (!$result) {throw new RuntimeException("Errore recupero risultati");}
    
    $userInfo = $result->fetch_assoc();
    $stmt->close();
    return $userInfo;
}

function changeUsername($conn, $userId, $newUsername) {

    if(empty($newUsername) || trim($newUsername) === ''){
        return 'Il nome utente non può essere vuoto.';
    }

    $stmt = $conn->prepare("UPDATE utente SET username = ? WHERE id = ?");
    
    if(!$stmt){throw new RuntimeException("Errore prepare SQL: " . $conn->error);}    //errore inizializzazione query
    if(!$stmt->bind_param('si', $newUsername, $userId)){throw new RuntimeException("Errore bind_param");}    //errore bind parametri
    if(!$stmt->execute()){throw new RuntimeException("Errore esecuzione query");}   //errore esecuzione query

    return $stmt->affected_rows > 0 ? true : 'Nessuna modifica effettuata.';
}

function changeEmail($conn, $userId, $newEmail) {

    if(empty($newEmail) || trim($newEmail) === ''){
        return 'L\'email non può essere vuota.';
    }

    $stmt = $conn->prepare("UPDATE utente SET email = ? WHERE id = ?");

    if(!$stmt){throw new RuntimeException("Errore prepare SQL: " . $conn->error);}
    if(!$stmt->bind_param('si', $newEmail, $userId)){throw new RuntimeException("Errore bind_param");}
    if(!$stmt->execute()){throw new RuntimeException("Errore esecuzione query");}

    return $stmt->affected_rows > 0 ? true : 'Nessuna modifica effettuata.';
}

function changePassword($conn, $userId, $newPassword) {

    if(empty($newPassword) || trim($newPassword) === ''){
        return 'La password non può essere vuota.';
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE utente SET password = ? WHERE id = ?");

    if(!$stmt){throw new RuntimeException("Errore prepare SQL: " . $conn->error);}
    if(!$stmt->bind_param('si', $hashedPassword, $userId)){throw new RuntimeException("Errore bind_param");}
    if(!$stmt->execute()){throw new RuntimeException("Errore esecuzione query");}

    return $stmt->affected_rows > 0 ? true : 'Nessuna modifica effettuata.';
}

function changeFotoProfilo($conn, $userId, $fotoPath) {

    if(empty($fotoPath) || trim($fotoPath) === ''){
        return 'Il percorso della foto non può essere vuoto.';
    }

    $stmt = $conn->prepare("UPDATE utente SET foto_profilo = ? WHERE id = ?");

    if(!$stmt){throw new RuntimeException("Errore prepare SQL: " . $conn->error);}
    if(!$stmt->bind_param('si', $fotoPath, $userId)){throw new RuntimeException("Errore bind_param");}
    if(!$stmt->execute()){throw new RuntimeException("Errore esecuzione query");}

    return $stmt->affected_rows > 0 ? true : 'Nessuna modifica effettuata.';
}

function deleteUtente($conn, $userId) {
    $stmt = $conn->prepare('SELECT COUNT(*) FROM prestito WHERE utente_id = ? AND stato IN ("attivo", "in_ritardo")');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return 'Impossibile eliminare l\'account se ci sono prestiti attivi';
    }

    $stmt = $conn->prepare('DELETE FROM utente WHERE id = ?');
    $stmt->bind_param('i', $userId);
    return $stmt->execute();
}
?>
