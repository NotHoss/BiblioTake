<?php
// Il template apre il documento HTML fino a <main id="main-content">.
// Si aspetta dal modello chiamante: $pageTitle, $pageDescription,
// $pageKeywords, $currentPage e (opzionale) $breadcrumb come array di
// elementi con chiavi label e href.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$navLinks = array(
    array('key' => 'home',      'href' => 'index.php',     'label' => 'Home',      'lang' => 'en'),
    array('key' => 'catalogo',  'href' => 'catalogo.php',  'label' => 'Catalogo'),
    array('key' => 'about',     'href' => 'about.php',     'label' => 'Chi siamo'),
    array('key' => 'contatti',  'href' => 'contatti.php',  'label' => 'Contatti'),
);
?>
<!DOCTYPE html>
<html lang="it" xml:lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(isset($pageTitle) ? $pageTitle : SITE_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars(isset($pageDescription) ? $pageDescription : SITE_DESCRIPTION, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="keywords"    content="<?= htmlspecialchars(isset($pageKeywords)    ? $pageKeywords    : '',                ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>

<a href="#main-content" class="skip-link">Vai al contenuto</a>

<header>
    <div id="logo">
        <h1>
            <?php if (isset($currentPage) && $currentPage === 'home'): ?>
                <?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?>
            <?php else: ?>
                <a href="index.php"><?= htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') ?></a>
            <?php endif; ?>
        </h1>
    </div>

    <nav aria-label="Navigazione principale">
        <ul>
            <?php foreach ($navLinks as $link):
                $isCurrent = (isset($currentPage) && $currentPage === $link['key']);
                $langAttr  = isset($link['lang']) ? ' lang="' . $link['lang'] . '"' : '';
            ?>
                <?php if ($isCurrent): ?>
                    <li aria-current="page" class="current-page">
                        <span<?= $langAttr ?>><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?= htmlspecialchars($link['href'], ENT_QUOTES, 'UTF-8') ?>"<?= $langAttr ?>><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <?php if (isset($currentPage) && $currentPage === 'admin'): ?>
                        <li aria-current="page" class="current-page">Amministrazione</li>
                    <?php else: ?>
                        <li><a href="admin/index.php">Amministrazione</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (isset($currentPage) && $currentPage === 'dashboard'): ?>
                        <li aria-current="page" class="current-page">Area utente</li>
                    <?php else: ?>
                        <li><a href="dashboard.php">Area utente</a></li>
                    <?php endif; ?>
                <?php endif; ?>

                <li><a href="<?= htmlspecialchars(WEB_ROOT . 'logout.php', ENT_QUOTES, 'UTF-8') ?>">Esci</a></li>
            <?php else: ?>
                <?php if (isset($currentPage) && $currentPage === 'login'): ?>
                    <li aria-current="page" class="current-page">Accedi</li>
                <?php else: ?>
                    <li><a href="login.php">Accedi</a></li>
                <?php endif; ?>

                <?php if (isset($currentPage) && $currentPage === 'register'): ?>
                    <li aria-current="page" class="current-page">Registrati</li>
                <?php else: ?>
                    <li><a href="register.php">Registrati</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<?php if (!empty($breadcrumb) && is_array($breadcrumb)): ?>
<nav class="breadcrumb" aria-label="Percorso nel sito">
    <ul>
        <?php
        $lastIndex = count($breadcrumb) - 1;
        foreach ($breadcrumb as $i => $crumb):
            $isLast = ($i === $lastIndex);
        ?>
            <li>
                <?php if ($isLast || empty($crumb['href'])): ?>
                    <span aria-current="page"><?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($crumb['href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php endif; ?>

<main id="main-content">
