<div class="auth-wrapper">
<h2>Registrati</h2>

<?php if (!empty($errors)): ?>
    <div role="alert" class="auth-errors">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="form" action="register.php" method="post">
    <p>
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               value="<?= htmlspecialchars($emailValore, ENT_QUOTES, 'UTF-8') ?>" required>
    </p>
    <p>
        <label for="username">Username</label>
        <input type="text" id="username" name="username"
               value="<?= htmlspecialchars($usernameValore, ENT_QUOTES, 'UTF-8') ?>" required>
    </p>
    <p>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </p>
    <p>
        <label for="password-conferma">Conferma password</label>
        <input type="password" id="password-conferma" name="password-conferma" required>
    </p>
    <p>
        <button type="submit">Registrati</button>
    </p>
</form>

<p><a href="login.php">Hai gia un account? Accedi</a></p>
</div>
