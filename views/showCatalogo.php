<h2>Catalogo libri</h2>

<aside id="filtri-catalogo">
    <h3>Filtra i risultati</h3>
    <form action="catalogo.php" method="get" id="filtri-form">

        <div>
            <label for="cerca">Ricerca</label>
            <input type="search" id="cerca" name="cerca"
                   value="<?= htmlspecialchars($filtri['cerca'], ENT_QUOTES, 'UTF-8') ?>"
                   autocomplete="off">
        </div>

        <div>
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria">
                <option value="">Tutte le categorie</option>
                <?php foreach ($categorie as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['categoria'], ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($filtri['categoria'] === $cat['categoria']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['categoria'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="autore">Autore</label>
            <input type="text" id="autore" name="autore"
                   value="<?= htmlspecialchars($filtri['autore'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div>
            <label for="anno">Anno</label>
            <input type="number" id="anno" name="anno"
                   min="1900" max="<?= date('Y') ?>"
                   value="<?= $filtri['anno'] !== '' ? (int) $filtri['anno'] : '' ?>">
        </div>

        <div>
            <label for="disponibile">
                <input type="checkbox" id="disponibile" name="disponibile" value="1"
                    <?= ($filtri['disponibile'] === '1') ? 'checked' : '' ?>>
                Solo disponibili
            </label>
        </div>

        <div>
            <label for="ordine">Ordina per</label>
            <select id="ordine" name="ordine">
                <option value="">Più recenti</option>
                <option value="valutazione" <?= ($filtri['ordine'] === 'valutazione') ? 'selected' : '' ?>>
                    Valutazione
                </option>
            </select>
        </div>

        <button type="submit">Applica filtri</button>
        <a href="catalogo.php">Azzera filtri</a>

    </form>
</aside>

<section id="risultati-catalogo">
    <h3>
        <?php if ($totalLibri === 0): ?>
            Nessun risultato trovato
        <?php elseif ($totalLibri === 1): ?>
            1 libro trovato
        <?php else: ?>
            <?= $totalLibri ?> libri trovati
        <?php endif; ?>
    </h3>

    <?php if (!empty($libri)): ?>
        <ul id="lista-libri">
            <?php foreach ($libri as $libro): ?>
                <li>
                    <article class="libro-card">
                        <h4>
                            <a href="dettaglio-libro.php?id=<?= (int) $libro['id'] ?>">
                                <?= htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h4>
                        <p><strong>Autore:</strong> <?= htmlspecialchars($libro['autore'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Categoria:</strong> <?= htmlspecialchars($libro['categoria'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Anno:</strong> <time datetime="<?= (int) $libro['anno'] ?>"><?= (int) $libro['anno'] ?></time></p>
                        <?php if ($libro['media_voti'] !== null): ?>
                            <p><strong>Valutazione:</strong> <?= htmlspecialchars((string) $libro['media_voti'], ENT_QUOTES, 'UTF-8') ?> su 5</p>
                        <?php endif; ?>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($totalPagine > 1): ?>
            <nav aria-label="Navigazione pagine risultati">
                <ul>
                    <?php if ($pagina > 1): ?>
                        <li>
                            <a href="catalogo.php?<?= http_build_query(array_merge($filtri, ['page' => $pagina - 1])) ?>">
                                Pagina precedente
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPagine; $i++): ?>
                        <li <?= ($i === $pagina) ? 'aria-current="page"' : '' ?>>
                            <?php if ($i === $pagina): ?>
                                <?= $i ?>
                            <?php else: ?>
                                <a href="catalogo.php?<?= http_build_query(array_merge($filtri, ['page' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagina < $totalPagine): ?>
                        <li>
                            <a href="catalogo.php?<?= http_build_query(array_merge($filtri, ['page' => $pagina + 1])) ?>">
                                Pagina successiva
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <p>Nessun libro corrisponde ai criteri di ricerca. <a href="catalogo.php">Mostra tutti i libri</a>.</p>
    <?php endif; ?>
</section>
