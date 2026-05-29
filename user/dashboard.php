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

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showDashboard.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}