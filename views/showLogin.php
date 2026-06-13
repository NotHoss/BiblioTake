<div class="auth-wrapper">
<h2>Accedi</h2>

<?php if (!empty($errors)): ?>
    <div role="alert" class="auth-errors">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
$actionUrl = 'login.php';
// The controller may provide an $intended variable; views should not read superglobals.
if (!empty($intended)) {
    $actionUrl .= '?intended=' . urlencode($intended);
}
?>

<form class="form" action="<?= htmlspecialchars($actionUrl, ENT_QUOTES, 'UTF-8') ?>" method="post">
    <p>
        <label for="login">Email</label>
        <input type="text" id="login" name="login"
               value="<?= htmlspecialchars($loginValore, ENT_QUOTES, 'UTF-8') ?>" required>
    </p>
    <p>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
    </p>
    <p>
        <button type="submit">Accedi</button>
    </p>
</form>

<p><a href="register.php">Non hai un account? Registrati</a></p>
</div>
