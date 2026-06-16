<?php
require_once '../includes/resources.php';
requireRole('admin');

$pageTitle = 'Elimina libro - Amministrazione BiblioTake';
$pageDescription = 'Rimuovi un libro dal catalogo (operazione irreversibile).';
$pageKeywords = 'amministrazione, elimina libro, catalogo, BiblioTake';
$breadcrumb = array(
    array('label' => 'Home', 'href' => '../index.php', 'lang' => 'en'),
    array('label' => 'Admin', 'href' => 'index.php', 'lang' => 'en'),
    array('label' => 'Gestione libri', 'href' => 'libri.php'),
    array('label' => 'Elimina libro', 'href' => ''),
);
$currentPage = 'admin';
$message = '';
$errorMessage = '';
$returnUrl = getSafeAdminReturnUrl('libri.php');

$libroId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

$libro = null;
if ($conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $libro = getLibroById($conn, $libroId);
}

$res = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $libroId > 0) {
    $res = ['message' => '', 'errorMessage' => '', 'redirect' => null];

    if ((int) $libroId <= 0) {
        $res['message'] = 'ID libro non valido.';
    } else {
        try {
            if (deleteLibroWithCascade($conn, $libroId)) {
                $res['redirect'] = appendAdminQueryParam($returnUrl, ['deleted' => 1]);
            } else {
                $res['message'] = 'Eliminazione non eseguita.';
            }
        } catch (Throwable $e) {
            $res['message'] = 'Impossibile eliminare il libro: ' . $e->getMessage();
        }
    }

    if (!empty($res['redirect'])) {
        header('Location: ' . $res['redirect']);
        exit;
    }
    $message = $res['message'] ?? $message;
}
$adminViewMode = 'delete';
require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showEliminaLibroAdmin.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}
