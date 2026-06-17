<?php
function countRecensioniAdminFiltrate($conn, array $filtri) {
    $where = [];
    $params = [];
    $types = '';

    if (!empty($filtri['utente_id'])) {
        $where[] = 'r.utente_id = ?';
        $params[] = (int) $filtri['utente_id'];
        $types .= 'i';
    }

    if (!empty($filtri['voto']) && (int) $filtri['voto'] >= 1 && (int) $filtri['voto'] <= 5) {
        $where[] = 'r.valutazione = ?';
        $params[] = (int) $filtri['voto'];
        $types .= 'i';
    }

    if (!empty($filtri['stato_recensione']) && $filtri['stato_recensione'] !== 'tutte') {
        if ($filtri['stato_recensione'] === 'visibile') {
            $where[] = 'r.censura = 0';
        } elseif ($filtri['stato_recensione'] === 'censurata') {
            $where[] = 'r.censura = 1';
        }
    }

    if (!empty($filtri['cerca'])) {
        $searchValue = trim((string) $filtri['cerca']);
        $where[] = '(r.id = ? OR u.username LIKE ? OR u.email LIKE ? OR l.titolo LIKE ? OR r.testo LIKE ?)';
        $params[] = (int) $searchValue;
        $like = '%' . $searchValue . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types .= 'issss';
    }

    $sql = 'SELECT COUNT(*) AS totale FROM recensione r INNER JOIN utente u ON u.id = r.utente_id INNER JOIN libro l ON l.id = r.libro_id';
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

function getRecensioniAdminFiltrate($conn, array $filtri, $pagina, $limit = 10) {
    $pagina = max(1, (int) $pagina);
    $limit = max(1, (int) $limit);
    $offset = ($pagina - 1) * $limit;

    $where = [];
    $params = [];
    $types = '';

    if (!empty($filtri['utente_id'])) {
        $where[] = 'r.utente_id = ?';
        $params[] = (int) $filtri['utente_id'];
        $types .= 'i';
    }

    if (!empty($filtri['voto']) && (int) $filtri['voto'] >= 1 && (int) $filtri['voto'] <= 5) {
        $where[] = 'r.valutazione = ?';
        $params[] = (int) $filtri['voto'];
        $types .= 'i';
    }

    if (!empty($filtri['stato_recensione']) && $filtri['stato_recensione'] !== 'tutte') {
        if ($filtri['stato_recensione'] === 'visibile') {
            $where[] = 'r.censura = 0';
        } elseif ($filtri['stato_recensione'] === 'censurata') {
            $where[] = 'r.censura = 1';
        }
    }

    if (!empty($filtri['cerca'])) {
        $searchValue = trim((string) $filtri['cerca']);
        $where[] = '(r.id = ? OR u.username LIKE ? OR u.email LIKE ? OR l.titolo LIKE ? OR r.testo LIKE ?)';
        $params[] = (int) $searchValue;
        $like = '%' . $searchValue . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types .= 'issss';
    }

    $sql = 'SELECT r.id, r.valutazione, r.testo, r.censura, r.data, r.utente_id, r.libro_id, u.username, l.titolo AS libro_titolo
            FROM recensione r
            INNER JOIN utente u ON u.id = r.utente_id
            INNER JOIN libro l ON l.id = r.libro_id';

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY r.data DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $recensioni = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $recensioni;
}

function getRecensioneById($conn, $recensioneId) {
    $stmt = $conn->prepare(
        'SELECT r.id, r.valutazione, r.testo, r.censura, r.data, r.libro_id, r.utente_id, u.username, l.titolo AS libro_titolo
         FROM recensione r
         INNER JOIN utente u ON u.id = r.utente_id
         INNER JOIN libro l ON l.id = r.libro_id
         WHERE r.id = ?'
    );
    $stmt->bind_param('i', $recensioneId);
    $stmt->execute();
    $result = $stmt->get_result();
    $recensione = $result->fetch_assoc();
    $stmt->close();

    return $recensione;
}

//cancellazione fisica della recensione (uso admin, nessun check ownership). per la versione utente con check ownership vedi deleteRecensioneUtente.
function deleteRecensione($conn, $recensioneId) {
    $stmt = $conn->prepare('DELETE FROM recensione WHERE id = ?');
    $stmt->bind_param('i', $recensioneId);

    return $stmt->execute();
}

//cancellazione fisica della recensione da parte del suo autore, con check ownership. l'utente elimina davvero la propria recensione; la censura (nascondere) resta un'azione riservata all'admin.
function deleteRecensioneUtente($conn, $recensioneId, $userId) {
    $stmt = $conn->prepare('DELETE FROM recensione WHERE id = ? AND utente_id = ?');
    //$stmt = $conn->prepare('UPDATE recensione SET censura = 1 WHERE id = ? AND utente_id = ?');
    $stmt->bind_param('ii', $recensioneId, $userId);

    return $stmt->execute();
}

function censuraRecensione($conn, $recensioneId) {
    // Toggle: se censurata, decensura; se non censurata, censura
    $stmt = $conn->prepare('UPDATE recensione SET censura = NOT censura WHERE id = ?');
    $stmt->bind_param('i', $recensioneId);

    return $stmt->execute();
}

function getRecensioniUser($conn, $userId) {
    $stmt = $conn->prepare(
        "SELECT r.id AS recensione_id, l.titolo, l.autore, l.id AS libro_id, r.valutazione, r.testo, r.data
         FROM recensione r
         JOIN libro l ON r.libro_id = l.id
         WHERE r.utente_id = ? AND censura = 0"
    );
    
    if (!$stmt) {throw new RuntimeException("Errore prepare SQL: " . $conn->error);}
    if (!$stmt->bind_param('i', $userId)) {throw new RuntimeException("Errore bind_param");;}
    if (!$stmt->execute()) {throw new RuntimeException("Errore esecuzione query");}

    $result = $stmt->get_result();
    if (!$result) {throw new RuntimeException("Errore recupero risultati");}

    $recensioni = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $recensioni;
}

function getRecensioneSingolaUser($conn, $userId, $recensioneId){
    $stmt = $conn->prepare(
        "SELECT r.id, r.utente_id, l.titolo, l.autore, l.id AS libro_id, r.valutazione, r.testo, r.data
         FROM recensione r
         JOIN libro l ON r.libro_id = l.id
         WHERE r.utente_id = ? AND r.id = ? AND censura = 0"
    );
    
    if (!$stmt) {throw new RuntimeException("Errore prepare SQL: " . $conn->error);}
    if (!$stmt->bind_param('ii', $userId, $recensioneId)) {throw new RuntimeException("Errore bind_param");;}
    if (!$stmt->execute()) {throw new RuntimeException("Errore esecuzione query");}

    $result = $stmt->get_result();
    if (!$result) {throw new RuntimeException("Errore recupero risultati");}

    $recensione = $result->fetch_assoc();
    $stmt->close();
    return $recensione;
}

function aggiungiRecensione($conn, $userId, $libroId, $testo, $valutazione) {
    $stmt = $conn->prepare(
        'INSERT INTO recensione (utente_id, libro_id, valutazione, testo) VALUES (?, ?, ?, ?)'
    );
    if (!$stmt) { return 'Errore prepare SQL: ' . $conn->error; }
    if (!$stmt->bind_param('iiis', $userId, $libroId, $valutazione, $testo)) {
        return 'Errore bind_param';
    }
    if (!$stmt->execute()) {
        return 'Errore esecuzione query: ' . $stmt->error;
    }
    $stmt->close();
    return true;
}

//modifica testo e valutazione di una recensione dell'utente con check ownership. il vincolo censura=0 impedisce di modificare recensioni nascoste. ritorna true o stringa errore, come aggiungiRecensione.
function updateRecensioneUtente($conn, $recensioneId, $userId, $testo, $valutazione) {
    $stmt = $conn->prepare(
        'UPDATE recensione SET testo = ?, valutazione = ? WHERE id = ? AND utente_id = ? AND censura = 0'
    );
    if (!$stmt) { return 'Errore prepare SQL: ' . $conn->error; }
    if (!$stmt->bind_param('siii', $testo, $valutazione, $recensioneId, $userId)) {
        return 'Errore bind_param';
    }
    if (!$stmt->execute()) {
        return 'Errore esecuzione query: ' . $stmt->error;
    }
    $stmt->close();
    return true;
}