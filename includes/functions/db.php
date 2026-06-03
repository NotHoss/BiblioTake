<?php

function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            error_log('Connessione DB fallita: ' . $conn->connect_error);
            header('Location: 500.php');
            exit;
        }

        $conn->set_charset('utf8mb4');
        return $conn;
    } catch (mysqli_sql_exception $e) {
        error_log('Connessione DB fallita: ' . $e->getMessage());
        header('Location: 500.php');
        exit;
    }
}

?>
