<?php
// =====================================================================
// FUNZIONI ADMIN - Gestione Dashboard, Statistiche, Utenti
// =====================================================================

function getStatisticheGenerali($conn) {
    $stats = [
        'libri_totali' => 0,
        'libri_prenotati' => 0,
        'libri_non_prenotati' => 0,
        'utenti_totali' => 0,
        'nuovi_iscritti' => null,
        'prestiti_attivi' => 0,
        'prestiti_ritardo' => 0,
        'recensioni_totali' => 0,
    ];

    $stmt = $conn->prepare('SELECT COUNT(*) AS totale FROM libro');
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($row) {
            $stats['libri_totali'] = (int) $row['totale'];
        }
        $stmt->close();
    }

    $stmt = $conn->prepare("SELECT COUNT(DISTINCT libro_id) AS totale FROM prestito WHERE stato IN ('attivo', 'in_ritardo')");
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($row) {
            $stats['libri_prenotati'] = (int) $row['totale'];
        }
        $stmt->close();
    }

    $stats['libri_non_prenotati'] = $stats['libri_totali'] - $stats['libri_prenotati'];

    $stmt = $conn->prepare('SELECT COUNT(*) AS totale FROM utente');
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($row) {
            $stats['utenti_totali'] = (int) $row['totale'];
        }
        $stmt->close();
    }

    $dateColumn = getUtenteDateColumn($conn);
    if ($dateColumn !== '') {
        $sql = 'SELECT COUNT(*) AS totale FROM utente WHERE ' . $dateColumn . ' >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            if ($row) {
                $stats['nuovi_iscritti'] = (int) $row['totale'];
            }
            $stmt->close();
        }
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS totale FROM prestito WHERE stato = 'attivo'");
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($row) {
            $stats['prestiti_attivi'] = (int) $row['totale'];
        }
        $stmt->close();
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS totale FROM prestito WHERE stato = 'in_ritardo'");
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($row) {
            $stats['prestiti_ritardo'] = (int) $row['totale'];
        }
        $stmt->close();
    }

    $stmt = $conn->prepare('SELECT COUNT(*) AS totale FROM recensione');
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        if ($row) {
            $stats['recensioni_totali'] = (int) $row['totale'];
        }
        $stmt->close();
    }

    return $stats;
}

