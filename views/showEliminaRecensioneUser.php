<?php
$errorMsg = '';
if(!empty($errorMessage)){
	$errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}

$successMsg = '';
if(!empty($successMessage)){
	$successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}

//$messages = $errorMsg . $successMsg;
$template = file_get_contents(__DIR__ . '/../html/user/showEliminaRecensione.html');

$libroTitolo = htmlspecialchars((string) ($recensione['libro_titolo'] ?? ''), ENT_QUOTES, 'UTF-8');
$recensioneIdHtml = (int) ($recensione['id'] ?? 0);

if(empty($recensione)){
	echo strtr($template, [
		'[MESSAGES]' => $errorMsg,
		'[NOT_FOUND_MESSAGE]' => '<p>Recensione non trovata.</p>',
        '[TITOLO]' => '',
        '[CARD_RECENSIONE]' => '',
        '[RECENSIONE_ID]'     => 0,
	]);
	return;
}



$libroId      = (int) ($recensione['libro_id'] ?? 0);






//$recensioneId = (int) ($recensione['id'] ?? 0);
$titolo = htmlspecialchars((string) ($recensione['titolo'] ?? ''), ENT_QUOTES, 'UTF-8');
$autore = htmlspecialchars((string) ($recensione['autore'] ?? ''), ENT_QUOTES, 'UTF-8');
//$username = htmlspecialchars((string) ($recensione['username'] ?? ''), ENT_QUOTES, 'UTF-8');
$dataAttr = date('Y-m-d\TH:i:s', strtotime($recensione['data']));
$dataHtml = date('d/m/Y', strtotime($recensione['data']));
$valutazione = (int) ($recensione['valutazione'] ?? 0);
$testo = htmlspecialchars((string) ($recensione['testo'] ?? ''), ENT_QUOTES, 'UTF-8');

$cardRecensione = '<article class="recensione-card">';
$cardRecensione .= '<header>';
$cardRecensione .= '<p class="recensione-titolo"><a href="../dettaglio-libro.php?id=' . $libroId . '">' . $titolo . '</a></p>';
$cardRecensione .= '<p>' . $autore . '</p>';
$cardRecensione .= '<p><time datetime="' . $dataAttr . '">' . $dataHtml . '</time></p>';
$cardRecensione .= '<p><strong>Valutazione: </strong>' . $valutazione . '</p>';
$cardRecensione .= '</header>';
$cardRecensione .= '<p>' . $testo . '</p>';
$cardRecensione .= '</article>';

echo strtr($template, [
	'[MESSAGES]' => $errorMsg,
	'[NOT_FOUND_MESSAGE]' => '',
	'[TITOLO]' => $libroTitolo,
	'[RECENSIONE_ID]' => $recensioneIdHtml,
    '[CARD_RECENSIONE]'   => $cardRecensione,
]);