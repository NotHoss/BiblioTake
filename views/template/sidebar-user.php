<?php

if (isset($_SESSION['user_id'])) {
    $user = getUserInfo($conn, $_SESSION['user_id']);

    $navItems = array(
    array('key' => $user['username'],     'href' => 'dashboard.php',    'label' => $user['username']),
    array('key' => 'prestiti_attivi', 'href' => 'prestiti-attivi.php', 'label' => 'Prestiti attivi'),
    array('key' => 'prestiti_passati',    'href' => 'prestiti-passati.php',    'label' => 'Prestiti passati'),
    array('key' => 'recensioni', 'href' => 'recensioni.php', 'label' => 'Recensioni'),
);
}
$template = file_get_contents(__DIR__ . '/../../html/template/sidebar-user.html');



$navigation = array();

foreach ($navItems as $item) {
    $isCurrent = isset($currentPage) && $currentPage === $item['key'];
    $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
    $langAttr = isset($item['lang']) ? ' lang="' . htmlspecialchars($item['lang'], ENT_QUOTES, 'UTF-8') . '"' : '';

    if ($isCurrent) {
        $navigation[] = '<li aria-current="page" class="current-page"><span' . $langAttr . '>' . $label . '</span></li>';
        continue;
    }
}

if(isset($_SESSION['user_id'])){
    if(isset($currentPage)){
        if ($currentPage === 'dashboard') {
            $usernameHtml = '<li aria-current="page" class="sidebar-current-page username"><p class="username">' . htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') . '</p></li>';
        } else {
            $usernameHtml = '<li class="sidebar-link username"><a class="username" href="dashboard.php">' . htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') . '</a></li>';
        }
        if ($currentPage === 'prestiti-attivi') {
            $prestitiAttiviHtml = '<li aria-current="page" class="sidebar-current-page">Prestiti Attivi</li>';
        } else {
            $prestitiAttiviHtml = '<li class="sidebar-link"><a href="prestiti-attivi.php">Prestiti Attivi</a></li>';
        }
        if($currentPage === 'prestiti-passati'){
            $prestitiPassatiHtml = '<li aria-current="page" class="sidebar-current-page">Prestiti Passati</li>';
        }
        else{
            $prestitiPassatiHtml = '<li class="sidebar-link"><a href="prestiti-passati.php">Prestiti Passati</a></li>';
        }
        if($currentPage === 'recensioni'){
            $recensioniHtml = '<li aria-current="page" class="sidebar-current-page">Recensioni</li>';
        }
        else{
            $recensioniHtml = '<li class="sidebar-link"><a href="recensioni.php">Recensioni</a></li>';
        }
    }

    $breadcrumbHtml = '';
    if (!empty($breadcrumb) && is_array($breadcrumb)) {
        $breadcrumbItems = array();
        $lastIndex = count($breadcrumb) - 1;
        $renderBreadcrumbLabel = function ($crumb) {
            if (!empty($crumb['label_parts']) && is_array($crumb['label_parts'])) {
                $parts = array();
                foreach ($crumb['label_parts'] as $part) {
                    $text = htmlspecialchars((string) ($part['text'] ?? ''), ENT_QUOTES, 'UTF-8');
                    if ($text === '') {
                        continue;
                    }
                    if (!empty($part['lang'])) {
                        $lang = htmlspecialchars((string) $part['lang'], ENT_QUOTES, 'UTF-8');
                        $parts[] = '<span lang="' . $lang . '">' . $text . '</span>';
                    } else {
                        $parts[] = $text;
                    }
                }
                return implode('', $parts);
            }

            return isset($crumb['label']) ? htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') : '';
        };

        foreach ($breadcrumb as $index => $crumb) {
            $label = $renderBreadcrumbLabel($crumb);
            $langAttr = empty($crumb['label_parts']) && isset($crumb['lang']) ? ' lang="' . htmlspecialchars($crumb['lang'], ENT_QUOTES, 'UTF-8') . '"' : '';
            $href = isset($crumb['href']) ? trim($crumb['href']) : '';

            if ($index === $lastIndex || $href === '') {
                $breadcrumbItems[] = '<li><span aria-current="page"' . $langAttr . '>' . $label . '</span></li>';
            } else {
                $breadcrumbItems[] = '<li><a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '"' . $langAttr . '>' . $label . '</a></li>';
            }
        }

        $breadcrumbHtml = '<nav class="breadcrumb" aria-label="Percorso nel sito"><ul>' . implode('', $breadcrumbItems) . '</ul></nav>';
    }

    $replacements = array(
        '[PAGE_TITLE]' => htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'),
        '[PAGE_DESCRIPTION]' => htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'),
        '[PAGE_KEYWORDS]' => htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8'),
        '[NAVIGATION]' => implode('', $navigation),
        '[USERNAME]'          => $usernameHtml,
        '[PRESTITI-ATTIVI]'   => $prestitiAttiviHtml,
        '[PRESTITI-PASSATI]'  => $prestitiPassatiHtml,
        '[RECENSIONI]'        => $recensioniHtml,
    );

}
else{
    $navigation[] = '<li><a href="login.php"><span lang="en">Login</span></a></li>';
    $navigation[] = '<li><a href="register.php">Registrati</a></li>';
    $replacements = array(
        '[PAGE_TITLE]' => '',
        '[PAGE_DESCRIPTION]' => '',
        '[PAGE_KEYWORDS]' => '',
        '[NAVIGATION]' => '',
        '[USERNAME]'          => '',
        '[PRESTITI-ATTIVI]'   => '',
        '[PRESTITI-PASSATI]'  => '',
        '[RECENSIONI]'        => '',
    );
}



echo strtr($template, $replacements);
