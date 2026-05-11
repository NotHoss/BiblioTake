<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Elimina libro — Admin BiblioTake';
$currentPage = 'admin';
$message = '';
$errorMessage = '';

$libroId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

$libro = null;
if ($conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $libro = getLibroById($conn, $libroId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    try {
        if (deleteLibroWithCascade($conn, $libroId)) {
            header('Location: libri.php?deleted=1');
            exit;
        }
        $message = 'Eliminazione non eseguita.';
    } catch (Throwable $e) {
        $message = 'Impossibile eliminare il libro: ' . $e->getMessage();
    }
}
$adminViewMode = 'delete';
require_once __DIR__ . '/../views/showLibriAdmin.php';

if ($conn instanceof mysqli) {
    $conn->close();
}