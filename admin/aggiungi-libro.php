<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Aggiungi libro - Amministrazione BiblioTake';
$pageDescription = 'Aggiungi un nuovo libro al catalogo della biblioteca.';
$pageKeywords = 'amministrazione, aggiungi libro, catalogo, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Gestione libri', 'href' => 'libri.php'),
    array('label' => 'Aggiungi libro', 'href' => ''),
);
$currentPage = 'admin';
$errorMessage = '';
$successMessage = '';
$returnUrl = getSafeAdminReturnUrl('libri.php');

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '') {
    $res = ['successMessage' => '', 'errorMessage' => '', 'dati' => []];

    foreach ($dati as $chiave => $valore) {
        $dati[$chiave] = isset($_POST[$chiave]) ? trim((string) $_POST[$chiave]) : '';
    }

    if ($dati['categoria'] === '__NEW__') {
        $dati['categoria'] = isset($_POST['categoria_nuova']) ? trim((string) $_POST['categoria_nuova']) : '';
    }

    $validation = validateLibroAdminData($dati);
    $dati = $validation['dati'];

    if (!$validation['ok']) {
        $res['errorMessage'] = $validation['errorMessage'];
        $res['dati'] = $dati;
    } elseif (!isCodiceIsbnDisponibile($conn, $dati['codice_isbn'])) {
        $res['errorMessage'] = 'Questo codice identificativo è gia presente nel catalogo.';
        $res['dati'] = $dati;
    } else {
        try {
            $fileCopertina = null;
            if (isset($_FILES['copertina_file']) && $_FILES['copertina_file']['error'] === UPLOAD_ERR_OK) {
                $fileCopertina = validateAndProcessCopertina($_FILES['copertina_file']);
                if (!$fileCopertina) {
                    $res['errorMessage'] = 'Il file della copertina deve essere un .jpg valido, massimo 5 megabyte, con dimensioni esatte 705 x 1125 pixel.';
                    $res['dati'] = $dati;
                }
            }

            if ($res['errorMessage'] === '') {
                $nuovoId = createLibro($conn, $dati);
                if ($nuovoId > 0) {
                    if ($fileCopertina !== null) {
                        $nomeFile = 'cover-' . $nuovoId . '.jpg';
                        $percorsoDestinazione = __DIR__ . '/../images/cover/' . $nomeFile;
                        if (move_uploaded_file($fileCopertina['tmp_name'], $percorsoDestinazione)) {
                            $percorsoDb = 'images/cover/' . $nomeFile;
                            $stmt = $conn->prepare('UPDATE libro SET copertina = ? WHERE id = ?');
                            $stmt->bind_param('si', $percorsoDb, $nuovoId);
                            $stmt->execute();
                            $stmt->close();
                            $res['successMessage'] = 'Libro inserito con successo e copertina salvata.';
                        } else {
                            $res['successMessage'] = 'Libro inserito con successo, ma la copertina non è stata salvata.';
                        }
                    } else {
                        $res['successMessage'] = 'Libro inserito con successo.';
                    }
                    $res['dati'] = array_fill_keys(array_keys($dati), '');
                    $res['redirect'] = appendAdminQueryParam($returnUrl, ['created' => 1]);
                } else {
                    $res['errorMessage'] = 'Impossibile inserire il libro.';
                    $res['dati'] = $dati;
                }
            }
        } catch (Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'codice_isbn') !== false) {
                $res['errorMessage'] = 'Impossibile inserire il libro: questo codice identificativo e gia presente nel catalogo.';
            } else {
                $res['errorMessage'] = 'Impossibile inserire il libro: ' . $e->getMessage();
            }
            $res['dati'] = $dati;
        }
    }

    $errorMessage = $res['errorMessage'] ?? '';
    $successMessage = $res['successMessage'] ?? '';
    $dati = $res['dati'] ?? $dati;
    if (!empty($res['redirect'])) {
        header('Location: ' . $res['redirect']);
        exit;
    }
}
$adminViewMode = 'create';
$categorie = getCategorieLibri($conn);
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showAggiungiLibroAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
