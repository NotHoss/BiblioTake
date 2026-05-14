    <?php $adminViewMode = $adminViewMode ?? 'list'; ?>

    <?php if ($adminViewMode === 'create'): ?>
        <h1>Aggiungi libro</h1>
        <p><a href="libri.php">Torna ai libri</a></p>

        <?php if ($errorMessage !== ''): ?>
            <div><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($successMessage !== ''): ?>
            <div><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <p><label>ISBN <input type="text" name="codice_isbn" value="<?= htmlspecialchars($dati['codice_isbn'], ENT_QUOTES, 'UTF-8') ?>" required></label></p>
            <p><label>Titolo <input type="text" name="titolo" value="<?= htmlspecialchars($dati['titolo'], ENT_QUOTES, 'UTF-8') ?>" required></label></p>
            <p><label>Autore <input type="text" name="autore" value="<?= htmlspecialchars($dati['autore'], ENT_QUOTES, 'UTF-8') ?>" required></label></p>
            <p><label>Casa editrice <input type="text" name="casa_editrice" value="<?= htmlspecialchars($dati['casa_editrice'], ENT_QUOTES, 'UTF-8') ?>" required></label></p>
            <p><label>Edizione <input type="number" name="edizione" value="<?= htmlspecialchars($dati['edizione'], ENT_QUOTES, 'UTF-8') ?>"></label></p>
            <p><label>Anno <input type="number" name="anno" value="<?= htmlspecialchars($dati['anno'], ENT_QUOTES, 'UTF-8') ?>" required></label></p>
            <p><label>Lingua <input type="text" name="lingua" value="<?= htmlspecialchars($dati['lingua'], ENT_QUOTES, 'UTF-8') ?>" required></label></p>
            <p><label>Descrizione <textarea name="descrizione" required><?= htmlspecialchars($dati['descrizione'], ENT_QUOTES, 'UTF-8') ?></textarea></label></p>
            <p><label>Pagine <input type="number" name="pagine" value="<?= htmlspecialchars($dati['pagine'], ENT_QUOTES, 'UTF-8') ?>" required></label></p>
            <p><label>Copertina (JPG, max 5MB, facoltativa) <input type="file" name="copertina_file" accept=".jpg,.jpeg,image/jpeg"></label></p>
            <p>
                <label>Categoria
                    <select name="categoria" id="categoria-select" required>
                        <option value="">-- Scegli una categoria --</option>
                        <?php foreach (($categorie ?? []) as $cat): ?>
                            <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>" <?= ($dati['categoria'] === $cat ? 'selected' : '') ?>>
                                <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="__NEW__">+ Aggiungi nuova categoria</option>
                    </select>
                </label>
                <input type="text" name="categoria_nuova" id="categoria-nuova" placeholder="Nome nuova categoria" style="display: none;">
            </p>
            <script>
                document.getElementById('categoria-select').addEventListener('change', function() {
                    const nuovaInput = document.getElementById('categoria-nuova');
                    const selectEl = document.getElementById('categoria-select');
                    if (this.value === '__NEW__') {
                        nuovaInput.style.display = 'inline-block';
                        nuovaInput.required = true;
                    } else {
                        nuovaInput.style.display = 'none';
                        nuovaInput.required = false;
                        nuovaInput.value = '';
                    }
                });
            </script>
            <p><button type="submit">Salva libro</button></p>
        </form>

    <?php elseif ($adminViewMode === 'edit'): ?>
        <h1>Modifica libro</h1>
        <p><a href="libri.php">Torna ai libri</a></p>

        <?php if ($errorMessage !== ''): ?>
            <div><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!$libro): ?>
            <p>Libro non trovato.</p>
        <?php else: ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string) $libroId, ENT_QUOTES, 'UTF-8') ?>">
                <p><label>ISBN <input type="text" name="codice_isbn" value="<?= htmlspecialchars((string) $libro['codice_isbn'], ENT_QUOTES, 'UTF-8') ?>"></label></p>
                <p><label>Titolo <input type="text" name="titolo" value="<?= htmlspecialchars((string) $libro['titolo'], ENT_QUOTES, 'UTF-8') ?>"></label></p>
                <p><label>Autore <input type="text" name="autore" value="<?= htmlspecialchars((string) $libro['autore'], ENT_QUOTES, 'UTF-8') ?>"></label></p>
                <p><label>Casa editrice <input type="text" name="casa_editrice" value="<?= htmlspecialchars((string) ($libro['casa_editrice'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label></p>
                <p><label>Edizione <input type="number" name="edizione" value="<?= htmlspecialchars((string) ($libro['edizione'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label></p>
                <p><label>Anno <input type="number" name="anno" value="<?= htmlspecialchars((string) ($libro['anno'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label></p>
                <p><label>Lingua <input type="text" name="lingua" value="<?= htmlspecialchars((string) ($libro['lingua'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label></p>
                <p><label>Descrizione <textarea name="descrizione"><?= htmlspecialchars((string) ($libro['descrizione'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea></label></p>
                <p><label>Pagine <input type="number" name="pagine" value="<?= htmlspecialchars((string) ($libro['pagine'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label></p>
                <p>
                    <label>Copertina corrente: </label>
                    <?php if (!empty($libro['copertina'])): ?>
                        <span><?= htmlspecialchars((string) $libro['copertina'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php else: ?>
                        <span>---</span>
                    <?php endif; ?>
                </p>
                <?php
                    $canDeleteCover = true;
                    if (!empty($libro['copertina'])) {
                        $canDeleteCover = (basename($libro['copertina']) !== basename(DEFAULT_COVER));
                    }
                ?>
                <p>
                    <label>
                        <input type="checkbox" name="delete_copertina" value="1" <?php if (!$canDeleteCover) echo 'disabled'; ?> <?php if (!$canDeleteCover) echo 'title="Non è possibile eliminare la copertina placeholder"'; ?>>
                        Elimina copertina corrente
                    </label>
                </p>
                <p><label>Carica nuova copertina (JPG, max 5MB) <input type="file" name="copertina_file" accept=".jpg,.jpeg,image/jpeg"></label></p>
                <p>
                    <label>Categoria
                        <select name="categoria" id="categoria-select-edit">
                            <option value="">-- Scegli una categoria --</option>
                            <?php foreach (($categorie ?? []) as $cat): ?>
                                <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>" <?= ($libro['categoria'] === $cat ? 'selected' : '') ?>>
                                    <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="__NEW__">+ Aggiungi nuova categoria</option>
                        </select>
                    </label>
                    <input type="text" name="categoria_nuova" id="categoria-nuova-edit" placeholder="Nome nuova categoria" style="display: none;">
                </p>
                <script>
                    (function(){
                        var sel = document.getElementById('categoria-select-edit');
                        var nuova = document.getElementById('categoria-nuova-edit');
                        if (sel) {
                            sel.addEventListener('change', function(){
                                if (this.value === '__NEW__') { nuova.style.display = 'inline-block'; nuova.required = true; }
                                else { nuova.style.display = 'none'; nuova.required = false; nuova.value = ''; }
                            });
                        }
                    })();
                </script>
                <p><button type="submit">Aggiorna libro</button></p>
            </form>
        <?php endif; ?>

    <?php elseif ($adminViewMode === 'delete'): ?>
        <h1>Elimina libro</h1>
        <p><a href="libri.php">Torna ai libri</a></p>

        <?php if ($errorMessage !== ''): ?>
            <div><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!$libro): ?>
            <p>Libro non trovato.</p>
        <?php else: ?>
            <p>Stai per eliminare: <strong><?= htmlspecialchars((string) $libro['titolo'], ENT_QUOTES, 'UTF-8') ?></strong></p>
            <?php if ($message !== ''): ?><p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <form method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string) $libroId, ENT_QUOTES, 'UTF-8') ?>">
                <p><button type="submit">Conferma eliminazione</button></p>
            </form>
        <?php endif; ?>

    <?php else: ?>
        <h1>Gestione libri</h1>
        <p><a href="aggiungi-libro.php">Inserisci nuovo libro</a></p>

        <?php if ($errorMessage !== ''): ?>
            <div><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (empty($libri)): ?>
            <p>Nessun libro trovato.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titolo</th>
                        <th>Autore</th>
                        <th>Anno</th>
                        <th>Categoria</th>
                        <th>Prestiti attivi</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($libri as $libro): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $libro['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $libro['titolo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $libro['autore'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $libro['anno'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $libro['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $libro['prestiti_attivi'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="modifica-libro.php?id=<?= htmlspecialchars((string) $libro['id'], ENT_QUOTES, 'UTF-8') ?>">Modifica</a>
                                |
                                <a href="elimina-libro.php?id=<?= htmlspecialchars((string) $libro['id'], ENT_QUOTES, 'UTF-8') ?>">Elimina</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>