function getBibliotecaInfo($conn) {
    $hasOrari = bibliotecaHasOrariColumns($conn);
    $hasNote = bibliotecaHasNoteColumn($conn);

    if ($hasOrari && $hasNote) {
        $stmt = $conn->prepare(
            'SELECT id, indirizzo, telefono, email, note, orario_lun_ven, orario_sabato, orario_domenica
             FROM biblioteca
             ORDER BY id ASC
             LIMIT 1'
        );
    } elseif ($hasOrari) {
        $stmt = $conn->prepare(
            'SELECT id, indirizzo, telefono, email, orario_lun_ven, orario_sabato, orario_domenica
             FROM biblioteca
             ORDER BY id ASC
             LIMIT 1'
        );
    } else {
        if ($hasNote) {
            $stmt = $conn->prepare(
                'SELECT id, indirizzo, telefono, email, note
                 FROM biblioteca
                 ORDER BY id ASC
                 LIMIT 1'
            );
        } else {
            $stmt = $conn->prepare(
                'SELECT id, indirizzo, telefono, email
                 FROM biblioteca
                 ORDER BY id ASC
                 LIMIT 1'
            );
        }
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $biblioteca = $result->fetch_assoc();
    $stmt->close();

    if ($biblioteca && !$hasOrari) {
        $biblioteca['orario_lun_ven'] = '9:00 - 19:00';
        $biblioteca['orario_sabato'] = '9:00 - 13:00';
        $biblioteca['orario_domenica'] = 'Chiuso';
    }

    if ($biblioteca && !isset($biblioteca['note'])) {
        $biblioteca['note'] = '';
    }

    return $biblioteca ?: null;
}

function updateBibliotecaInfo($conn, $bibliotecaId, array $dati) {
    $hasOrari = bibliotecaHasOrariColumns($conn);
    $hasNote = bibliotecaHasNoteColumn($conn);

    if ($hasOrari && $hasNote) {
        $stmt = $conn->prepare(
            'UPDATE biblioteca
             SET indirizzo = ?, telefono = ?, email = ?, note = ?, orario_lun_ven = ?, orario_sabato = ?, orario_domenica = ?
             WHERE id = ?'
        );
        $stmt->bind_param(
            'sssssssi',
            $dati['indirizzo'],
            $dati['telefono'],
            $dati['email'],
            $dati['note'],
            $dati['orario_lun_ven'],
            $dati['orario_sabato'],
            $dati['orario_domenica'],
            $bibliotecaId
        );
    } elseif ($hasOrari) {
        $stmt = $conn->prepare(
            'UPDATE biblioteca
             SET indirizzo = ?, telefono = ?, email = ?, orario_lun_ven = ?, orario_sabato = ?, orario_domenica = ?
             WHERE id = ?'
        );
        $stmt->bind_param(
            'ssssssi',
            $dati['indirizzo'],
            $dati['telefono'],
            $dati['email'],
            $dati['orario_lun_ven'],
            $dati['orario_sabato'],
            $dati['orario_domenica'],
            $bibliotecaId
        );
    } else {
        if ($hasNote) {
            $stmt = $conn->prepare(
                'UPDATE biblioteca
                 SET indirizzo = ?, telefono = ?, email = ?, note = ?
                 WHERE id = ?'
            );
            $stmt->bind_param(
                'ssssi',
                $dati['indirizzo'],
                $dati['telefono'],
                $dati['email'],
                $dati['note'],
                $bibliotecaId
            );
        } else {
            $stmt = $conn->prepare(
                'UPDATE biblioteca
                 SET indirizzo = ?, telefono = ?, email = ?
                 WHERE id = ?'
            );
            $stmt->bind_param(
                'sssi',
                $dati['indirizzo'],
                $dati['telefono'],
                $dati['email'],
                $bibliotecaId
            );
        }
    }

    return $stmt->execute();
}

function bibliotecaHasOrariColumns($conn) {
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS totale
         FROM information_schema.columns
         WHERE table_schema = DATABASE()
           AND table_name = 'biblioteca'
           AND column_name IN ('orario_lun_ven', 'orario_sabato', 'orario_domenica')"
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return (int) ($row['totale'] ?? 0) === 3;
}

function bibliotecaHasNoteColumn($conn) {
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS totale
         FROM information_schema.columns
         WHERE table_schema = DATABASE()
           AND table_name = 'biblioteca'
           AND column_name = 'note'"
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return (int) ($row['totale'] ?? 0) > 0;
}

function getUtenteDateColumn($conn) {
    $candidateColumns = ['data_registrazione', 'data_iscrizione', 'created_at', 'created_on'];

    foreach ($candidateColumns as $column) {
        $stmt = $conn->prepare(
            'SELECT COUNT(*) AS totale
             FROM information_schema.columns
             WHERE table_schema = ? AND table_name = \'utente\' AND column_name = ?'
        );
        $dbName = DB_NAME;
        $stmt->bind_param('ss', $dbName, $column);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ((int) $row['totale'] > 0) {
            return $column;
        }
    }

    return '';
}

function buildAdminUtentiFilterParts(array $filtri) {
    $where  = ["u.ruolo <> 'admin'"];
    $params = [];
    $types  = '';

    $statoUtenti = isset($filtri['stato_utenti']) ? (string) $filtri['stato_utenti'] : 'tutti';

    if ($statoUtenti === 'attivi') {
        $where[] = 'u.attivo = 1';
    } elseif ($statoUtenti === 'non_attivi') {
        $where[] = 'u.attivo = 0';
    }

    if (!empty($filtri['cerca'])) {
        $searchValue = trim((string) $filtri['cerca']);
        $where[] = '(u.id = ? OR u.username LIKE ? OR u.email LIKE ?)';
        $params[] = (int) $searchValue;
        $like = '%' . $searchValue . '%';
        $params[] = $like;
        $params[] = $like;
        $types   .= 'iss';
    }

    return [
        'where' => $where,
        'params' => $params,
        'types' => $types,
    ];
}

function validateBibliotecaAdminData(array $input) {
    $data = [
        'indirizzo' => '',
        'telefono' => '',
        'email' => '',
        'note' => '',
        'orario_lun_ven' => '',
        'orario_sabato' => '',
        'orario_domenica' => '',
    ];
    $errors = [];

    $capitalizeFirst = static function ($value) {
        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));
        if ($value === '') {
            return '';
        }

        $firstChar = mb_substr($value, 0, 1, 'UTF-8');
        $rest = mb_substr($value, 1, null, 'UTF-8');

        return mb_strtoupper($firstChar, 'UTF-8') . $rest;
    };

    $indirizzo = $capitalizeFirst($input['indirizzo'] ?? '');
    if ($indirizzo === '') {
        $errors[] = 'Indirizzo obbligatorio.';
    } elseif (mb_strlen($indirizzo, 'UTF-8') > 100) {
        $errors[] = 'Indirizzo troppo lungo (max 100 caratteri).';
    } else {
        $data['indirizzo'] = $indirizzo;
    }

    $telefono = trim((string) ($input['telefono'] ?? ''));
    if ($telefono === '') {
        $errors[] = 'Telefono obbligatorio.';
    } elseif (mb_strlen($telefono, 'UTF-8') > 20 || !preg_match('/^[0-9 +()\-\/]{6,20}$/', $telefono)) {
        $errors[] = 'Telefono non valido.';
    } else {
        $data['telefono'] = $telefono;
    }

    $email = strtolower(trim((string) ($input['email'] ?? '')));
    if ($email === '') {
        $errors[] = 'Email obbligatoria.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email, 'UTF-8') > 255) {
        $errors[] = 'Email non valida.';
    } else {
        $data['email'] = $email;
    }

    $note = trim((string) ($input['note'] ?? ''));
    if ($note !== '') {
        if (mb_strlen($note, 'UTF-8') > 500) {
            $errors[] = 'Note troppo lunghe (max 500 caratteri).';
        } else {
            $data['note'] = $capitalizeFirst($note);
        }
    }

    foreach (['orario_lun_ven', 'orario_sabato', 'orario_domenica'] as $campoOrario) {
        $valore = trim((string) ($input[$campoOrario] ?? ''));
        if ($valore === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $campoOrario)) . ' obbligatorio.';
        } elseif (mb_strlen($valore, 'UTF-8') > 100) {
            $errors[] = ucfirst(str_replace('_', ' ', $campoOrario)) . ' troppo lungo (max 100 caratteri).';
        } else {
            $data[$campoOrario] = $valore;
        }
    }

    return [
        'ok' => empty($errors),
        'errorMessage' => implode(' ', $errors),
        'dati' => $data,
    ];
}

