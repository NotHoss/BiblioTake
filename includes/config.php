<?php

// Connessione database (default MAMP: utente root con password root)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'bibliotake');

// Connessione database Tecweb (nome database: username_laboratorio, user: username_laboratorio, password: copiata da pwd_db_2526.txt)
/*
define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');
*/

// Identita del sito
define('SITE_NAME',        'BiblioTake');
define('SITE_DESCRIPTION', 'Biblioteca digitale del corso di Tecnologie Web');

// Paginazione del catalogo
define('MAX_PER_PAGINA', 12);

// Lunghezze minime per la validazione
define('MIN_PASSWORD_LENGTH', 8);
define('MIN_USERNAME_LENGTH', 3);
define('MAX_USERNAME_LENGTH', 50);

// Avatar di default per i nuovi utenti
define('DEFAULT_AVATAR', 'images/avatar/default-avatar.webp');

// Copertina di default per i nuovi libri
define('DEFAULT_COVER', 'images/cover/default-cover.jpg');

// In sviluppo gli errori sono visibili. In produzione invertire i due flag.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors',     '1');

// Fuso orario coerente con la biblioteca padovana
date_default_timezone_set('Europe/Rome');

?>
