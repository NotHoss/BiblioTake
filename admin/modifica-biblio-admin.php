<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Modifica generalità biblioteca — Admin BiblioTake';
$pageDescription = 'Aggiorna indirizzo, contatti e orari della biblioteca.';
$pageKeywords = 'admin, biblioteca, modifica, generalità, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Modifica generalità biblioteca', 'href' => ''),
);
$currentPage = 'admin';

$errorMessage = '';
$successMessage = '';

$biblioteca = null;
if ($conn instanceof mysqli) {
    $biblioteca = getBibliotecaInfo($conn);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli) {
    $res = ['successMessage' => '', 'errorMessage' => '', 'biblioteca' => null, 'isEditGeneralita' => false];

    if (!isset($_POST['azione']) || $_POST['azione'] !== 'aggiorna_generalita' || !isset($_POST['biblioteca_id'])) {
        // nothing to do
    } else {
        $bibliotecaId = (int) $_POST['biblioteca_id'];
        $dati = [
            'indirizzo' => trim((string) ($_POST['indirizzo'] ?? '')),
            'telefono' => trim((string) ($_POST['telefono'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'note' => trim((string) ($_POST['note'] ?? '')),
            'orario_lun_ven' => trim((string) ($_POST['orario_lun_ven'] ?? '')),
            'orario_sabato' => trim((string) ($_POST['orario_sabato'] ?? '')),
            'orario_domenica' => trim((string) ($_POST['orario_domenica'] ?? '')),
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
                $res['successMessage'] = 'Generalità della biblioteca aggiornate con successo.';
                $res['biblioteca'] = getBibliotecaInfo($conn);
                $res['isEditGeneralita'] = false;
            } else {
                $res['errorMessage'] = 'Aggiornamento delle generalità non riuscito.';
                $res['isEditGeneralita'] = true;
            }
        } else {
            $res['errorMessage'] = 'Compila tutti i campi obbligatori delle generalità e degli orari.';
            $res['isEditGeneralita'] = true;
        }
    }

    $successMessage = $res['successMessage'] ?? '';
    $errorMessage = $res['errorMessage'] ?? '';
    $biblioteca = $res['biblioteca'] ?? $biblioteca;

    if ($successMessage !== '') {
        header('Location: index.php?generalita_success=1');
        exit;
    }
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showModificaBiblioAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
