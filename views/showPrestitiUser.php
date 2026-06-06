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
    $data_inizio = htmlspecialchars((string) $prestitoRow['data_inizio'], ENT_QUOTES, 'UTF-8');
    $data_fine = htmlspecialchars((string) $prestitoRow['data_fine'], ENT_QUOTES, 'UTF-8');
    $stato = htmlspecialchars((string) $prestitoRow['stato'], ENT_QUOTES, 'UTF-8');

    $tableRows .= "<tr>
        <td>{$titolo}</td>
        <td>{$autore}</td>
        <td>{$anno}</td>
        <td>{$categoria}</td>
        <td>{$data_inizio}</td>
        <td>{$data_fine}</td>
        <td>{$stato}</td>
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
