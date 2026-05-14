<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Aggiungi libro — Admin BiblioTake';
$currentPage = 'admin';
$errorMessage = '';
$successMessage = '';

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
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '') {
    foreach ($dati as $chiave => $valore) {
        $dati[$chiave] = isset($_POST[$chiave]) ? trim((string) $_POST[$chiave]) : '';
    }

    // Gestisci la categoria (nuova o scelta)
    if ($dati['categoria'] === '__NEW__') {
        $dati['categoria'] = isset($_POST['categoria_nuova']) ? trim((string) $_POST['categoria_nuova']) : '';
    }

    // Validazione campi obbligatori (tutti tranne copertina)
    $campiObbligatori = ['codice_isbn', 'titolo', 'autore', 'casa_editrice', 'anno', 'lingua', 'descrizione', 'pagine', 'categoria'];
    $campiMancanti = [];
    
    foreach ($campiObbligatori as $campo) {
        if ($dati[$campo] === '') {
            $campiMancanti[] = $campo;
        }
    }
    
    if (!empty($campiMancanti)) {
        $errorMessage = 'Compila tutti i campi obbligatori: ' . implode(', ', $campiMancanti);
    } else {
        try {
            // Gestione dell'upload della copertina (facoltativo)
            $fileCopertina = null;
            if (isset($_FILES['copertina_file']) && $_FILES['copertina_file']['error'] === UPLOAD_ERR_OK) {
                $fileCopertina = validateAndProcessCopertina($_FILES['copertina_file']);
                if (!$fileCopertina) {
                    $errorMessage = 'Il file della copertina deve essere un file JPG valido (max 5MB).';
                } else {
                    $dati['copertina_file'] = $fileCopertina;
                }
            }
            
            if ($errorMessage === '') {
                $nuovoId = createLibro($conn, $dati);
                if ($nuovoId > 0) {
                    // Se è stato caricato un file, salvalo con il nome corretto
                    if ($fileCopertina !== null) {
                        $nomeFile = 'cover-' . $nuovoId . '.jpg';
                        $percorsoDestinazione = __DIR__ . '/../images/' . $nomeFile;
                        
                        if (move_uploaded_file($fileCopertina['tmp_name'], $percorsoDestinazione)) {
                            // Aggiorna il record del libro con il percorso della copertina
                            $percorsoDb = 'images/' . $nomeFile;
                            $stmt = $conn->prepare('UPDATE libro SET copertina = ? WHERE id = ?');
                            $stmt->bind_param('si', $percorsoDb, $nuovoId);
                            $stmt->execute();
                            $stmt->close();
                            $successMessage = 'Libro inserito con successo e copertina salvata.';
                        } else {
                            $successMessage = 'Libro inserito con successo, ma la copertina non è stata salvata.';
                        }
                    } else {
                        $successMessage = 'Libro inserito con successo.';
                    }
                    $dati = array_fill_keys(array_keys($dati), '');
                } else {
                    $errorMessage = 'Impossibile inserire il libro.';
                }
            }
        } catch (Throwable $e) {
            $errorMessage = 'Impossibile inserire il libro: ' . $e->getMessage();
        }
    }
}
$adminViewMode = 'create';
$categorie = getCategorieLibri($conn);
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showLibriAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}