<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Dashboard Admin — BiblioTake';
$currentPage = 'admin';

$errorMessage = '';
$successMessage = '';
$isEditGeneralita = isset($_GET['mode']) && $_GET['mode'] === 'edit';

$biblioteca = null;
if ($conn instanceof mysqli && $errorMessage === '') {
    $biblioteca = getBibliotecaInfo($conn);
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && $conn instanceof mysqli
    && $errorMessage === ''
    && isset($_POST['azione'])
    && $_POST['azione'] === 'aggiorna_generalita'
    && isset($_POST['biblioteca_id'])
) {
    $bibliotecaId = (int) $_POST['biblioteca_id'];
    $dati = [
        'indirizzo' => trim((string) ($_POST['indirizzo'] ?? '')),
        'telefono' => trim((string) ($_POST['telefono'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
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
            $successMessage = 'Generalità della biblioteca aggiornate con successo.';
            $biblioteca = getBibliotecaInfo($conn);
            $isEditGeneralita = false;
        } else {
            $errorMessage = 'Aggiornamento delle generalità non riuscito.';
            $isEditGeneralita = true;
        }
    } else {
        $errorMessage = 'Compila tutti i campi delle generalità e degli orari.';
        $isEditGeneralita = true;
    }
}

// Calcola statistiche usando la funzione
$stats = ['libri' => 0, 'utenti' => 0];
if ($conn instanceof mysqli && $errorMessage === '') {
    $allStats = getStatisticheGenerali($conn);
    $stats['libri'] = $allStats['libri_totali'];
    $stats['utenti'] = $allStats['utenti_totali'];
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showDashboardAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}