function validatePrestitoAdminData($conn, array $input) {
    $dataInizioNorm = normalizeDateTimeForDb(trim((string) ($input['data_inizio'] ?? '')));
    $dataFineNorm = normalizeDateTimeForDb(trim((string) ($input['data_fine'] ?? '')));
    $stato = trim((string) ($input['stato'] ?? ''));
    $libroId = (int) ($input['libro_id'] ?? 0);
    $utenteId = (int) ($input['utente_id'] ?? ($input['nuovo_utente_id'] ?? 0));
    $bibliotecaId = (int) ($input['biblioteca_id'] ?? 1);
    $errors = [];

    if ($dataInizioNorm === null || $dataInizioNorm === '') {
        $errors[] = 'Data inizio non valida.';
    }
    if ($dataFineNorm === null || $dataFineNorm === '') {
        $errors[] = 'Data fine non valida.';
    }
    if ($dataInizioNorm !== null && $dataFineNorm !== null && strtotime($dataFineNorm) < strtotime($dataInizioNorm)) {
        $errors[] = 'La data fine deve essere uguale o successiva alla data inizio.';
    }

    $statiValidi = ['attivo', 'concluso', 'in_ritardo'];
    if (!in_array($stato, $statiValidi, true)) {
        $errors[] = 'Stato prestito non valido.';
    }

    if ($bibliotecaId <= 0) {
        $errors[] = 'Biblioteca non valida.';
    }
    if ($libroId <= 0 || !isLibroExists($conn, $libroId)) {
        $errors[] = 'Libro non valido.';
    }
    if ($utenteId <= 0 || !isUtenteExists($conn, $utenteId)) {
        $errors[] = 'Utente non valido.';
    }

    return [
        'ok' => empty($errors),
        'errorMessage' => implode(' ', $errors),
        'dati' => [
            'data_inizio' => $dataInizioNorm ?? '',
            'data_fine' => $dataFineNorm ?? '',
            'stato' => $stato,
            'biblioteca_id' => $bibliotecaId,
            'libro_id' => $libroId,
            'utente_id' => $utenteId,
        ],
    ];
}

