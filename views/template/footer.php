<?php
$template = file_get_contents(__DIR__ . '/../../html/template/footer.html');

// Calcola il percorso relativo alla root del progetto per gli script.
$projectRoot = realpath(__DIR__ . '/../..');
$scriptDir   = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
if ($projectRoot !== false && $scriptDir !== false && strpos($scriptDir, $projectRoot) === 0) {
    $rel   = ltrim(substr($scriptDir, strlen($projectRoot)), '/\\');
    $depth = ($rel !== '') ? substr_count(str_replace('\\', '/', $rel), '/') + 1 : 0;
} else {
    $depth = 0;
}
$relativeRoot = $depth > 0 ? implode('/', array_fill(0, $depth, '..')) : '.';
$validationPath = __DIR__ . '/../../js/validation.js';
$validationVersion = is_file($validationPath) ? (string) filemtime($validationPath) : (string) time();

$replacements = array(
    '[YEAR]' => date('Y'),
    '[SITE_NAME]' => htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'),
    '[RELATIVE_ROOT]' => $relativeRoot,
    '[VALIDATION_VERSION]' => htmlspecialchars($validationVersion, ENT_QUOTES, 'UTF-8'),
);

echo strtr($template, $replacements);
