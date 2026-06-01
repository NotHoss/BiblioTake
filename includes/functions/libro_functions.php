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
        $where[]  = 'l.titolo LIKE ?';
        $params[] = '%' . $filtri['cerca'] . '%';
        $types   .= 's';
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
        $where[]  = 'l.titolo LIKE ?';
        $params[] = '%' . $filtri['cerca'] . '%';
        $types   .= 's';
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

function createLibro($conn, array $dati) {
    $stmt = $conn->prepare(
        'INSERT INTO libro (codice_isbn, titolo, autore, casa_editrice, edizione, anno, lingua, descrizione, pagine, copertina, categoria)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    
    $copertina = $dati['copertina'] !== '' ? $dati['copertina'] : DEFAULT_COVER;
    $edizione = (int) ($dati['edizione'] ?? 1);
    $anno = (int) ($dati['anno'] ?? date('Y'));
    $pagine = (int) ($dati['pagine'] ?? 0);
    
    $stmt->bind_param(
        'ssssiississ',
        $dati['codice_isbn'],
        $dati['titolo'],
        $dati['autore'],
        $dati['casa_editrice'],
        $edizione,
        $anno,
        $dati['lingua'],
        $dati['descrizione'],
        $pagine,
        $copertina,
        $dati['categoria']
    );
    $stmt->execute();
    $nuovoId = $stmt->insert_id;
    $stmt->close();

    // Gestione tag associati (se forniti come stringa separata da virgole)
    if (!empty($dati['tags'])) {
        $tags = array_filter(array_map('trim', explode(',', (string) $dati['tags'])));
        if (!empty($tags)) {
            syncTagsForLibro($conn, $nuovoId, $tags);
        }
    }

    return $nuovoId;
}

function updateLibro($conn, $id, array $dati) {
    $stmt = $conn->prepare(
        'UPDATE libro
         SET codice_isbn = ?, titolo = ?, autore = ?, casa_editrice = ?, edizione = ?, anno = ?, lingua = ?, descrizione = ?, pagine = ?, copertina = ?, categoria = ?
         WHERE id = ?'
    );
    
    $copertina = $dati['copertina'] !== '' ? $dati['copertina'] : DEFAULT_COVER;
    $edizione = (int) ($dati['edizione'] ?? 1);
    $anno = (int) ($dati['anno'] ?? date('Y'));
    $pagine = (int) ($dati['pagine'] ?? 0);
    
    $stmt->bind_param(
        'ssssiississi',
        $dati['codice_isbn'],
        $dati['titolo'],
        $dati['autore'],
        $dati['casa_editrice'],
        $edizione,
        $anno,
        $dati['lingua'],
        $dati['descrizione'],
        $pagine,
        $copertina,
        $dati['categoria'],
        $id
    );

    $res = $stmt->execute();

    if ($res) {
        // Aggiorna i tag associati (se forniti come stringa separata da virgole)
        if (isset($dati['tags'])) {
            $tags = array_filter(array_map('trim', explode(',', (string) $dati['tags'])));
            syncTagsForLibro($conn, $id, $tags);
        }
    }

    return $res;
}

/**
 * Sincronizza i tag di un libro: rimuove le associazioni esistenti e ne inserisce di nuove.
 * Accetta un array di nomi di tag.
 */
function syncTagsForLibro($conn, $libroId, array $tags) {
    // Rimuovi associazioni esistenti
    $stmt = $conn->prepare('DELETE FROM libro_tag WHERE libro_id = ?');
    if ($stmt) {
        $stmt->bind_param('i', $libroId);
        $stmt->execute();
        $stmt->close();
    }

    if (empty($tags)) return;

    // Per ogni tag: assicurati che esista nella tabella tag, poi inserisci in libro_tag
    $stmtSelect = $conn->prepare('SELECT id FROM tag WHERE nome = ? LIMIT 1');
    $stmtInsertTag = $conn->prepare('INSERT INTO tag (nome) VALUES (?)');
    $stmtInsertLink = $conn->prepare('INSERT INTO libro_tag (libro_id, tag_id) VALUES (?, ?)');

    foreach ($tags as $tagName) {
        if ($tagName === '') continue;
        $nome = trim((string) $tagName);
        $tagId = null;

        if ($stmtSelect) {
            $stmtSelect->bind_param('s', $nome);
            $stmtSelect->execute();
            $res = $stmtSelect->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            if ($row) {
                $tagId = (int) $row['id'];
            }
            $stmtSelect->free_result();
        }

        if ($tagId === null && $stmtInsertTag) {
            $stmtInsertTag->bind_param('s', $nome);
            $stmtInsertTag->execute();
            $tagId = $stmtInsertTag->insert_id;
        }

        if ($tagId !== null && $stmtInsertLink) {
            $stmtInsertLink->bind_param('ii', $libroId, $tagId);
            $stmtInsertLink->execute();
        }
    }

    if ($stmtSelect) $stmtSelect->close();
    if ($stmtInsertTag) $stmtInsertTag->close();
    if ($stmtInsertLink) $stmtInsertLink->close();
}

function getAllTags($conn) {
    $stmt = $conn->prepare('SELECT id, nome FROM tag ORDER BY nome ASC');
    $tags = [];
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $tags[] = $row;
        }
        $stmt->close();
    }
    return $tags;
}

function deleteLibroWithCascade($conn, $id) {
    // Prima recupera il percorso della copertina per cancellarla dopo il commit
    $copertinaPath = '';
    $stmtSel = $conn->prepare('SELECT copertina FROM libro WHERE id = ?');
    if ($stmtSel) {
        $stmtSel->bind_param('i', $id);
        $stmtSel->execute();
        $res = $stmtSel->get_result();
        $row = $res->fetch_assoc();
        $stmtSel->close();
        $copertinaPath = isset($row['copertina']) ? $row['copertina'] : '';
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare('DELETE FROM prestito WHERE libro_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare('DELETE FROM recensione WHERE libro_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare('DELETE FROM libro WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $conn->commit();
            $stmt->close();

            // Dopo il commit, rimuovi il file della copertina solo se non è la copertina di default
            if (!empty($copertinaPath)) {
                $basename = basename($copertinaPath);
                if ($basename !== basename(DEFAULT_COVER)) {
                    $fileToDelete = __DIR__ . '/../' . $copertinaPath;
                    if (is_file($fileToDelete)) {
                        @unlink($fileToDelete);
                    }
                }
            }

            return true;
        }
        $conn->rollback();
        $stmt->close();
        return false;
    } catch (Throwable $e) {
        $conn->rollback();
        return false;
    }
}

?>