function countUtentiConPrestitiFiltrati($conn, array $filtri) {
    $parts = buildAdminUtentiFilterParts($filtri);
    $where  = $parts['where'];
    $params = $parts['params'];
    $types  = $parts['types'];

    $sql = 'SELECT COUNT(*) AS totale FROM utente u';

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

function getUtentiConPrestitiFiltrati($conn, array $filtri, $pagina, $limit = 10) {
    $offset = ($pagina - 1) * $limit;
    $parts = buildAdminUtentiFilterParts($filtri);
    $where  = $parts['where'];
    $params = $parts['params'];
    $types  = $parts['types'];

    $sql = "SELECT u.id, u.username, u.email, u.attivo, u.foto_profilo,
        (SELECT COUNT(*) FROM prestito p WHERE p.utente_id = u.id) AS prestiti_totali,
        (SELECT COUNT(*) FROM prestito p WHERE p.utente_id = u.id AND p.stato = 'attivo') AS prestiti_attivi,
        (SELECT COUNT(*) FROM recensione r WHERE r.utente_id = u.id) AS recensioni_totali
     FROM utente u";

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY u.id DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
    $types   .= 'ii';

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $rows;
}

function getUtenteById($conn, $utenteId) {
    $stmt = $conn->prepare('SELECT id, username, email, ruolo, attivo, foto_profilo FROM utente WHERE id = ?');
    $stmt->bind_param('i', $utenteId);
    $stmt->execute();
    $result = $stmt->get_result();
    $utente = $result->fetch_assoc();
    $stmt->close();

    return $utente;
}

function buildAdminLibroFilterParts(array $filtri) {
    $where  = [];
    $params = [];
    $types  = '';

    $statoLibri = isset($filtri['stato_libri']) ? (string) $filtri['stato_libri'] : 'tutti';

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

    if ($statoLibri === 'disponibili') {
        $where[] = 'l.id NOT IN (
            SELECT libro_id FROM prestito WHERE stato IN (\'attivo\', \'in_ritardo\')
        )';
    } elseif ($statoLibri === 'prestati') {
        $where[] = 'l.id IN (
            SELECT libro_id FROM prestito WHERE stato IN (\'attivo\', \'in_ritardo\')
        )';
    }

    if (!empty($filtri['cerca'])) {
        $searchValue = trim((string) $filtri['cerca']);
        $where[] = '(l.id = ? OR l.codice_isbn LIKE ? OR l.titolo LIKE ? OR l.autore LIKE ?)';
        $params[] = (int) $searchValue;
        $like = '%' . $searchValue . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types   .= 'isss';
    }

    if (!empty($filtri['tag'])) {
        $where[] = 'EXISTS (
            SELECT 1
            FROM libro_tag lt
            INNER JOIN tag t ON t.id = lt.tag_id
            WHERE lt.libro_id = l.id AND t.nome = ?
        )';
        $params[] = $filtri['tag'];
        $types   .= 's';
    }

    return [
        'where' => $where,
        'params' => $params,
        'types' => $types,
    ];
}

