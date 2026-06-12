<?php
$template = file_get_contents(__DIR__ . '/../../html/template/footer.html');

//calcola il percorso relativo alla root del progetto (stessa logica di header.php),
//serve per il placeholder [WEB_ROOT] usato dai tag <script>
$projectRoot = dirname(__DIR__, 2);
$scriptDir   = dirname($_SERVER['SCRIPT_FILENAME']);
$rel         = ltrim(str_replace($projectRoot, '', $scriptDir), '/\\');
$depth       = ($rel !== '') ? substr_count($rel, '/') + 1 : 0;
$baseUrl     = $depth > 0 ? implode('/', array_fill(0, $depth, '..')) : '.';

$replacements = array(
    '[YEAR]' => date('Y'),
    '[SITE_NAME]' => htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'),
    '[WEB_ROOT]' => $baseUrl,
);

echo strtr($template, $replacements);
