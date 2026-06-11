<?php
session_start();
require_once __DIR__ . '/../includes/resources.php';

$pageTitle = 'Profilo | BiblioTake';
$currentPage = 'dashboard';
$errorMessage = '';
$successMessage = '';

$user = null;
if (isset($_SESSION['user_id'])) {
    $user = getUserInfo($conn, $_SESSION['user_id']);
}

$breadcrumb = array(
    array('label' => 'Home',     'href' => 'index.php'),
    array('label' => 'Profilo', 'href' => ''),
);

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/template/sidebar-user.php';
require_once __DIR__ . '/../views/showDashboard.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}