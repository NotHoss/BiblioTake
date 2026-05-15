<?php

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        error_log('Connessione DB fallita: ' . $conn->connect_error);
        header('Location: 500.php');
        exit;
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}

?>
