<article id="contatti">
    <?php
    $indirizzo = (string) ($biblioteca['indirizzo'] ?? 'Via Garibaldi 12, Padova');
    $telefono = (string) ($biblioteca['telefono'] ?? '+39 02 88997766');
    $email = (string) ($biblioteca['email'] ?? 'contatti@bibliotake-padova.it');
    $orarioLunVen = (string) ($biblioteca['orario_lun_ven'] ?? '9:00 - 19:00');
    $orarioSabato = (string) ($biblioteca['orario_sabato'] ?? '9:00 - 13:00');
    $orarioDomenica = (string) ($biblioteca['orario_domenica'] ?? 'Chiuso');
    $telefonoHref = 'tel:' . preg_replace('/[^\d\+]/', '', $telefono);
    ?>
    <h2>Contatti</h2>

    <section id="info-contatti">
        <h3>Informazioni di contatto</h3>
        <table>
            <caption>Recapiti della biblioteca</caption>
            <tbody>
                <tr>
                    <th scope="row">Indirizzo</th>
                    <td><?= htmlspecialchars($indirizzo, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Telefono</th>
                    <td><a href="<?= htmlspecialchars($telefonoHref, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') ?></a></td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td><a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></a></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section id="orari">
        <h3>Orari di apertura</h3>
        <table>
            <caption>Orari settimanali della biblioteca</caption>
            <thead>
                <tr>
                    <th scope="col">Giorno</th>
                    <th scope="col">Orario</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Lunedì – Venerdì</td>
                    <td><?= htmlspecialchars($orarioLunVen, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <td>Sabato</td>
                    <td><?= htmlspecialchars($orarioSabato, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <td>Domenica</td>
                    <td><?= htmlspecialchars($orarioDomenica, ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            </tbody>
        </table>
    </section>
</article>
