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
		'[USERNAME]' => '',
		'[UTENTE_ID]' => '',
		'[ANNULLA_HREF]' => 'recensioni.php',
	]);
	return;
}

$warningMessage = '';
$username = htmlspecialchars((string) ($recensione['username'] ?? ''), ENT_QUOTES, 'UTF-8');
$utenteId = (int) ($recensione['utente_id'] ?? 0);

echo strtr($template, [
	'[MESSAGES]' => $messages,
	'[NOT_FOUND_MESSAGE]' => '',
	'[FORM_DISPLAY]' => '',
	'[WARNING_MESSAGE]' => $warningMessage,
	'[RECENSIONE_ID]' => htmlspecialchars((string) ($recensione['id'] ?? ''), ENT_QUOTES, 'UTF-8'),
	'[USERNAME]' => $username,
	'[UTENTE_ID]' => htmlspecialchars((string) $utenteId, ENT_QUOTES, 'UTF-8'),
	'[ANNULLA_HREF]' => $utenteId > 0 ? 'recensioni.php?cerca=' . rawurlencode((string) $recensione['username']) : 'recensioni.php',
]);
