<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions/db.php';
require_once __DIR__ . '/functions/auth.php';

// File di dominio caricati solo se gia presenti sul branch corrente.
// Gli altri membri del team li introducono via merge: il guard evita
// fatal error quando feat/auth e da solo su develop.
$__functionFiles = array(
    'libro_functions.php',
    'prestito_functions.php',
    'recensione_functions.php',
    'admin_functions.php',
    'user_functions.php',
);
foreach ($__functionFiles as $__file) {
    $__path = __DIR__ . '/functions/' . $__file;
    if (file_exists($__path)) {
        require_once $__path;
    }
}
unset($__file, $__path, $__functionFiles);

require_once __DIR__ . '/variables.php';

$conn = getConnection();

// Aggiorna automaticamente i prestiti scaduti prima di qualsiasi operazione
if ($conn instanceof mysqli && function_exists('aggiornaPrestitiScaduti')) {
    aggiornaPrestitiScaduti($conn);
}

?>
