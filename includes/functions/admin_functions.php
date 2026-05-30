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

/**
 * Gestisce la richiesta di aggiornamento delle generalità della biblioteca.
 * Restituisce un array con chiavi: successMessage, errorMessage, biblioteca, isEditGeneralita
 */
function handleAggiornaGeneralita($conn, array $post) {
    $result = [
        'successMessage' => '',
        'errorMessage' => '',
        'biblioteca' => null,
        'isEditGeneralita' => false,
    ];

    if (!isset($post['azione']) || $post['azione'] !== 'aggiorna_generalita' || !isset($post['biblioteca_id'])) {
        return $result;
    }

    $bibliotecaId = (int) $post['biblioteca_id'];
    $dati = [
        'indirizzo' => trim((string) ($post['indirizzo'] ?? '')),
        'telefono' => trim((string) ($post['telefono'] ?? '')),
        'email' => trim((string) ($post['email'] ?? '')),
        'note' => trim((string) ($post['note'] ?? '')),
        'orario_lun_ven' => trim((string) ($post['orario_lun_ven'] ?? '')),
        'orario_sabato' => trim((string) ($post['orario_sabato'] ?? '')),
        'orario_domenica' => trim((string) ($post['orario_domenica'] ?? '')),
    ];

    if (
        $bibliotecaId > 0
        && $dati['indirizzo'] !== ''
        && $dati['telefono'] !== ''
        && $dati['email'] !== ''
        && $dati['orario_lun_ven'] !== ''
        && $dati['orario_sabato'] !== ''
        && $dati['orario_domenica'] !== ''
    ) {
        if (updateBibliotecaInfo($conn, $bibliotecaId, $dati)) {
            $result['successMessage'] = 'Generalità della biblioteca aggiornate con successo.';
            $result['biblioteca'] = getBibliotecaInfo($conn);
            $result['isEditGeneralita'] = false;
        } else {
            $result['errorMessage'] = 'Aggiornamento delle generalità non riuscito.';
            $result['isEditGeneralita'] = true;
        }
    } else {
        $result['errorMessage'] = 'Compila tutti i campi obbligatori delle generalità e degli orari.';
        $result['isEditGeneralita'] = true;
    }

    return $result;
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

function getAllUtenti($conn, $limit = 200) {
    $stmt = $conn->prepare("SELECT id, username, email, ruolo, attivo, foto_profilo FROM utente WHERE ruolo <> 'admin' ORDER BY id DESC LIMIT ?");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $rows;
}

function getUtentiConPrestiti($conn, $limit = 200) {
    $stmt = $conn->prepare(
    "SELECT u.id, u.username, u.email, u.ruolo, u.attivo, u.foto_profilo,
        (SELECT COUNT(*) FROM prestito p WHERE p.utente_id = u.id) AS prestiti_totali,
        (SELECT COUNT(*) FROM prestito p WHERE p.utente_id = u.id AND p.stato = 'attivo') AS prestiti_attivi
     FROM utente u
     WHERE u.ruolo <> 'admin'
         ORDER BY u.id DESC
         LIMIT ?"
    );
    $stmt->bind_param('i', $limit);
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
             LIMIT 1) AS prestito_utente_id
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

/* ----------------------------- Helpers for admin POSTs ----------------------------- */

function handleAggiungiLibro($conn, array $post, array $files) {
    $result = ['successMessage' => '', 'errorMessage' => '', 'dati' => []];

    $dati = [
        'codice_isbn' => '',
        'titolo' => '',
        'autore' => '',
        'casa_editrice' => '',
        'edizione' => '',
        'anno' => '',
        'lingua' => '',
        'descrizione' => '',
        'pagine' => '',
        'copertina' => '',
        'categoria' => '',
        'tags' => '',
    ];

    foreach ($dati as $chiave => $valore) {
        $dati[$chiave] = isset($post[$chiave]) ? trim((string) $post[$chiave]) : '';
    }

    if ($dati['categoria'] === '__NEW__') {
        $dati['categoria'] = isset($post['categoria_nuova']) ? trim((string) $post['categoria_nuova']) : '';
    }

    $campiObbligatori = ['codice_isbn', 'titolo', 'autore', 'casa_editrice', 'anno', 'lingua', 'descrizione', 'pagine', 'categoria'];
    $campiMancanti = [];
    foreach ($campiObbligatori as $campo) {
        if ($dati[$campo] === '') $campiMancanti[] = $campo;
    }

    if (!empty($campiMancanti)) {
        $result['errorMessage'] = 'Compila tutti i campi obbligatori: ' . implode(', ', $campiMancanti);
        $result['dati'] = $dati;
        return $result;
    }

    try {
        $fileCopertina = null;
        if (isset($files['copertina_file']) && $files['copertina_file']['error'] === UPLOAD_ERR_OK) {
            $fileCopertina = validateAndProcessCopertina($files['copertina_file']);
            if (!$fileCopertina) {
                $result['errorMessage'] = 'Il file della copertina deve essere un file JPG valido (max 5MB).';
                $result['dati'] = $dati;
                return $result;
            }
            $dati['copertina_file'] = $fileCopertina;
        }

        $nuovoId = createLibro($conn, $dati);
        if ($nuovoId > 0) {
            if ($fileCopertina !== null) {
                $nomeFile = 'cover-' . $nuovoId . '.jpg';
                $percorsoDestinazione = __DIR__ . '/../images/' . $nomeFile;
                if (move_uploaded_file($fileCopertina['tmp_name'], $percorsoDestinazione)) {
                    $percorsoDb = 'images/' . $nomeFile;
                    $stmt = $conn->prepare('UPDATE libro SET copertina = ? WHERE id = ?');
                    $stmt->bind_param('si', $percorsoDb, $nuovoId);
                    $stmt->execute();
                    $stmt->close();
                    $result['successMessage'] = 'Libro inserito con successo e copertina salvata.';
                } else {
                    $result['successMessage'] = 'Libro inserito con successo, ma la copertina non è stata salvata.';
                }
            } else {
                $result['successMessage'] = 'Libro inserito con successo.';
            }
            $result['dati'] = array_fill_keys(array_keys($dati), '');
        } else {
            $result['errorMessage'] = 'Impossibile inserire il libro.';
            $result['dati'] = $dati;
        }
    } catch (Throwable $e) {
        $result['errorMessage'] = 'Impossibile inserire il libro: ' . $e->getMessage();
        $result['dati'] = $dati;
    }

    return $result;
}

function handleModificaLibro($conn, $libroId, array $post, array $files, array $libro = null) {
    $result = ['successMessage' => '', 'errorMessage' => '', 'redirect' => null];

    if ((int) $libroId <= 0) {
        $result['errorMessage'] = 'ID libro non valido.';
        return $result;
    }

    $dati = [
        'codice_isbn' => trim((string) ($post['codice_isbn'] ?? '')),
        'titolo' => trim((string) ($post['titolo'] ?? '')),
        'autore' => trim((string) ($post['autore'] ?? '')),
        'casa_editrice' => trim((string) ($post['casa_editrice'] ?? '')),
        'edizione' => (int) ($post['edizione'] ?? 0),
        'anno' => (int) ($post['anno'] ?? 0),
        'lingua' => trim((string) ($post['lingua'] ?? '')),
        'descrizione' => trim((string) ($post['descrizione'] ?? '')),
        'pagine' => (int) ($post['pagine'] ?? 0),
        'copertina' => trim((string) ($post['copertina'] ?? '')),
        'categoria' => trim((string) ($post['categoria'] ?? '')),
        'tags' => trim((string) ($post['tags'] ?? '')),
    ];

    if (isset($post['categoria']) && $post['categoria'] === '__NEW__') {
        $dati['categoria'] = trim((string) ($post['categoria_nuova'] ?? ''));
    }

    try {
        if (isset($post['delete_copertina']) && $post['delete_copertina'] === '1') {
            if (!empty($libro['copertina'])) {
                $basename = basename($libro['copertina']);
                if ($basename !== basename(DEFAULT_COVER)) {
                    $pathToDelete = __DIR__ . '/../' . $libro['copertina'];
                    if (is_file($pathToDelete)) {@unlink($pathToDelete);} 
                }
            }
            $dati['copertina'] = DEFAULT_COVER;
        }

        if (isset($files['copertina_file']) && $files['copertina_file']['error'] === UPLOAD_ERR_OK) {
            $file = validateAndProcessCopertina($files['copertina_file']);
            if ($file === null) {
                throw new RuntimeException('File copertina non valido. Usa JPG fino a 5MB.');
            }

            if (!empty($libro['copertina'])) {
                $basenameOld = basename($libro['copertina']);
                if ($basenameOld !== basename(DEFAULT_COVER)) {
                    $old = __DIR__ . '/../' . $libro['copertina'];
                    if (is_file($old)) {@unlink($old);}    
                }
            }

            $nomeFile = 'cover-' . $libroId . '.jpg';
            $percorsoDestinazione = __DIR__ . '/../images/' . $nomeFile;
            if (move_uploaded_file($file['tmp_name'], $percorsoDestinazione)) {
                $dati['copertina'] = 'images/' . $nomeFile;
            } else {
                throw new RuntimeException('Impossibile salvare il file della copertina.');
            }
        }

        if (updateLibro($conn, $libroId, $dati)) {
            $result['redirect'] = 'libri.php?updated=1';
        } else {
            $result['errorMessage'] = 'Aggiornamento non eseguito.';
        }
    } catch (Throwable $e) {
        $result['errorMessage'] = 'Impossibile aggiornare il libro: ' . $e->getMessage();
    }

    return $result;
}

function handleEliminaLibro($conn, $libroId, array $post) {
    $result = ['message' => '', 'errorMessage' => '', 'redirect' => null];

    if ((int) $libroId <= 0) {
        $result['message'] = 'ID libro non valido.';
        return $result;
    }

    try {
        if (deleteLibroWithCascade($conn, $libroId)) {
            $result['redirect'] = 'libri.php?deleted=1';
        } else {
            $result['message'] = 'Eliminazione non eseguita.';
        }
    } catch (Throwable $e) {
        $result['message'] = 'Impossibile eliminare il libro: ' . $e->getMessage();
    }

    return $result;
}

function handlePrestitiActions($conn, array $post) {
    $result = ['message' => '', 'prestitoInModifica' => null, 'utenteId' => isset($post['utente_id']) ? (int) $post['utente_id'] : 0];

    if (!isset($post['prestito_id'], $post['azione'])) return $result;
    $prestitoId = (int) $post['prestito_id'];
    $azione = (string) $post['azione'];

    try {
        if ($azione === 'concludi') {
            $result['message'] = concludePrestito($conn, $prestitoId) ? 'Prestito concluso con successo.' : 'Operazione non riuscita.';
        } elseif ($azione === 'proroga') {
            $res = prorogaPrestito($conn, $prestitoId);
            if (is_array($res)) {
                $result['message'] = $res['message'] ?? 'Operazione non riuscita.';
            } elseif ($res === true) {
                $result['message'] = 'Prestito prorogato di 30 giorni.';
            } else {
                $result['message'] = 'Operazione non riuscita.';
            }
        } elseif ($azione === 'elimina') {
            $result['message'] = deletePrestitoWithReferences($conn, $prestitoId) ? 'Prestito eliminato con successo.' : 'Operazione non riuscita.';
        } elseif ($azione === 'modifica') {
            $prestito = getPrestitoById($conn, $prestitoId);
            if (!$prestito) {
                $result['message'] = 'Prestito non trovato.';
            } else {
                $result['prestitoInModifica'] = $prestito;
            }
        } elseif ($azione === 'salva_modifica') {
            $dataInizioNorm = normalizeDateTimeForDb(trim((string) ($post['data_inizio'] ?? '')));
            $dataFineNorm = normalizeDateTimeForDb(trim((string) ($post['data_fine'] ?? '')));

            $dati = [
                'data_inizio' => $dataInizioNorm ?? '',
                'data_fine' => $dataFineNorm ?? '',
                'stato' => trim((string) ($post['stato'] ?? '')),
                'biblioteca_id' => 1,
                'libro_id' => (int) ($post['libro_id'] ?? 0),
                'utente_id' => (int) ($post['nuovo_utente_id'] ?? 0),
            ];

            $statiValidi = ['attivo', 'concluso', 'in_ritardo'];
            $formValido =
                $dati['data_inizio'] !== ''
                && $dati['data_fine'] !== ''
                && in_array($dati['stato'], $statiValidi, true)
                && $dati['biblioteca_id'] > 0
                && $dati['libro_id'] > 0
                && $dati['utente_id'] > 0
                && strtotime($dati['data_fine']) >= strtotime($dati['data_inizio']);

            if ($dataInizioNorm === null || $dataFineNorm === null) {
                $result['message'] = 'Formato data non valido.';
                $formValido = false;
            }

            if ($formValido) {
                if (!isLibroExists($conn, $dati['libro_id'])) {
                    $result['message'] = 'Errore: il libro specificato non esiste.';
                    $formValido = false;
                } elseif (!isUtenteExists($conn, $dati['utente_id'])) {
                    $result['message'] = 'Errore: l\'utente specificato non esiste.';
                    $formValido = false;
                }
            }

            if (!$formValido) {
                if ($result['message'] === '') $result['message'] = 'Dati non validi: controlla campi, stato e date.';
                $result['prestitoInModifica'] = getPrestitoById($conn, $prestitoId);
            } else {
                if (updatePrestito($conn, $prestitoId, $dati)) {
                    $result['message'] = 'Prestito aggiornato con successo.';
                    $result['utenteId'] = $dati['utente_id'];
                    if (function_exists('aggiornaPrestitiScaduti')) {
                        aggiornaPrestitiScaduti($conn);
                    }
                } else {
                    $result['message'] = 'Aggiornamento non riuscito.';
                    $result['prestitoInModifica'] = getPrestitoById($conn, $prestitoId);
                }
            }
        }
    } catch (Throwable $e) {
        $result['message'] = 'Operazione non riuscita: ' . $e->getMessage();
    }

    return $result;
}

function handleRecensioniActions($conn, array $post) {
    $result = ['message' => ''];
    if (!isset($post['recensione_id'], $post['azione'])) return $result;
    $recensioneId = (int) $post['recensione_id'];
    $azione = (string) $post['azione'];

    try {
        if ($azione === 'elimina') {
            $result['message'] = deleteRecensione($conn, $recensioneId) ? 'Operazione completata con successo.' : 'Operazione non riuscita.';
        } elseif ($azione === 'censura') {
            $result['message'] = censuraRecensione($conn, $recensioneId) ? 'Operazione completata con successo.' : 'Operazione non riuscita.';
        }
    } catch (Throwable $e) {
        $result['message'] = 'Operazione non riuscita: ' . $e->getMessage();
    }

    return $result;
}

?>
