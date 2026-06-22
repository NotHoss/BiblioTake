<?php
$errorMsg = '';
if (!empty($errorMessage)) {
	$errorMsg = renderAdminStatusMessage($errorMessage, 'error');
}

$successMsg = '';
if (!empty($successMessage)) {
	$successMsg = renderAdminStatusMessage($successMessage, 'success');
}

$messages = $errorMsg . $successMsg;
$template = file_get_contents(__DIR__ . '/../html/admin/showEliminaRecensioneAdmin.html');
$returnUrl = getSafeAdminReturnUrl('recensioni.php');
$returnUrlSafe = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');

if (empty($recensione)) {
	echo strtr($template, [
		'[MESSAGES]' => $messages,
		'[NOT_FOUND_MESSAGE]' => renderAdminStatusMessage('Recensione non trovata.', 'error'),
		'[FORM_DISPLAY]' => 'class="none"',
		'[WARNING_MESSAGE]' => '',
		'[RECENSIONE_ID]' => '',
		'[UTENTE]' => '',
		'[UTENTE_ID]' => '',
		'[SUMMARY]' => '',
		'[ANNULLA_HREF]' => $returnUrlSafe,
		'[RETURN_URL]' => $returnUrlSafe,
	]);
	return;
}

$warningMessage = '';
$nomeUtente = htmlspecialchars((string) ($recensione['username'] ?? ''), ENT_QUOTES, 'UTF-8');
$utenteId = (int) ($recensione['utente_id'] ?? 0);
$summary = '<ul class="admin-summary-list">'
	. '<li><strong>ID</strong>: ' . htmlspecialchars((string) ($recensione['id'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
	. '<li><strong>Utente</strong>: ' . $nomeUtente . '</li>'
	. '<li><strong>Libro</strong>: ' . htmlspecialchars((string) ($recensione['libro_titolo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
	. '<li><strong>Voto</strong>: ' . htmlspecialchars((string) ($recensione['valutazione'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
	. '<li><strong>Data</strong>: ' . formatDateDisplay($recensione['data'] ?? '') . '</li>'
	. '<li><strong>Stato</strong>: ' . (($recensione['censura'] ?? false) ? 'Censurata' : 'Visibile') . '</li>'
	. '<li><strong>Testo</strong>: ' . htmlspecialchars((string) ($recensione['testo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</li>'
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
	'[ANNULLA_HREF]' => $returnUrlSafe,
	'[RETURN_URL]' => $returnUrlSafe,
]);
