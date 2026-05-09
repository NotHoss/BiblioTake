<?php

// Le funzioni di questo file presuppongono che $_SESSION sia gia attiva.
// Avviare la sessione e responsabilita del modello chiamante: deve invocare
// session_start() prima di qualsiasi output e prima di includere resources.php.

function loginUser($conn, $email, $password) {
    $stmt = $conn->prepare(
        'SELECT id, email, username, password, ruolo, attivo
         FROM utente
         WHERE email = ?'
    );
    $stmt->bind_param('s', $email);
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

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

?>
