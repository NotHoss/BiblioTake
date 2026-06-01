<?php
    $indirizzo = (string) ($biblioteca['indirizzo'] ?? 'Via Garibaldi 12, Padova');
    $telefono = (string) ($biblioteca['telefono'] ?? '+39 02 88997766');
    $email = (string) ($biblioteca['email'] ?? 'contatti@bibliotake-padova.it');
    $note = (string) ($biblioteca['note'] ?? '');
    $orarioLunVen = (string) ($biblioteca['orario_lun_ven'] ?? '9:00 - 19:00');
    $orarioSabato = (string) ($biblioteca['orario_sabato'] ?? '9:00 - 13:00');
    $orarioDomenica = (string) ($biblioteca['orario_domenica'] ?? 'Chiuso');
    $telefonoHref = 'tel:' . preg_replace('/[^\d\+]/', '', $telefono);
    
    $template = file_get_contents(__DIR__ . '/../html/showContatti.html');
    
    $placeholders = [
        '[INDIRIZZO]'       => htmlspecialchars($indirizzo, ENT_QUOTES, 'UTF-8'),
        '[TELEFONO_HREF]'   => htmlspecialchars($telefonoHref, ENT_QUOTES, 'UTF-8'),
        '[TELEFONO]'        => htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8'),
        '[EMAIL]'           => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
        '[ORARIO_LUN_VEN]'  => htmlspecialchars($orarioLunVen, ENT_QUOTES, 'UTF-8'),
        '[ORARIO_SABATO]'   => htmlspecialchars($orarioSabato, ENT_QUOTES, 'UTF-8'),
        '[ORARIO_DOMENICA]' => htmlspecialchars($orarioDomenica, ENT_QUOTES, 'UTF-8'),
        '[NOTE_BLOCK]'      => $note !== ''
            ? '<p><strong>Note:</strong> ' . htmlspecialchars($note, ENT_QUOTES, 'UTF-8') . '</p>'
            : '',
    ];

    echo strtr($template, $placeholders);
?>
