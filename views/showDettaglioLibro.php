<article id="dettaglio-libro">
    <h2><?= htmlspecialchars($libro['titolo'], ENT_QUOTES, 'UTF-8') ?></h2>

    <img src="<?= htmlspecialchars(!empty($libro['copertina']) ? $libro['copertina'] : 'images/place-holder.jpg', ENT_QUOTES, 'UTF-8') ?>"
         alt="<?= htmlspecialchars('Copertina del libro ' . $libro['titolo'] . ' di ' . $libro['autore'], ENT_QUOTES, 'UTF-8') ?>">

    <section id="info-libro">
        <h3>Informazioni sul libro</h3>
        <table>
            <caption>Scheda bibliografica</caption>
            <tbody>
                <tr>
                    <th scope="row">Autore</th>
                    <td><?= htmlspecialchars($libro['autore'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Casa editrice</th>
                    <td><?= htmlspecialchars($libro['casa_editrice'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Edizione</th>
                    <td><?= (int) $libro['edizione'] ?></td>
                </tr>
                <tr>
                    <th scope="row">Anno</th>
                    <td><time datetime="<?= (int) $libro['anno'] ?>"><?= (int) $libro['anno'] ?></time></td>
                </tr>
                <tr>
                    <th scope="row">Lingua</th>
                    <td><?= htmlspecialchars($libro['lingua'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Pagine</th>
                    <td><?= (int) $libro['pagine'] ?></td>
                </tr>
                <tr>
                    <th scope="row">Categoria</th>
                    <td><?= htmlspecialchars($libro['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row"><abbr title="International Standard Book Number">ISBN</abbr></th>
                    <td><?= htmlspecialchars($libro['codice_isbn'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Disponibilità</th>
                    <td>
                        <?php if ($disponibile): ?>
                            <strong>&#10003; Disponibile</strong>
                        <?php else: ?>
                            <strong>&#10007; Non disponibile</strong>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ($libro['media_voti'] !== null): ?>
                    <tr>
                        <th scope="row">Valutazione media</th>
                        <td><?= htmlspecialchars((string) $libro['media_voti'], ENT_QUOTES, 'UTF-8') ?> su 5</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <?php if (!empty($libro['descrizione'])): ?>
        <section id="descrizione-libro">
            <h3>Descrizione</h3>
            <p><?= htmlspecialchars($libro['descrizione'], ENT_QUOTES, 'UTF-8') ?></p>
        </section>
    <?php endif; ?>

    <?php if (!empty($tags)): ?>
        <section id="tag-libro">
            <h3>Tag</h3>
            <ul>
                <?php foreach ($tags as $tag): ?>
                    <li>
                        <a href="catalogo.php?cerca=<?= urlencode($tag['nome']) ?>">
                            <?= htmlspecialchars($tag['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <section id="azioni-libro">
        <h3>Azioni</h3>
        <?php if (!$disponibile): ?>
            <p>Questo libro non è al momento disponibile per il prestito.</p>
        <?php elseif ($utenteLoggato): ?>
            <p>Il libro è disponibile per il prestito.</p>
        <?php else: ?>
            <p>Per richiedere il prestito devi aver effettuato l'accesso.</p>
            <a href="login.php?intended=dettaglio-libro.php?id=<?= (int) $libro['id'] ?>">Accedi per richiedere il prestito</a>
        <?php endif; ?>
    </section>
</article>

<section id="recensioni-libro">
    <h3>Recensioni</h3>

    <?php if (!empty($recensioni)): ?>
        <ul>
            <?php foreach ($recensioni as $recensione): ?>
                <li>
                    <article class="recensione-card">
                        <header>
                            <p><strong><?= htmlspecialchars($recensione['username'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                            <p>
                                <time datetime="<?= htmlspecialchars($recensione['data'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($recensione['data'])), ENT_QUOTES, 'UTF-8') ?>
                                </time>
                            </p>
                            <p>Valutazione: <?= (int) $recensione['valutazione'] ?> su 5</p>
                        </header>
                        <?php if (!empty($recensione['testo'])): ?>
                            <p><?= htmlspecialchars($recensione['testo'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nessuna recensione ancora. <a href="login.php">Accedi</a> per essere il primo a recensire questo libro.</p>
    <?php endif; ?>
</section>
