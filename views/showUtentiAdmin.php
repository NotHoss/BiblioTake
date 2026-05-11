<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <?php $adminViewMode = $adminViewMode ?? 'list'; ?>

    <?php if ($adminViewMode === 'prestiti'): ?>
        <h1>Prestiti utente — TEST</h1>
        <p><a href="utenti.php">Torna agli utenti</a></p>

        <?php if ($errorMessage !== ''): ?>
            <div><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($message !== ''): ?><p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <form method="get">
            <p>
                <label>Utente
                    <select name="utente_id">
                        <option value="0">Seleziona utente</option>
                        <?php foreach ($utenti as $utente): ?>
                            <option value="<?= htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $utenteId === (int) $utente['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
            <p><button type="submit">Visualizza prestiti</button></p>
        </form>

        <?php if ($utenteId > 0): ?>
            <?php if (!empty($prestitoInModifica)): ?>
                <h2>Modifica prestito #<?= htmlspecialchars((string) $prestitoInModifica['id'], ENT_QUOTES, 'UTF-8') ?></h2>
                <form method="post">
                    <input type="hidden" name="azione" value="salva_modifica">
                    <input type="hidden" name="prestito_id" value="<?= htmlspecialchars((string) $prestitoInModifica['id'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="utente_id" value="<?= htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8') ?>">

                    <p>
                        <label>Data inizio
                            <input
                                type="datetime-local"
                                name="data_inizio"
                                value="<?= htmlspecialchars(date('Y-m-d\\TH:i', strtotime((string) $prestitoInModifica['data_inizio'])), ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </label>
                    </p>
                    <p>
                        <label>Data fine
                            <input
                                type="datetime-local"
                                name="data_fine"
                                value="<?= htmlspecialchars(date('Y-m-d\\TH:i', strtotime((string) $prestitoInModifica['data_fine'])), ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </label>
                    </p>
                    <p>
                        <label>Stato
                            <select name="stato" required>
                                <?php $statiPrestito = ['attivo', 'in_ritardo', 'concluso']; ?>
                                <?php foreach ($statiPrestito as $stato): ?>
                                    <option value="<?= htmlspecialchars($stato, ENT_QUOTES, 'UTF-8') ?>" <?= $prestitoInModifica['stato'] === $stato ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($stato, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </p>
                    <p>
                        <label>Libro ID
                            <input type="number" name="libro_id" min="1" value="<?= htmlspecialchars((string) $prestitoInModifica['libro_id'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </label>
                    </p>
                    <p>
                        <label>Utente ID
                            <input type="number" name="nuovo_utente_id" min="1" value="<?= htmlspecialchars((string) $prestitoInModifica['utente_id'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </label>
                    </p>
                    <p><button type="submit">Aggiorna prestito</button></p>
                </form>
                <hr>
            <?php endif; ?>

            <?php if (empty($prestiti)): ?>
                <p>Nessun prestito trovato.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Libro</th>
                            <th>Stato</th>
                            <th>Inizio</th>
                            <th>Fine</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestiti as $prestito): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $prestito['libro_titolo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $prestito['stato'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $prestito['data_inizio'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $prestito['data_fine'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php $statoPrestito = (string) $prestito['stato']; ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="prestito_id" value="<?= htmlspecialchars((string) $prestito['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="utente_id" value="<?= htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8') ?>">
                                        <?php if ($statoPrestito === 'attivo'): ?>
                                            <button type="submit" name="azione" value="modifica">Modifica</button>
                                            <button type="submit" name="azione" value="elimina" onclick="return confirm('Eliminare questo prestito?');">Elimina</button>
                                            <button type="submit" name="azione" value="concludi">Concludi</button>
                                            <button type="submit" name="azione" value="proroga">Proroga</button>
                                        <?php elseif ($statoPrestito === 'in_ritardo'): ?>
                                            <button type="submit" name="azione" value="modifica">Modifica</button>
                                            <button type="submit" name="azione" value="elimina" onclick="return confirm('Eliminare questo prestito?');">Elimina</button>
                                            <button type="submit" name="azione" value="concludi">Concludi</button>
                                        <?php else: ?>
                                            <button type="submit" name="azione" value="modifica">Modifica</button>
                                            <button type="submit" name="azione" value="elimina" onclick="return confirm('Eliminare questo prestito?');">Elimina</button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

    <?php elseif ($adminViewMode === 'reviews'): ?>
        <h1>Recensioni utenti — TEST</h1>
        <p><a href="utenti.php">Torna agli utenti</a></p>

        <?php if ($errorMessage !== ''): ?>
            <div><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($message !== ''): ?><p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <form method="get">
            <p>
                <label>Utente
                    <select name="utente_id">
                        <option value="0">Tutti gli utenti</option>
                        <?php foreach ($utenti as $utente): ?>
                            <option value="<?= htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $utenteId === (int) $utente['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
            <p><button type="submit">Filtra recensioni</button></p>
        </form>

        <?php if (empty($recensioni)): ?>
            <p>Nessuna recensione trovata.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utente</th>
                        <th>Libro</th>
                        <th>Voto</th>
                        <th>Testo</th>
                        <th>Data</th>
                        <th>Censurata</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recensioni as $recensione): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $recensione['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $recensione['username'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $recensione['libro_titolo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $recensione['valutazione'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) mb_substr($recensione['testo'], 0, 80), ENT_QUOTES, 'UTF-8') ?>...</td>
                            <td><?= htmlspecialchars((string) $recensione['data'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $recensione['censura'] ? 'Sì' : 'No' ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="recensione_id" value="<?= htmlspecialchars((string) $recensione['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="utente_id" value="<?= htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" name="azione" value="censura"><?= $recensione['censura'] ? 'Mostra' : 'Censura' ?></button>
                                    <button type="submit" name="azione" value="elimina">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php else: ?>
        <h1>Gestione Utenti — TEST</h1>

        <?php if ($errorMessage !== ''): ?>
            <div><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (empty($utenti)): ?>
            <p>Nessun utente trovato.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Ruolo</th>
                        <th>Attivo</th>
                        <th>Prestiti totali</th>
                        <th>Prestiti attivi</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utenti as $utente): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php
                                    $rawPath = (string) ($utente['foto_profilo'] ?? '');
                                    $fotoSrc = '';
                                    if ($rawPath === '') {
                                        $fotoSrc = '/BiblioTake/images/place-holder.jpg';
                                    } elseif (preg_match('#^(https?://|/)#i', $rawPath)) {
                                        $fotoSrc = $rawPath;
                                    } else {
                                        $fotoSrc = '/BiblioTake/' . ltrim($rawPath, '/');
                                    }
                                ?>
                                <img src="<?= htmlspecialchars($fotoSrc, ENT_QUOTES, 'UTF-8') ?>" alt="Foto profilo di <?= htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8') ?>" width="48" height="48">
                            </td>
                            <td><?= htmlspecialchars((string) $utente['username'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $utente['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $utente['ruolo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= ((int) $utente['attivo'] === 1) ? 'Sì' : 'No' ?></td>
                            <td><?= htmlspecialchars((string) $utente['prestiti_totali'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $utente['prestiti_attivi'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="prestiti-utente.php?utente_id=<?= htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8') ?>">Prestiti</a>
                                |
                                <a href="recensioni.php?utente_id=<?= htmlspecialchars((string) $utente['id'], ENT_QUOTES, 'UTF-8') ?>">Recensioni</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
