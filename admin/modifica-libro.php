<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Modifica libro - Amministrazione BiblioTake';
$pageDescription = 'Modifica i dettagli di un libro presente nel catalogo.';
$pageKeywords = 'amministrazione, modifica libro, catalogo, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php', 'lang' => 'en'),
    array('label' => 'Admin', 'href' => 'index.php', 'lang' => 'en'),
    array('label' => 'Gestione libri', 'href' => 'libri.php'),
    array('label' => 'Modifica libro', 'href' => ''),
);
$currentPage = 'admin';
$errorMessage = '';
$successMessage = '';
$returnUrl = getSafeAdminReturnUrl('libri.php');

$libroId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$libro = null;
if ($conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $libro = getLibroById($conn, $libroId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $res = ['successMessage' => '', 'errorMessage' => '', 'redirect' => null];

    if ((int) $libroId <= 0) {
        $res['errorMessage'] = 'ID libro non valido.';
    } else {
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
            'copertina' => trim((string) ($libro['copertina'] ?? '')),
            'categoria' => trim((string) ($_POST['categoria'] ?? '')),
            'tags' => trim((string) ($_POST['tags'] ?? '')),
        ];

        if (isset($_POST['categoria']) && $_POST['categoria'] === '__NEW__') {
            $dati['categoria'] = trim((string) ($_POST['categoria_nuova'] ?? ''));
        }

        try {
            $validation = validateLibroAdminData($dati);
            $dati = $validation['dati'];
            if (!$validation['ok']) {
                throw new InvalidArgumentException($validation['errorMessage']);
            }
            if (!isCodiceIsbnDisponibile($conn, $dati['codice_isbn'], $libroId)) {
                throw new InvalidArgumentException('Questo codice identificativo e gia presente nel catalogo.');
            }

            if (isset($_POST['delete_copertina']) && $_POST['delete_copertina'] === '1') {
                if (!empty($libro['copertina'])) {
                    $basename = basename($libro['copertina']);
                    if ($basename !== basename(DEFAULT_COVER)) {
                        $pathToDelete = __DIR__ . '/../' . $libro['copertina'];
                        if (is_file($pathToDelete)) {unlink($pathToDelete);} 
                    }
                }
                $dati['copertina'] = DEFAULT_COVER;
            }

            if (isset($_FILES['copertina_file']) && $_FILES['copertina_file']['error'] === UPLOAD_ERR_OK) {
                $file = validateAndProcessCopertina($_FILES['copertina_file']);
                if ($file === null) {
                    throw new RuntimeException('File copertina non valido. Usa un .jpg di massimo 5 megabyte con dimensioni esatte 705 x 1125 pixel.');
                }

                if (!empty($libro['copertina'])) {
                    $basenameOld = basename($libro['copertina']);
                    if ($basenameOld !== basename(DEFAULT_COVER)) {
                        $old = __DIR__ . '/../' . $libro['copertina'];
                        if (is_file($old)) {unlink($old);}    
                    }
                }

                $nomeFile = 'cover-' . $libroId . '.jpg';
                $percorsoDestinazione = __DIR__ . '/../images/cover/' . $nomeFile;
                if (move_uploaded_file($file['tmp_name'], $percorsoDestinazione)) {
                    $dati['copertina'] = 'images/cover/' . $nomeFile;
                } else {
                    throw new RuntimeException('Impossibile salvare il file della copertina.');
                }
            }

            if (updateLibro($conn, $libroId, $dati)) {
                $res['redirect'] = appendAdminQueryParam($returnUrl, ['updated' => 1]);
            } else {
                $res['errorMessage'] = 'Aggiornamento non eseguito.';
            }
        } catch (Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'codice_isbn') !== false) {
                $res['errorMessage'] = 'Impossibile aggiornare il libro: questo codice identificativo e gia presente nel catalogo.';
            } else {
                $res['errorMessage'] = 'Impossibile aggiornare il libro: ' . $e->getMessage();
            }
            $libro = array_merge($libro ?: [], $dati);
        }
    }

    if (!empty($res['redirect'])) {
        header('Location: ' . $res['redirect']);
        exit;
    }
    $errorMessage = $res['errorMessage'] ?? '';
    $successMessage = $res['successMessage'] ?? '';
}
$adminViewMode = 'edit';
$categorie = getCategorieLibri($conn);
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showModificaLibroAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
