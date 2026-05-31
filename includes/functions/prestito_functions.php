<?php
function aggiornaPrestitiScaduti($conn) {
    // Porta in stato 'in_ritardo' tutti i prestiti ancora 'attivo' la cui data_fine è già passata.
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
    $aggiornati_attivo_to_ritardo = $stmt->affected_rows;
    $stmt->close();

    // Riporta in stato 'attivo' i prestiti attualmente 'in_ritardo' la cui data_fine è nel futuro.
    $stmt2 = $conn->prepare(
        "UPDATE prestito
         SET stato = 'attivo'
         WHERE stato = 'in_ritardo'
           AND data_fine > NOW()"
    );

    if (!$stmt2) {
        return $aggiornati_attivo_to_ritardo;
    }

    $stmt2->execute();
    $aggiornati_ritardo_to_attivo = $stmt2->affected_rows;
    $stmt2->close();

    return $aggiornati_attivo_to_ritardo + $aggiornati_ritardo_to_attivo;
}

// Normalizzazione e formattazione date per i prestiti
function normalizeDateTimeForDb($input) {
    $s = str_replace('T', ' ', trim((string) $input));
    if ($s === '') {
        return null;
    }
    // Se manca i secondi, aggiungili
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $s)) {
        $s .= ':00';
    }
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $s);
    if ($dt === false) {
        return null;
    }
    return $dt->format('Y-m-d H:i:s');
}

function formatDateTimeForInput($dbDatetime) {
    if (empty($dbDatetime)) {
        return '';
    }
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $dbDatetime);
    if ($dt === false) {
        $ts = strtotime($dbDatetime);
        if ($ts === false) {
            return '';
        }
        $dt = new DateTime('@' . $ts);
        $dt->setTimezone(new DateTimeZone(date_default_timezone_get()));
    }
    return $dt->format('Y-m-d\\TH:i');
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

function countPrestitiAdminFiltrati($conn, array $filtri) {
    $where = [];
    $params = [];
    $types = '';

    if (!empty($filtri['utente_id'])) {
        $where[] = 'p.utente_id = ?';
        $params[] = (int) $filtri['utente_id'];
        $types .= 'i';
    }

    if (!empty($filtri['cerca'])) {
        $searchValue = trim((string) $filtri['cerca']);
        $where[] = '(p.id = ? OR l.titolo LIKE ? OR l.autore LIKE ? OR u.username LIKE ?)';
        $params[] = (int) $searchValue;
        $like = '%' . $searchValue . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types .= 'isss';
    }

    $sql = 'SELECT COUNT(*) AS totale FROM prestito p INNER JOIN utente u ON u.id = p.utente_id INNER JOIN libro l ON l.id = p.libro_id';
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return (int) ($row['totale'] ?? 0);
}

function getPrestitiAdminFiltrati($conn, array $filtri, $pagina, $limit = 10) {
    $pagina = max(1, (int) $pagina);
    $limit = max(1, (int) $limit);
    $offset = ($pagina - 1) * $limit;

    $where = [];
    $params = [];
    $types = '';

    if (!empty($filtri['utente_id'])) {
        $where[] = 'p.utente_id = ?';
        $params[] = (int) $filtri['utente_id'];
        $types .= 'i';
    }

    if (!empty($filtri['cerca'])) {
        $searchValue = trim((string) $filtri['cerca']);
        $where[] = '(p.id = ? OR l.titolo LIKE ? OR l.autore LIKE ? OR u.username LIKE ?)';
        $params[] = (int) $searchValue;
        $like = '%' . $searchValue . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types .= 'isss';
    }

    $sql = 'SELECT p.id, p.data_inizio, p.data_fine, p.stato, u.username, u.email, l.titolo AS libro_titolo
            FROM prestito p
            INNER JOIN utente u ON u.id = p.utente_id
            INNER JOIN libro l ON l.id = p.libro_id';

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY p.data_inizio DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
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
    // Recupera data_fine e stato
    $stmt = $conn->prepare('SELECT data_fine, stato FROM prestito WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return ['success' => false, 'message' => 'Errore interno.'];
    }
    $stmt->bind_param('i', $prestitoId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return ['success' => false, 'message' => 'Prestito non trovato.'];
    }

    if ($row['stato'] !== 'attivo') {
        return ['success' => false, 'message' => 'Impossibile prorogare: lo stato del prestito non è attivo.'];
    }

    $dataFine = $row['data_fine'];
    $tsFine = strtotime($dataFine);
    if ($tsFine === false) {
        return ['success' => false, 'message' => 'Formato data non valido.'];
    }

    $now = time();
    $delta = $tsFine - $now;
    $weekSeconds = 7 * 24 * 3600;

    if ($delta > $weekSeconds) {
        // Calcola tempo rimanente in giorni/ore
        $days = (int) floor($delta / 86400);
        $hours = (int) floor(($delta % 86400) / 3600);

        $parts = [];
        if ($days > 0) {
            $parts[] = $days . ' ' . ($days === 1 ? 'giorno' : 'giorni');
        }
        if ($hours > 0) {
            $parts[] = $hours . ' ' . ($hours === 1 ? 'ora' : 'ore');
        }

        if ($parts) {
            $remaining = implode(' e ', $parts);
        } else {
            $remaining = 'meno di un ora';
        }

        // Verbo corretto: singolare se manca esattamente 1 unità temporale, plurale altrimenti
        $totalUnits = $days + $hours;
        $verb = ($totalUnits === 1) ? 'manca' : 'mancano';

        return ['success' => false, 'message' => "Non puoi ancora prorogare il tuo prestito, $verb $remaining."];
    }

    // Esegui la proroga di 30 giorni
    $stmt2 = $conn->prepare(
        "UPDATE prestito
         SET data_fine = DATE_ADD(data_fine, INTERVAL 30 DAY)
         WHERE id = ?
           AND stato = 'attivo'"
    );
    if (!$stmt2) {
        return ['success' => false, 'message' => 'Errore interno.'];
    }
    $stmt2->bind_param('i', $prestitoId);
    $ok = $stmt2->execute();
    $affected = $stmt2->affected_rows;
    $stmt2->close();

    if ($ok && $affected > 0) {
        return ['success' => true, 'message' => 'Prestito prorogato di 30 giorni.'];
    }

    return ['success' => false, 'message' => 'Operazione non riuscita.'];
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
