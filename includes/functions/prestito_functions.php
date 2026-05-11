<?php
function aggiornaPrestitiScaduti($conn) {
// Porta in stato 'in_ritardo' tutti i prestiti ancora 'attivo' la cui data_fine è già passata.
// Va richiamata prima di leggere liste/statistiche dei prestiti, oppure dal bootstrap comune.
/*Da capire in quali file che usano i prestiti richiamare la funzione di aggiornamento:

prestiti.php
dashboard.php
richiedi-prestito.php
restituisci.php
index.php
prestiti-utente.php
libri.php
utenti.php
recensioni.php
aggiungi-libro.php
modifica-libro.php
elimina-libro.php

Esempio su come richiamare l'aggiornamento automatico dei prestiti in resource.php subito dopo la connessione:

```php
$conn = getConnection();
aggiornaPrestitiScaduti($conn);
```

In questo modo ogni modello che passa da `resources.php` trova i prestiti già allineati allo stato corretto prima di eseguire le proprie query. */

    $stmt = $conn->prepare(
        "UPDATE prestito
         SET stato = 'in_ritardo'
         WHERE stato = 'attivo'
           AND data_fine < NOW()"
    );

    if (!$stmt) {
        return 0;
    }

    $stmt->execute();
    $aggiornati = $stmt->affected_rows;
    $stmt->close();

    return $aggiornati;
}

function getPrestitiByUtente($conn, $utenteId) {
    $stmt = $conn->prepare(
        'SELECT p.id, p.data_inizio, p.data_fine, p.stato, l.titolo AS libro_titolo, l.autore
         FROM prestito p
         INNER JOIN libro l ON l.id = p.libro_id
         WHERE p.utente_id = ?
         ORDER BY p.data_inizio DESC'
    );
    $stmt->bind_param('i', $utenteId);
    $stmt->execute();
    $result = $stmt->get_result();
    $prestiti = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $prestiti;
}

function getPrestitiAdmin($conn, $utenteId = 0) {
    $sql = 'SELECT p.id, p.data_inizio, p.data_fine, p.stato, u.username, u.email, l.titolo AS libro_titolo
            FROM prestito p
            INNER JOIN utente u ON u.id = p.utente_id
            INNER JOIN libro l ON l.id = p.libro_id';

    if ($utenteId > 0) {
        $sql .= ' WHERE p.utente_id = ?';
    }

    $sql .= ' ORDER BY p.data_inizio DESC';

    $stmt = $conn->prepare($sql);
    if ($utenteId > 0) {
        $stmt->bind_param('i', $utenteId);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $prestiti = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $prestiti;
}

function getPrestitoById($conn, $prestitoId) {
    $stmt = $conn->prepare(
        'SELECT p.id, p.data_inizio, p.data_fine, p.stato, p.libro_id, p.utente_id
         FROM prestito p
         WHERE p.id = ?'
    );
    $stmt->bind_param('i', $prestitoId);
    $stmt->execute();
    $result = $stmt->get_result();
    $prestito = $result->fetch_assoc();
    $stmt->close();

    return $prestito;
}

function aggiornaStatoPrestito($conn, $prestitoId, $stato) {
    $stmt = $conn->prepare('UPDATE prestito SET stato = ? WHERE id = ?');
    $stmt->bind_param('si', $stato, $prestitoId);

    return $stmt->execute();
}

function concludePrestito($conn, $prestitoId) {
    return aggiornaStatoPrestito($conn, $prestitoId, 'concluso');
}

function prorogaPrestito($conn, $prestitoId) {
    $stmt = $conn->prepare(
        "UPDATE prestito
                 SET data_fine = DATE_ADD(data_fine, INTERVAL 30 DAY)
         WHERE id = ?
           AND stato = 'attivo'"
    );
    $stmt->bind_param('i', $prestitoId);

    return $stmt->execute();
}

function updatePrestito($conn, $prestitoId, array $dati) {
    $stmt = $conn->prepare(
        'UPDATE prestito
         SET data_inizio = ?, data_fine = ?, stato = ?, biblioteca_id = ?, libro_id = ?, utente_id = ?
         WHERE id = ?'
    );
    $stmt->bind_param(
        'sssiiii',
        $dati['data_inizio'],
        $dati['data_fine'],
        $dati['stato'],
        $dati['biblioteca_id'],
        $dati['libro_id'],
        $dati['utente_id'],
        $prestitoId
    );

    return $stmt->execute();
}

function deletePrestitoWithReferences($conn, $prestitoId) {
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare('DELETE FROM prestito WHERE id = ?');
        $stmt->bind_param('i', $prestitoId);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            $conn->rollback();
            return false;
        }

        $conn->commit();
        return true;
    } catch (Throwable $e) {
        $conn->rollback();
        return false;
    }
}
