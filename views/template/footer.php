<?php
$template = file_get_contents(__DIR__ . '/../../html/template/footer.html');

//calcola il percorso relativo alla root del progetto (stessa logica di header.php),erve per il placeholder [WEB_ROOT] usato dai tag <script>
$projectRoot = realpath(__DIR__ . '/../..');
$scriptDir   = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
if ($projectRoot !== false && $scriptDir !== false && strpos($scriptDir, $projectRoot) === 0) {
    $rel   = ltrim(substr($scriptDir, strlen($projectRoot)), '/\\');
    $depth = ($rel !== '') ? substr_count(str_replace('\\', '/', $rel), '/') + 1 : 0;
} else {
    $depth = 0;
}
$baseUrl = $depth > 0 ? implode('/', array_fill(0, $depth, '..')) : '.';

$replacements = array(
    '[YEAR]' => date('Y'),
    '[SITE_NAME]' => htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'),
    '[WEB_ROOT]' => $baseUrl,
);

echo strtr($template, $replacements);
