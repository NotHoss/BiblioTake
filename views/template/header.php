<?php
// Il template apre il documento HTML fino a <main id="main-content">.
// Si aspetta dal modello chiamante: $pageTitle, $pageDescription,
// $pageKeywords, $currentPage e (opzionale) $breadcrumb come array di
// elementi con chiavi label e href.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$template = file_get_contents(__DIR__ . '/../../html/template/header.html');

$navItems = array(
    array('key' => 'home',     'href' => 'index.php',    'label' => 'Home',        'lang' => 'en'),
    array('key' => 'catalogo', 'href' => 'catalogo.php', 'label' => 'Catalogo'),
    array('key' => 'about',    'href' => 'about.php',    'label' => 'Chi siamo'),
    array('key' => 'contatti', 'href' => 'contatti.php', 'label' => 'Contatti'),
);

$siteName = htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8');
$pageTitle = isset($pageTitle) && $pageTitle !== '' ? $pageTitle : SITE_NAME;
$pageDescription = isset($pageDescription) && $pageDescription !== '' ? $pageDescription : SITE_DESCRIPTION;
$pageKeywords = isset($pageKeywords) ? $pageKeywords : '';

$navigation = array();
$baseUrl = rtrim(WEB_ROOT, '/');

foreach ($navItems as $item) {
    $isCurrent = isset($currentPage) && $currentPage === $item['key'];
    $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
    $langAttr = isset($item['lang']) ? ' lang="' . htmlspecialchars($item['lang'], ENT_QUOTES, 'UTF-8') . '"' : '';

    if ($isCurrent) {
        $navigation[] = '<li aria-current="page" class="current-page"><span' . $langAttr . '>' . $label . '</span></li>';
        continue;
    }

    $href = $item['href'];
    if (!preg_match('#^(https?://|/)#i', $href)) {
        $href = $baseUrl . '/' . ltrim($href, '/');
    }

    $navigation[] = '<li><a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"' . $langAttr . '>' . $label . '</a></li>';
}

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        if (isset($currentPage) && $currentPage === 'admin') {
            $navigation[] = '<li aria-current="page" class="current-page">Amministrazione</li>';
        } else {
            $navigation[] = '<li><a href="' . htmlspecialchars($baseUrl . '/admin/index.php', ENT_QUOTES, 'UTF-8') . '">Amministrazione</a></li>';
        }
    } else {
        if (isset($currentPage) && $currentPage === 'dashboard') {
            $navigation[] = '<li aria-current="page" class="current-page">Area utente</li>';
        } else {
            $navigation[] = '<li><a href="' . htmlspecialchars($baseUrl . '/dashboard.php', ENT_QUOTES, 'UTF-8') . '">Area utente</a></li>';
        }
    }

    $navigation[] = '<li><a href="' . htmlspecialchars($baseUrl . '/logout.php', ENT_QUOTES, 'UTF-8') . '">Esci</a></li>';
} else {
    if (isset($currentPage) && $currentPage === 'login') {
        $navigation[] = '<li aria-current="page" class="current-page">Accedi</li>';
    } else {
        $navigation[] = '<li><a href="' . htmlspecialchars($baseUrl . '/login.php', ENT_QUOTES, 'UTF-8') . '">Accedi</a></li>';
    }

    if (isset($currentPage) && $currentPage === 'register') {
        $navigation[] = '<li aria-current="page" class="current-page">Registrati</li>';
    } else {
        $navigation[] = '<li><a href="' . htmlspecialchars($baseUrl . '/register.php', ENT_QUOTES, 'UTF-8') . '">Registrati</a></li>';
    }
}

$breadcrumbHtml = '';
if (!empty($breadcrumb) && is_array($breadcrumb)) {
    $breadcrumbItems = array();
    $lastIndex = count($breadcrumb) - 1;

    foreach ($breadcrumb as $index => $crumb) {
        $label = isset($crumb['label']) ? htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') : '';
        $href = isset($crumb['href']) ? trim($crumb['href']) : '';

        if ($index === $lastIndex || $href === '') {
            $breadcrumbItems[] = '<li><span aria-current="page">' . $label . '</span></li>';
        } else {
            $breadcrumbItems[] = '<li><a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' . $label . '</a></li>';
        }
    }

    $breadcrumbHtml = '<nav class="breadcrumb" aria-label="Percorso nel sito"><ul>' . implode('', $breadcrumbItems) . '</ul></nav>';
}

$replacements = array(
    '[PAGE_TITLE]' => htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'),
    '[PAGE_DESCRIPTION]' => htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'),
    '[PAGE_KEYWORDS]' => htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8'),
    '[SITE_NAME]' => $siteName,
    '[NAVIGATION]' => implode('', $navigation),
    '[WEB_ROOT]' => $baseUrl,
    '[BREADCRUMB]' => $breadcrumbHtml,
);

echo strtr($template, $replacements);
