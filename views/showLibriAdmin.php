<?php
$adminViewMode = $adminViewMode ?? 'list';

$errorMsg = '';
if (!empty($errorMessage)) {
    $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$successMsg = '';
if (!empty($successMessage)) {
    $successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
}
$messages = $errorMsg . $successMsg;

// List mode only — create/edit/delete moved to dedicated views
$template = file_get_contents(__DIR__ . '/../html/admin/showLibri.html');

if (empty($libri)) {
    echo strtr($template, [
        '[MESSAGES]' => $messages,
        '[EMPTY_MESSAGE]' => '<p>Nessun libro trovato.</p>',
        '[TABLE_DISPLAY]' => 'style="display:none;"',
        '[TABLE_ROWS]' => '',
    ]);
    return;
}

$tableRows = '';
foreach ($libri as $libroRow) {
    $id = htmlspecialchars((string) $libroRow['id'], ENT_QUOTES, 'UTF-8');
    $titolo = htmlspecialchars((string) $libroRow['titolo'], ENT_QUOTES, 'UTF-8');
    $autore = htmlspecialchars((string) $libroRow['autore'], ENT_QUOTES, 'UTF-8');
    $anno = htmlspecialchars((string) $libroRow['anno'], ENT_QUOTES, 'UTF-8');
    $categoria = htmlspecialchars((string) $libroRow['categoria'], ENT_QUOTES, 'UTF-8');
    $prestiti = htmlspecialchars((string) $libroRow['prestiti_attivi'], ENT_QUOTES, 'UTF-8');

    $tagsArr = getTagsByLibroId($conn, (int) $libroRow['id']);
    $tagsList = [];
    foreach ($tagsArr as $t) { $tagsList[] = htmlspecialchars((string) $t['nome'], ENT_QUOTES, 'UTF-8'); }
    $tagsHtml = implode(', ', $tagsList);

    $tableRows .= "<tr>
        <td>{$id}</td>
        <td>{$titolo}</td>
        <td>{$autore}</td>
        <td>{$anno}</td>
        <td>{$categoria}</td>
        <td>{$tagsHtml}</td>
        <td>{$prestiti}</td>
        <td>
            <a href=\"modifica-libro.php?id={$id}\">Modifica</a>
            |
            <a href=\"elimina-libro.php?id={$id}\">Elimina</a>
        </td>
    </tr>\n";
}

echo strtr($template, [
    '[MESSAGES]' => $messages,
    '[EMPTY_MESSAGE]' => '',
    '[TABLE_DISPLAY]' => '',
    '[TABLE_ROWS]' => $tableRows,
]);



