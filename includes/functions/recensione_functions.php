<?php
function getRecensioniAdmin($conn, $utenteId = 0) {
    $sql = 'SELECT r.id, r.valutazione, r.testo, r.censura, r.data, u.username, l.titolo AS libro_titolo
            FROM recensione r
            INNER JOIN utente u ON u.id = r.utente_id
            INNER JOIN libro l ON l.id = r.libro_id';

    if ($utenteId > 0) {
        $sql .= ' WHERE r.utente_id = ?';
    }

    $sql .= ' ORDER BY r.data DESC';

    $stmt = $conn->prepare($sql);
    if ($utenteId > 0) {
        $stmt->bind_param('i', $utenteId);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $recensioni = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $recensioni;
}

function getRecensioneById($conn, $recensioneId) {
    $stmt = $conn->prepare(
        'SELECT id, valutazione, testo, censura, data, libro_id, utente_id
         FROM recensione
         WHERE id = ?'
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
