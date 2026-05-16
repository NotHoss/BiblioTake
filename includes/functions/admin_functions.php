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

    if ($hasOrari) {
        $stmt = $conn->prepare(
            'SELECT id, indirizzo, telefono, email, orario_lun_ven, orario_sabato, orario_domenica
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
    $stmt->execute();
    $result = $stmt->get_result();
    $biblioteca = $result->fetch_assoc();
    $stmt->close();

    if ($biblioteca && !$hasOrari) {
        $biblioteca['orario_lun_ven'] = '9:00 - 19:00';
        $biblioteca['orario_sabato'] = '9:00 - 13:00';
        $biblioteca['orario_domenica'] = 'Chiuso';
    }

    return $biblioteca ?: null;
}

function updateBibliotecaInfo($conn, $bibliotecaId, array $dati) {
    $hasOrari = bibliotecaHasOrariColumns($conn);

    if ($hasOrari) {
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