function getLibriAdminFiltrati($conn, array $filtri, $pagina, $limit = 20) {
    $offset = ($pagina - 1) * $limit;
    $parts = buildAdminLibroFilterParts($filtri);
    $where  = $parts['where'];
    $params = $parts['params'];
    $types  = $parts['types'];

    $sql = 'SELECT l.id, l.codice_isbn, l.titolo, l.autore, l.casa_editrice, l.edizione, l.anno, l.lingua, l.descrizione, l.pagine, l.copertina, l.categoria,
            (SELECT COUNT(*)
             FROM prestito p
             WHERE p.libro_id = l.id AND p.stato IN (\'attivo\', \'in_ritardo\')) AS prestiti_attivi,
            (SELECT p.utente_id
             FROM prestito p
             WHERE p.libro_id = l.id AND p.stato IN (\'attivo\', \'in_ritardo\')
             ORDER BY p.data_inizio DESC
             LIMIT 1) AS prestito_utente_id,
            (SELECT p.id
             FROM prestito p
             WHERE p.libro_id = l.id AND p.stato IN (\'attivo\', \'in_ritardo\')
             ORDER BY p.data_inizio DESC
             LIMIT 1) AS prestito_id
         FROM libro l';

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY l.id DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
    $types   .= 'ii';

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $libri = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $libri;
}

function countLibriAdminFiltrati($conn, array $filtri) {
    $parts = buildAdminLibroFilterParts($filtri);
    $where  = $parts['where'];
    $params = $parts['params'];
    $types  = $parts['types'];

    $sql = 'SELECT COUNT(*) AS totale FROM libro l';

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

// =====================================================================
// FUNZIONI ADMIN - Gestione Libri (CRUD)
// =====================================================================

function getCategorieLibri($conn) {
    $stmt = $conn->prepare('SELECT DISTINCT categoria FROM libro ORDER BY categoria ASC');
    $stmt->execute();
    $result = $stmt->get_result();
    $categorie = [];
    while ($row = $result->fetch_assoc()) {
        $categorie[] = $row['categoria'];
    }
    $stmt->close();
    return $categorie;
}

function validateAndProcessCopertina($file) {
    // Verifica che il file sia stato caricato
    if (!is_uploaded_file($file['tmp_name'])) {
        return null;
    }
    
    // Verifica la dimensione massima (5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    // Verifica l'estensione
    $allowedExtensions = ['jpg', 'jpeg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions, true)) {
        return null;
    }
    
    // Verifica che sia effettivamente un'immagine JPG usando getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false || !in_array($imageInfo[2], [IMAGETYPE_JPEG], true)) {
        return null;
    }
    
    return $file;
}

// =====================================================================
// FUNZIONI ADMIN - Validazione Dati
// =====================================================================

function isLibroExists($conn, $libroId) {
    if ((int) $libroId <= 0) {
        return false;
    }
    $stmt = $conn->prepare('SELECT id FROM libro WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $libroId);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->fetch_assoc() !== null;
    $stmt->close();
    return $exists;
}

function isUtenteExists($conn, $utenteId) {
    if ((int) $utenteId <= 0) {
        return false;
    }
    $stmt = $conn->prepare('SELECT id FROM utente WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $utenteId);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->fetch_assoc() !== null;
    $stmt->close();
    return $exists;
}

?>
