<section id="hero">
    <h2>Benvenuto in BiblioTake</h2>
    <p>Cerca un libro, consulta il catalogo e richiedi un prestito direttamente online</p>

    <form action="catalogo.php" method="get" id="ricerca-rapida">
        <label for="cerca">Cerca per titolo o autore</label>
        <input type="search" id="cerca" name="cerca"
               placeholder="Es. Il nome della rosa"
               autocomplete="off">
        <button type="submit">Cerca</button>
    </form>
</section>

<section id="categorie">
    <h3>Categorie</h3>
    <?php if (!empty($categorie)): ?>
        <ul>
            <?php foreach ($categorie as $categoria): ?>
                <li>
                    <a href="catalogo.php?categoria=<?= urlencode($categoria['categoria']) ?>">
                        <?= htmlspecialchars($categoria['categoria'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nessuna categoria disponibile</p>
    <?php endif; ?>
</section>

<section id="ultimi-arrivi">
    <h3>Ultimi arrivi</h3>
    <?php if (!empty($libriRecenti)): ?>
        <ul>
            <?php foreach ($libriRecenti as $libro): ?>
                <li>
                    <article>
                        <h4>
                            <a href="dettaglio-libro.php?id=<?= (int) $libro['id'] ?>">
                                <?= htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h4>
                        <p><?= htmlspecialchars($libro['autore'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><?= htmlspecialchars($libro['categoria'], ENT_QUOTES, 'UTF-8') ?></p>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nessun libro disponibile al momento</p>
    <?php endif; ?>
</section>
