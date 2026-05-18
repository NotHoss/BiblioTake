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

$res = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $res = handleEliminaLibro($conn, $libroId, $_POST);
    if (!empty($res['redirect'])) {
        header('Location: ' . $res['redirect']);
        exit;
    }
    $message = $res['message'] ?? $message;
}
$adminViewMode = 'delete';
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showLibriAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}