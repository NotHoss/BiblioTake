<?php
session_start();
require_once __DIR__ . '/../includes/resources.php';
requireRole('utente');

//accetta solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$libroId  = isset($_POST['libro_id']) ? (int) $_POST['libro_id'] : 0;
$utenteId = (int) $_SESSION['user_id'];

if ($libroId <= 0) {
    header('Location: ../404.php');
    exit;
}

$libro = getLibroById($conn, $libroId);
if (!$libro) {
    header('Location: ../404.php');
    exit;
}

//controlla che il libro sia disponibile e che l'utente non abbia gia' un prestito attivo
if (!isLibroDisponibile($conn, $libroId) || utenteHaPrestitoAttivo($conn, $libroId, $utenteId)) {
    header('Location: ../dettaglio-libro.php?id=' . $libroId);
    exit;
}

creaPrestito($conn, $libroId, $utenteId);

header('Location: ../dettaglio-libro.php?id=' . $libroId);
exit;
