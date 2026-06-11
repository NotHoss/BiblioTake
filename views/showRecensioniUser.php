<?php
$pagina = $pagina ?? 1;
$totalPagine = $totalPagine ?? 1;

$errorMsg = '';
if ($errorMessage !== '') {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$messages = $errorMsg . $successMsg;

$user = null;
if (isset($_SESSION['user_id'])) {
    $user = getUserInfo($conn, $_SESSION['user_id']);
}

$template = file_get_contents(__DIR__ . '/../html/user/showRecensioni.html');

if (empty($recensioni)) {
    echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[EMPTY_MESSAGE]' => '<p class="empty-result">Nessuna recensione trovata.</p>',
    '[LISTA_RECENSIONI]' => '',
    '[PAGINATION]' => '',
    ]);
    return;
}


$listaCard = '';
foreach ($recensioni as $rec) {

// r.id, l.titolo, l.autore, l.id, r.valutazione, r.testo, r.data

    $id = htmlspecialchars((string) $rec['recensione_id'], ENT_QUOTES, 'UTF-8');
    $titolo = htmlspecialchars((string) $rec['titolo'], ENT_QUOTES, 'UTF-8');
    $autore = htmlspecialchars((string) $rec['autore'], ENT_QUOTES, 'UTF-8');
    $libroId = htmlspecialchars((string) $rec['libro_id'], ENT_QUOTES, 'UTF-8');
    $valutazione = (int) $rec['valutazione'];
    $testo = htmlspecialchars((string) $rec['testo'], ENT_QUOTES, 'UTF-8');
    $data = htmlspecialchars((string) $rec['data'], ENT_QUOTES, 'UTF-8');

    $dataHtml = date('d/m/Y', strtotime($rec['data']));
    $dataAttr = date('Y-m-d', strtotime($rec['data']));

    $listaCard .= '<li>';
    $listaCard .= '<article class="recensione-card">';
    $listaCard .= '<header>';
    $listaCard .= '<p class="recensione-titolo"><a href="../dettaglio-libro.php?id=' . $libroId . '">' . $titolo . '</a></p>';
    $listaCard .= '<p>' . $autore . '</p>';
    $listaCard .= '<p><time datetime="' . $dataAttr . '">' . $dataHtml . '</time></p>';
    $listaCard .= '<p><strong>Valutazione: </strong>' . $valutazione . '</p>';
    $listaCard .= '<p class="elimina-card"><a href="../user/elimina-recensione.php?id=' . $id . '">Elimina</a></p>';
    $listaCard .= '</header>';
    $listaCard .= '<p>' . $testo . '</p>';
    $listaCard .= '</article>';
    $listaCard .= '</li>';
}
$listaCard .= '';

$pagination = renderPagination(basename($_SERVER['SCRIPT_NAME']), [], $pagina, $totalPagine, 'Navigazione pagine recensioni', 'Pagina precedente', 'Pagina successiva');

echo strtr($template, [
    '[MESSAGES]'          => $messages,
    '[EMPTY_MESSAGE]'     => '',
    '[LISTA_RECENSIONI]'  => $listaCard,
    '[PAGINATION]'        => $pagination,
]);