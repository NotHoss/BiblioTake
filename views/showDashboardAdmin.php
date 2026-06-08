
    <?php
    $bibliotecaContent = '';
    if (empty($biblioteca)) {
        $bibliotecaContent = '<p>Non ci sono generalità da mostrare.</p>';
    } else {
        $items = [];
        if (!empty($biblioteca['indirizzo'])) {
            $items[] = '<li><strong>Indirizzo:</strong> ' . htmlspecialchars($biblioteca['indirizzo'], ENT_QUOTES, 'UTF-8') . '</li>';
        }
        if (!empty($biblioteca['telefono'])) {
            $items[] = '<li><strong>Telefono:</strong> ' . htmlspecialchars($biblioteca['telefono'], ENT_QUOTES, 'UTF-8') . '</li>';
        }
        if (!empty($biblioteca['email'])) {
            $items[] = '<li><strong>Email:</strong> ' . htmlspecialchars($biblioteca['email'], ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $orari = [];
        if (!empty($biblioteca['orario_lun_ven'])) {
            $orari[] = 'Lun-Ven: ' . htmlspecialchars($biblioteca['orario_lun_ven'], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($biblioteca['orario_sabato'])) {
            $orari[] = 'Sabato: ' . htmlspecialchars($biblioteca['orario_sabato'], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($biblioteca['orario_domenica'])) {
            $orari[] = 'Domenica: ' . htmlspecialchars($biblioteca['orario_domenica'], ENT_QUOTES, 'UTF-8');
        }
        if (!empty($orari)) {
            $items[] = '<li><strong>Orari:</strong> ' . implode(' — ', $orari) . '</li>';
        }
        if (!empty($biblioteca['note'])) {
            $items[] = '<li><strong>Note:</strong> ' . htmlspecialchars($biblioteca['note'], ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $items[] = '<li class="actions"><a href="modifica-biblio-admin.php">Modifica</a></li>';

        $bibliotecaContent = '<ul class="generalita-list">' . implode("\n", $items) . '</ul>';
    }

    $errorMsg = '';
    if ($errorMessage !== '') {
        $errorMsg = renderAdminStatusMessage($errorMessage, 'error');
    }

    $successMsg = '';
    if (!empty($successMessage)) {
        $successMsg = renderAdminStatusMessage($successMessage, 'success');
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



