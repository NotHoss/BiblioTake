<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Modifica libro — Admin BiblioTake';
$pageDescription = 'Modifica i dettagli di un libro presente nel catalogo.';
$pageKeywords = 'admin, modifica libro, catalogo, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php'),
    array('label' => 'Admin', 'href' => 'index.php'),
    array('label' => 'Libri', 'href' => 'libri.php'),
    array('label' => 'Modifica', 'href' => ''),
);
$currentPage = 'admin';
$errorMessage = '';
$errorMessage = '';

$libroId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);
$libro = null;
if ($conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $libro = getLibroById($conn, $libroId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $res = handleModificaLibro($conn, $libroId, $_POST, $_FILES, $libro);
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