<?php

// Variabili lette da $_GET riutilizzate in piu modelli.
// Mai mettere qui variabili specifiche di una sola pagina.

$id     = isset($_GET['id'])     ? (int) $_GET['id']                                      : 0;
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action'], ENT_QUOTES, 'UTF-8') : '';
$page   = isset($_GET['page'])   ? max(1, (int) $_GET['page'])                            : 1;

?>
