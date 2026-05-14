
    <h1>Dashboard amministrazione</h1>

    <?php if ($errorMessage !== ''): ?>
        <div>
            <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div>
            <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <section>
        <h2>Statistiche</h2>
        <ul>
            <li>Libri totali: <?= htmlspecialchars((string) $stats['libri'], ENT_QUOTES, 'UTF-8') ?></li>
            <li>Utenti totali: <?= htmlspecialchars((string) $stats['utenti'], ENT_QUOTES, 'UTF-8') ?></li>
        </ul>
    </section>

    <section>
        <h2>Generalità della biblioteca</h2>

        <?php if (empty($biblioteca)): ?>
            <p>Non ci sono generalità da mostrare.</p>
        <?php else: ?>
            <?php if (empty($isEditGeneralita)): ?>
                <p>
                    <strong>Indirizzo:</strong>
                    <?= htmlspecialchars((string) $biblioteca['indirizzo'], ENT_QUOTES, 'UTF-8') ?> |
                    <strong>Telefono:</strong>
                    <?= htmlspecialchars((string) $biblioteca['telefono'], ENT_QUOTES, 'UTF-8') ?> |
                    <strong>Email:</strong>
                    <?= htmlspecialchars((string) $biblioteca['email'], ENT_QUOTES, 'UTF-8') ?> |
                    <strong>Lun-Ven:</strong>
                    <?= htmlspecialchars((string) ($biblioteca['orario_lun_ven'] ?? ''), ENT_QUOTES, 'UTF-8') ?> |
                    <strong>Sabato:</strong>
                    <?= htmlspecialchars((string) ($biblioteca['orario_sabato'] ?? ''), ENT_QUOTES, 'UTF-8') ?> |
                    <strong>Domenica:</strong>
                    <?= htmlspecialchars((string) ($biblioteca['orario_domenica'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p><a href="index.php?mode=edit">Modifica</a></p>
            <?php else: ?>
                <form method="post">
                    <input type="hidden" name="azione" value="aggiorna_generalita">
                    <input type="hidden" name="biblioteca_id" value="<?= htmlspecialchars((string) $biblioteca['id'], ENT_QUOTES, 'UTF-8') ?>">

                    <p>
                        <label for="indirizzo">Indirizzo</label><br>
                        <input type="text" id="indirizzo" name="indirizzo" value="<?= htmlspecialchars((string) $biblioteca['indirizzo'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p>
                        <label for="telefono">Telefono</label><br>
                        <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars((string) $biblioteca['telefono'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p>
                        <label for="email">Email</label><br>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars((string) $biblioteca['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p>
                        <label for="orario_lun_ven">Orario Lun-Ven</label><br>
                        <input type="text" id="orario_lun_ven" name="orario_lun_ven" value="<?= htmlspecialchars((string) ($biblioteca['orario_lun_ven'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p>
                        <label for="orario_sabato">Orario Sabato</label><br>
                        <input type="text" id="orario_sabato" name="orario_sabato" value="<?= htmlspecialchars((string) ($biblioteca['orario_sabato'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p>
                        <label for="orario_domenica">Orario Domenica</label><br>
                        <input type="text" id="orario_domenica" name="orario_domenica" value="<?= htmlspecialchars((string) ($biblioteca['orario_domenica'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </p>

                    <p>
                        <button type="submit">Aggiorna generalità</button>
                        <a href="index.php">Annulla</a>
                    </p>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <section>
        <h2>Gestione rapida</h2>
        <ul>
            <li><a href="aggiungi-libro.php">Inserimento nuovi libri</a></li>
            <li><a href="libri.php">Modifica ed eliminazione libri</a></li>
            <li><a href="utenti.php">Visualizzazione utenti registrati</a></li>
            <li><a href="prestiti-utente.php">Controllo prestiti utenti</a></li>
            <li><a href="recensioni.php">Controllo recensioni utenti</a></li>
        </ul>
    </section>


