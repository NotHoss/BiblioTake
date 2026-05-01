<?php

function getLibri($conn, $filtri, $pagina) {
    $limit  = MAX_PER_PAGINA;
    $offset = ($pagina - 1) * $limit;

    $where  = [];
    $params = [];
    $types  = '';

    if (!empty($filtri['categoria'])) {
        $where[]  = 'l.categoria = ?';
        $params[] = $filtri['categoria'];
        $types   .= 's';
    }

    if (!empty($filtri['autore'])) {
        $where[]  = 'l.autore LIKE ?';
        $params[] = '%' . $filtri['autore'] . '%';
        $types   .= 's';
    }

    if (!empty($filtri['anno'])) {
        $where[]  = 'l.anno = ?';
        $params[] = (int) $filtri['anno'];
        $types   .= 'i';
    }

    if (isset($filtri['disponibile']) && $filtri['disponibile'] === '1') {
        $where[] = 'l.id NOT IN (
            SELECT libro_id FROM prestito WHERE stato IN (\'attivo\', \'in_ritardo\')
        )';
    }

    if (!empty($filtri['cerca'])) {
        $where[]  = '(l.titolo LIKE ? OR l.autore LIKE ?)';
        $cerca    = '%' . $filtri['cerca'] . '%';
        $params[] = $cerca;
        $params[] = $cerca;
        $types   .= 'ss';
    }

    $sql = 'SELECT l.*, (
                SELECT ROUND(AVG(r.valutazione), 1)
                FROM recensione r
                WHERE r.libro_id = l.id
            ) AS media_voti
            FROM libro l';

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    if (!empty($filtri['ordine']) && $filtri['ordine'] === 'valutazione') {
        $sql .= ' ORDER BY media_voti DESC';
    } else {
        $sql .= ' ORDER BY l.id DESC';
    }

    $sql .= ' LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
    $types   .= 'ii';

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $libri  = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $libri;
}

function countLibri($conn, $filtri) {
    $where  = [];
    $params = [];
    $types  = '';

    if (!empty($filtri['categoria'])) {
        $where[]  = 'l.categoria = ?';
        $params[] = $filtri['categoria'];
        $types   .= 's';
    }

    if (!empty($filtri['autore'])) {
        $where[]  = 'l.autore LIKE ?';
        $params[] = '%' . $filtri['autore'] . '%';
        $types   .= 's';
    }

    if (!empty($filtri['anno'])) {
        $where[]  = 'l.anno = ?';
        $params[] = (int) $filtri['anno'];
        $types   .= 'i';
    }

    if (isset($filtri['disponibile']) && $filtri['disponibile'] === '1') {
        $where[] = 'l.id NOT IN (
            SELECT libro_id FROM prestito WHERE stato IN (\'attivo\', \'in_ritardo\')
        )';
    }

    if (!empty($filtri['cerca'])) {
        $where[]  = '(l.titolo LIKE ? OR l.autore LIKE ?)';
        $cerca    = '%' . $filtri['cerca'] . '%';
        $params[] = $cerca;
        $params[] = $cerca;
        $types   .= 'ss';
    }

    $sql = 'SELECT COUNT(*) AS totale FROM libro l';

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result  = $stmt->get_result();
    $row     = $result->fetch_assoc();
    $stmt->close();

    return (int) $row['totale'];
}

function getLibroById($conn, $id) {
    $stmt = $conn->prepare(
        'SELECT l.*, (
            SELECT ROUND(AVG(r.valutazione), 1)
            FROM recensione r
            WHERE r.libro_id = l.id
        ) AS media_voti
        FROM libro l
        WHERE l.id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $libro  = $result->fetch_assoc();
    $stmt->close();

    return $libro;
}

function getTagsByLibroId($conn, $libroId) {
    $stmt = $conn->prepare(
        'SELECT t.nome
         FROM tag t
         INNER JOIN libro_tag lt ON lt.tag_id = t.id
         WHERE lt.libro_id = ?
         ORDER BY t.nome ASC'
    );
    $stmt->bind_param('i', $libroId);
    $stmt->execute();
    $result = $stmt->get_result();
    $tags   = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $tags;
}

function getLibriRecenti($conn, $limit) {
    $stmt = $conn->prepare(
        'SELECT * FROM libro ORDER BY id DESC LIMIT ?'
    );
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $libri  = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $libri;
}

function getCategorie($conn) {
    $stmt = $conn->prepare(
        'SELECT DISTINCT categoria FROM libro ORDER BY categoria ASC'
    );
    $stmt->execute();
    $result    = $stmt->get_result();
    $categorie = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $categorie;
}

function isLibroDisponibile($conn, $libroId) {
    $stmt = $conn->prepare(
        'SELECT COUNT(*) AS attivi
         FROM prestito
         WHERE libro_id = ? AND stato IN (\'attivo\', \'in_ritardo\')'
    );
    $stmt->bind_param('i', $libroId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();
    $stmt->close();

    return (int) $row['attivi'] === 0;
}

function getRecensioniByLibroId($conn, $libroId) {
    $stmt = $conn->prepare(
        'SELECT r.valutazione, r.testo, r.data, u.username
         FROM recensione r
         INNER JOIN utente u ON u.id = r.utente_id
         WHERE r.libro_id = ?
         ORDER BY r.data DESC'
    );
    $stmt->bind_param('i', $libroId);
    $stmt->execute();
    $result     = $stmt->get_result();
    $recensioni = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $recensioni;
}
