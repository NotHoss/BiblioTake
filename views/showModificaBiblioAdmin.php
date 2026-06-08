<?php
$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = renderAdminStatusMessage($errorMessage, 'error');
}

$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = renderAdminStatusMessage($successMessage, 'success');
}

$template = file_get_contents(__DIR__ . '/../html/admin/showModificaBiblioAdmin.html');
$returnUrl = getSafeAdminReturnUrl('index.php');
$returnUrlSafe = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');

if (empty($biblioteca)) {
    echo strtr($template, [
        '[ERROR_MESSAGE]' => $errorMsg,
        '[SUCCESS_MESSAGE]' => $successMsg,
        '[BIBLIOTECA_ID]' => '',
        '[INDIRIZZO]' => '',
        '[TELEFONO]' => '',
        '[EMAIL]' => '',
        '[NOTE]' => '',
        '[ORARIO_LUN_VEN]' => '',
        '[ORARIO_SABATO]' => '',
        '[ORARIO_DOMENICA]' => '',
        '[ANNULLA_HREF]' => $returnUrlSafe,
    ]);
    return;
}

echo strtr($template, [
    '[ERROR_MESSAGE]' => $errorMsg,
    '[SUCCESS_MESSAGE]' => $successMsg,
    '[BIBLIOTECA_ID]' => htmlspecialchars((string) $biblioteca['id'], ENT_QUOTES, 'UTF-8'),
    '[INDIRIZZO]' => htmlspecialchars((string) $biblioteca['indirizzo'], ENT_QUOTES, 'UTF-8'),
    '[TELEFONO]' => htmlspecialchars((string) $biblioteca['telefono'], ENT_QUOTES, 'UTF-8'),
    '[EMAIL]' => htmlspecialchars((string) $biblioteca['email'], ENT_QUOTES, 'UTF-8'),
    '[NOTE]' => htmlspecialchars((string) ($biblioteca['note'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '[ORARIO_LUN_VEN]' => htmlspecialchars((string) ($biblioteca['orario_lun_ven'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '[ORARIO_SABATO]' => htmlspecialchars((string) ($biblioteca['orario_sabato'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '[ORARIO_DOMENICA]' => htmlspecialchars((string) ($biblioteca['orario_domenica'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '[ANNULLA_HREF]' => $returnUrlSafe,
]);
