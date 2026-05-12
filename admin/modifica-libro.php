<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Modifica libro — Admin BiblioTake';
$currentPage = 'admin';
$errorMessage = '';
$errorMessage = '';

$libroId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$libro = null;
if ($conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $libro = getLibroById($conn, $libroId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $dati = [
        'codice_isbn' => trim((string) ($_POST['codice_isbn'] ?? '')),
        'titolo' => trim((string) ($_POST['titolo'] ?? '')),
        'autore' => trim((string) ($_POST['autore'] ?? '')),
        'casa_editrice' => trim((string) ($_POST['casa_editrice'] ?? '')),
        'edizione' => (int) ($_POST['edizione'] ?? 0),
        'anno' => (int) ($_POST['anno'] ?? 0),
        'lingua' => trim((string) ($_POST['lingua'] ?? '')),
        'descrizione' => trim((string) ($_POST['descrizione'] ?? '')),
        'pagine' => (int) ($_POST['pagine'] ?? 0),
        'copertina' => trim((string) ($_POST['copertina'] ?? '')),
        'categoria' => trim((string) ($_POST['categoria'] ?? '')),
    ];

    // Gestione categoria nuova
    if (isset($_POST['categoria']) && $_POST['categoria'] === '__NEW__') {
        $dati['categoria'] = trim((string) ($_POST['categoria_nuova'] ?? ''));
    }

    try {
        // Gestione cancellazione della copertina esistente
        if (isset($_POST['delete_copertina']) && $_POST['delete_copertina'] === '1') {
            if (!empty($libro['copertina'])) {
                $basename = basename($libro['copertina']);
                if ($basename !== basename(DEFAULT_COVER)) {
                    $pathToDelete = __DIR__ . '/../' . $libro['copertina'];
                    if (is_file($pathToDelete)) {
                        @unlink($pathToDelete);
                    }
                }
            }
            $dati['copertina'] = DEFAULT_COVER;
        }

        // Gestione upload nuovo file copertina (facoltativo)
        if (isset($_FILES['copertina_file']) && $_FILES['copertina_file']['error'] === UPLOAD_ERR_OK) {
            $file = validateAndProcessCopertina($_FILES['copertina_file']);
            if ($file === null) {
                throw new RuntimeException('File copertina non valido. Usa JPG fino a 5MB.');
            }

            // Rimuovi eventuale copertina esistente (se non placeholder)
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
            header('Location: libri.php?updated=1');
            exit;
        }
        $errorMessage = 'Aggiornamento non eseguito.';
    } catch (Throwable $e) {
        $errorMessage = 'Impossibile aggiornare il libro: ' . $e->getMessage();
    }
}
$adminViewMode = 'edit';
$categorie = getCategorieLibri($conn);
require_once __DIR__ . '/../views/showLibriAdmin.php';

if ($conn instanceof mysqli) {
    $conn->close();
}