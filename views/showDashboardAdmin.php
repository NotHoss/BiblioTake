
    <?php
// Genera il contenuto della sezione "Generalità della biblioteca"
$bibliotecaContent = '';
if (empty($biblioteca)) {
    $bibliotecaContent = '<p>Non ci sono generalità da mostrare.</p>';
} else {
    if (!$isEditGeneralita) {
        // Vista lettura
        $bibliotecaContent = '<p>' .
            '<strong>Indirizzo:</strong> ' . htmlspecialchars($biblioteca['indirizzo'], ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Telefono:</strong> ' . htmlspecialchars($biblioteca['telefono'], ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Email:</strong> ' . htmlspecialchars($biblioteca['email'], ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Lun-Ven:</strong> ' . htmlspecialchars($biblioteca['orario_lun_ven'] ?? '', ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Sabato:</strong> ' . htmlspecialchars($biblioteca['orario_sabato'] ?? '', ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Domenica:</strong> ' . htmlspecialchars($biblioteca['orario_domenica'] ?? '', ENT_QUOTES, 'UTF-8') .
            '</p>' .
            '<p><a href="index.php?mode=edit">Modifica</a></p>';
    } else {
        // Vista modifica (form)
        ob_start();
        ?>
<form method="post">
    <input type="hidden" name="azione" value="aggiorna_generalita">
    <input type="hidden" name="biblioteca_id" value="<?= htmlspecialchars($biblioteca['id'], ENT_QUOTES, 'UTF-8') ?>">

    <p>
        <label for="indirizzo">Indirizzo</label><br>
        <input type="text" id="indirizzo" name="indirizzo" value="<?= htmlspecialchars($biblioteca['indirizzo'], ENT_QUOTES, 'UTF-8') ?>" required>
    </p>

    <p>
        <label for="telefono">Telefono</label><br>
        <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($biblioteca['telefono'], ENT_QUOTES, 'UTF-8') ?>" required>
    </p>

    <p>
        <label for="email">Email</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($biblioteca['email'], ENT_QUOTES, 'UTF-8') ?>" required>
    </p>

    <p>
        <label for="orario_lun_ven">Orario Lun-Ven</label><br>
        <input type="text" id="orario_lun_ven" name="orario_lun_ven" value="<?= htmlspecialchars($biblioteca['orario_lun_ven'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </p>

    <p>
        <label for="orario_sabato">Orario Sabato</label><br>
        <input type="text" id="orario_sabato" name="orario_sabato" value="<?= htmlspecialchars($biblioteca['orario_sabato'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </p>

    <p>
        <label for="orario_domenica">Orario Domenica</label><br>
        <input type="text" id="orario_domenica" name="orario_domenica" value="<?= htmlspecialchars($biblioteca['orario_domenica'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </p>

    <p>
        <button type="submit">Aggiorna generalità</button>
        <a href="index.php">Annulla</a>
    </p>
</form>
        <?php
        $bibliotecaContent = ob_get_clean();
    }
}

// Genera messaggi di errore e successo
$errorMsg = '';
if ($errorMessage !== '') {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}

$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}

// Carica il template HTML e sostituisce i placeholder
$template = file_get_contents(__DIR__ . '/../html/admin/index.html');
echo strtr($template, [
    '[ERROR_MESSAGE]' => $errorMsg,
    '[SUCCESS_MESSAGE]' => $successMsg,
    '[LIBRI_TOTALI]' => htmlspecialchars((string) $stats['libri'], ENT_QUOTES, 'UTF-8'),
    '[UTENTI_TOTALI]' => htmlspecialchars((string) $stats['utenti'], ENT_QUOTES, 'UTF-8'),
    '[BIBLIOTECA_CONTENT]' => $bibliotecaContent,
]);



