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

function deleteRecensione($conn, $recensioneId) {
    $stmt = $conn->prepare('DELETE FROM recensione WHERE id = ?');
    $stmt->bind_param('i', $recensioneId);

    return $stmt->execute();
}

function censuraRecensione($conn, $recensioneId) {
    // Toggle: se censurata, decensura; se non censurata, censura
    $stmt = $conn->prepare('UPDATE recensione SET censura = NOT censura WHERE id = ?');
    $stmt->bind_param('i', $recensioneId);

    return $stmt->execute();
}
