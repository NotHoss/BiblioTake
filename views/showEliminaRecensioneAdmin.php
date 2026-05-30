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
		'[FORM_DISPLAY]' => 'style="display:none;"',
		'[WARNING_MESSAGE]' => '',
		'[RECENSIONE_ID]' => '',
		'[UTENTE_ID]' => htmlspecialchars((string) ($utenteId ?? 0), ENT_QUOTES, 'UTF-8'),
	]);
	return;
}

$warningMessage = '';

echo strtr($template, [
	'[MESSAGES]' => $messages,
	'[NOT_FOUND_MESSAGE]' => '',
	'[FORM_DISPLAY]' => '',
	'[WARNING_MESSAGE]' => $warningMessage,
	'[RECENSIONE_ID]' => htmlspecialchars((string) ($recensione['id'] ?? ''), ENT_QUOTES, 'UTF-8'),
	'[UTENTE_ID]' => htmlspecialchars((string) ($utenteId ?? 0), ENT_QUOTES, 'UTF-8'),
]);
