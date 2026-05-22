<?php
$template = file_get_contents(__DIR__ . '/../../html/template/footer.html');

$replacements = array(
    '[YEAR]' => date('Y'),
    '[SITE_NAME]' => htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'),
);

echo strtr($template, $replacements);
