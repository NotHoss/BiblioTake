<?php

// Le funzioni di questo file presuppongono che $_SESSION sia gia attiva.
// Avviare la sessione e responsabilita del modello chiamante: deve invocare
// session_start() prima di qualsiasi output e prima di includere resources.php.

//accetta login per email o username (la prof accede con admin/admin)
function loginUser($conn, $login, $password) {
    $stmt = $conn->prepare(
        'SELECT id, email, username, password, ruolo, attivo
         FROM utente
         WHERE email = ? OR username = ?'
    );
    $stmt->bind_param('ss', $login, $login);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user === null) {
        return false;
    }
    if (!$user['attivo']) {
        return false;
    }
    if (!password_verify($password, $user['password'])) {
        return false;
    }

    session_regenerate_id(true);

    $_SESSION['user_id']       = (int) $user['id'];
    $_SESSION['user_email']    = $user['email'];
    $_SESSION['user_username'] = $user['username'];
    $_SESSION['user_role']     = $user['ruolo'];

    return true;
}

function logoutUser() {
    $_SESSION = array();

    // Invalida anche il cookie di sessione lato browser
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function registerUser($conn, $email, $username, $password) {
    // Controllo unicita email
    $stmt = $conn->prepare('SELECT id FROM utente WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exists !== null) {
        return false;
    }

    $hash   = password_hash($password, PASSWORD_BCRYPT);
    $avatar = DEFAULT_AVATAR;
    $ruolo  = 'utente';
    $attivo = 1;

    $stmt = $conn->prepare(
        'INSERT INTO utente (email, username, password, foto_profilo, attivo, ruolo)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('ssssis', $email, $username, $hash, $avatar, $attivo, $ruolo);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

//calcola il path relativo verso la root del progetto, stessa logica di views/template/header.php
//serve per i redirect (login.php, 403.php) emessi da pagine in sottocartelle (admin/, user/):
//un Location relativo da /admin/* punterebbe a /admin/login.php che non esiste
function getAuthBaseUrl() {
    $projectRoot = realpath(__DIR__ . '/../..');
    $scriptDir   = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
    if ($projectRoot !== false && $scriptDir !== false && strpos($scriptDir, $projectRoot) === 0) {
        $rel   = ltrim(substr($scriptDir, strlen($projectRoot)), '/\\');
        $depth = ($rel !== '') ? substr_count(str_replace('\\', '/', $rel), '/') + 1 : 0;
    } else {
        $depth = 0;
    }
    return $depth > 0 ? implode('/', array_fill(0, $depth, '..')) : '.';
}

function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        $intended = urlencode($_SERVER['REQUEST_URI']);
        header('Location: ' . getAuthBaseUrl() . '/login.php?intended=' . $intended);
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        header('Location: ' . getAuthBaseUrl() . '/403.php');
        exit;
    }
}

function getCurrentUser($conn) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $id   = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare(
        'SELECT id, email, username, foto_profilo, ruolo, attivo
         FROM utente
         WHERE id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $user;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

?>
