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

$template = file_get_contents(__DIR__ . '/../html/user/showModificaRecensione.html');

$recensioneId = (int) ($recensione['id'] ?? 0);
$libroTitolo  = htmlspecialchars((string) ($recensione['titolo'] ?? ''), ENT_QUOTES, 'UTF-8');
$testo        = htmlspecialchars((string) ($recensione['testo'] ?? ''), ENT_QUOTES, 'UTF-8');
$valutazione  = (int) ($recensione['valutazione'] ?? 0);

//preseleziona l'opzione della valutazione corrente nel select
$selected = array(1 => '', 2 => '', 3 => '', 4 => '', 5 => '');
if (isset($selected[$valutazione])) {
    $selected[$valutazione] = ' selected="selected"';
}

//propaga il flag return nel form (campo hidden) cosi' il redirect post-submit sa dove tornare
$returnValue = (isset($return) && $return === 'libro') ? 'libro' : '';

echo strtr($template, [
    '[MESSAGES]'      => $messages,
    '[RECENSIONE_ID]' => $recensioneId,
    '[RETURN_VALUE]'  => $returnValue,
    '[LIBRO_TITOLO]'  => $libroTitolo,
    '[TESTO]'         => $testo,
    '[SEL1]'          => $selected[1],
    '[SEL2]'          => $selected[2],
    '[SEL3]'          => $selected[3],
    '[SEL4]'          => $selected[4],
    '[SEL5]'          => $selected[5],
]);
