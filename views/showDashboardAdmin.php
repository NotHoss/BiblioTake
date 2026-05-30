
    <?php
    $bibliotecaContent = '';
    if (empty($biblioteca)) {
        $bibliotecaContent = '<p>Non ci sono generalità da mostrare.</p>';
    } else {
        $bibliotecaContent = '<p>' .
            '<strong>Indirizzo:</strong> ' . htmlspecialchars($biblioteca['indirizzo'], ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Telefono:</strong> ' . htmlspecialchars($biblioteca['telefono'], ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Email:</strong> ' . htmlspecialchars($biblioteca['email'], ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Lun-Ven:</strong> ' . htmlspecialchars($biblioteca['orario_lun_ven'] ?? '', ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Sabato:</strong> ' . htmlspecialchars($biblioteca['orario_sabato'] ?? '', ENT_QUOTES, 'UTF-8') . ' | ' .
            '<strong>Domenica:</strong> ' . htmlspecialchars($biblioteca['orario_domenica'] ?? '', ENT_QUOTES, 'UTF-8') .
            '</p>' .
            (!empty($biblioteca['note'])
                ? '<p><strong>Note:</strong> ' . htmlspecialchars($biblioteca['note'], ENT_QUOTES, 'UTF-8') . '</p>'
                : '') .
            '<p><a href="modifica-biblio-admin.php">Modifica</a></p>';
    }

    $errorMsg = '';
    if ($errorMessage !== '') {
        $errorMsg = '<div>' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    $successMsg = '';
    if (!empty($successMessage)) {
        $successMsg = '<div>' . htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    $template = file_get_contents(__DIR__ . '/../html/admin/showDashboardAdmin.html');
    echo strtr($template, [
        '[ERROR_MESSAGE]' => $errorMsg,
        '[SUCCESS_MESSAGE]' => '',
        '[GENERALITA_SUCCESS_MESSAGE]' => $successMsg,
        '[LIBRI_TOTALI]' => htmlspecialchars((string) ($stats['libri_totali'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[LIBRI_PRESTATI]' => htmlspecialchars((string) ($stats['libri_prenotati'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[LIBRI_DISPONIBILI]' => htmlspecialchars((string) ($stats['libri_non_prenotati'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[UTENTI_TOTALI]' => htmlspecialchars((string) ($stats['utenti_totali'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[RECENSIONI_TOTALI]' => htmlspecialchars((string) ($stats['recensioni_totali'] ?? 0), ENT_QUOTES, 'UTF-8'),
        '[BIBLIOTECA_CONTENT]' => $bibliotecaContent,
    ]);



