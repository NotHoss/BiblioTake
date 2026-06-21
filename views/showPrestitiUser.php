<?php
//$adminViewMode = $adminViewMode ?? 'list';
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

$template = file_get_contents(__DIR__ . '/../html/user/showPrestiti.html');
    
if (empty($prestiti)) {
    echo strtr($template, [
        '[TITLE]' => $prestitiTitle,
        '[MESSAGES]' => $messages,
        '[EMPTY_MESSAGE]' => '<p class="empty-result">Nessun prestito trovato.</p>',
        '[TABLE_DISPLAY]' => 'style="display:none;"',
        '[TABLE_ROWS]' => '',
        '[PAGINATION]'     => '',
    ]);
    return;
}

$tableRows = '';
foreach($prestiti as $prestitoRow){
    $titolo = htmlspecialchars((string) $prestitoRow['titolo'], ENT_QUOTES, 'UTF-8');
    $autore = htmlspecialchars((string) $prestitoRow['autore'], ENT_QUOTES, 'UTF-8');
    $anno = htmlspecialchars((string) $prestitoRow['anno'], ENT_QUOTES, 'UTF-8');
    $categoria = htmlspecialchars((string) $prestitoRow['categoria'], ENT_QUOTES, 'UTF-8');
    $data_inizio = formatDateForAdminDisplay($prestitoRow['data_inizio']);
    $data_fine = formatDateForAdminDisplay($prestitoRow['data_fine']);
    
    $statoRaw = (string) $prestitoRow['stato'];
    $statoLabel = match($statoRaw) {
        'attivo'    => 'Attivo',
        'in_ritardo'=> 'In ritardo',
        'concluso'  => 'Concluso',
        default     => ucfirst($statoRaw),
    };
    $stato = htmlspecialchars($statoLabel, ENT_QUOTES, 'UTF-8');


    $tableRows .= "<tr>
        <td data-label=\"Titolo\">{$titolo}</td>
        <td data-label=\"Autore\">{$autore}</td>
        <td data-label=\"Anno\">{$anno}</td>
        <td data-label=\"Categoria\">{$categoria}</td>
        <td data-label=\"Inizio Prestito\">{$data_inizio}</td>
        <td data-label=\"Fine Prestito\">{$data_fine}</td>
        <td data-label=\"Stato\">{$stato}</td>
    </tr>\n";
}

$pagination = renderPagination(basename($_SERVER['SCRIPT_NAME']), [], $pagina, $totalPagine, 'Navigazione pagine risultati', 'Pagina precedente', 'Pagina successiva');


echo strtr($template, [
    '[TITLE]' => $prestitiTitle,
    '[MESSAGES]' => $messages,
    '[EMPTY_MESSAGE]' => '',
    '[TABLE_DISPLAY]' => '',
    '[TABLE_ROWS]' => $tableRows,
    '[PAGINATION]' => $pagination,
]);
