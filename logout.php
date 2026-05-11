<?php
session_start();
require_once 'includes/resources.php';

logoutUser();

header('Location: index.php?logout=1');
exit;
?>
