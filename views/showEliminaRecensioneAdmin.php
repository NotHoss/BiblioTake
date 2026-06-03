<?php
$errorMsg = '';
if (!empty($errorMessage)) {
	$errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}

$successMsg = '';
if (!empty($successMessage)) {
	$successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}

$messages = $errorMsg . $successMsg;
$template = file_get_contents(__DIR__ . '/../html/admin/showEliminaRecensioneAdmin.html');

if (empty($recensione)) {
	echo strtr($template, [
		'[MESSAGES]' => $messages,
		'[NOT_FOUND_MESSAGE]' => '<p>Recensione non trovata.</p>',
		'[FORM_DISPLAY]' => 'class="none"',
		'[WARNING_MESSAGE]' => '',
		'[RECENSIONE_ID]' => '',
		'[UTENTE]' => '',
		'[UTENTE_ID]' => '',
		'[SUMMARY]' => '',
		'[ANNULLA_HREF]' => 'recensioni.php',
	]);
	return;
}

$warningMessage = '';
$nomeUtente = htmlspecialchars((string) ($recensione['username'] ?? ''), ENT_QUOTES, 'UTF-8');
$utenteId = (int) ($recensione['utente_id'] ?? 0);
$summary = '<ul>'
	. '<li><strong>ID</strong>: ' . htmlspecialchars((string) ($recensione['id'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
	. '<li><strong>Utente</strong>: ' . $nomeUtente . '</li>'
	. '<li><strong>Libro</strong>: ' . htmlspecialchars((string) ($recensione['libro_titolo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
	. '<li><strong>Voto</strong>: ' . htmlspecialchars((string) ($recensione['valutazione'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
	. '<li><strong>Data</strong>: ' . htmlspecialchars((string) ($recensione['data'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
	. '<li><strong>Stato</strong>: ' . (($recensione['censura'] ?? false) ? 'Censurata' : 'Visibile') . '</li>'
	. '<li><strong>Testo</strong>: ' . htmlspecialchars(mb_substr((string) ($recensione['testo'] ?? ''), 0, 120), ENT_QUOTES, 'UTF-8') . '</li>'
	. '</ul>';

echo strtr($template, [
	'[MESSAGES]' => $messages,
	'[NOT_FOUND_MESSAGE]' => '',
	'[FORM_DISPLAY]' => '',
	'[WARNING_MESSAGE]' => $warningMessage,
	'[RECENSIONE_ID]' => htmlspecialchars((string) ($recensione['id'] ?? ''), ENT_QUOTES, 'UTF-8'),
	'[UTENTE]' => $nomeUtente,
	'[UTENTE_ID]' => htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8'),
	'[SUMMARY]' => $summary,
	'[ANNULLA_HREF]' => $utenteId > 0 ? 'recensioni.php?cerca=' . rawurlencode((string) ($recensione['username'] ?? '')) : 'recensioni.php',
]);
