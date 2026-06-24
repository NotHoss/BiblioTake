<?php

function renderHtmlTemplate($templatePath, array $replacements = []) {
    $template = file_get_contents($templatePath);
    return strtr($template, $replacements);
}

function renderSearchForm(array $config) {
    $templatePath = __DIR__ . '/../../html/template/search-form.html';
    $fieldsHtml = '';

    $groupOpen = false;
    $groupClass = '';
    foreach (($config['fields'] ?? []) as $field) {
        $type = strtolower((string) ($field['type'] ?? 'text'));
        $name = htmlspecialchars((string) ($field['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $id = htmlspecialchars((string) ($field['id'] ?? $field['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars((string) ($field['label'] ?? ''), ENT_QUOTES, 'UTF-8');
        $value = (string) ($field['value'] ?? '');
        $placeholder = htmlspecialchars((string) ($field['placeholder'] ?? ''), ENT_QUOTES, 'UTF-8');
        $min = array_key_exists('min', $field) ? ' min="' . htmlspecialchars((string) $field['min'], ENT_QUOTES, 'UTF-8') . '"' : '';
        $max = array_key_exists('max', $field) ? ' max="' . htmlspecialchars((string) $field['max'], ENT_QUOTES, 'UTF-8') . '"' : '';
        $step = array_key_exists('step', $field) ? ' step="' . htmlspecialchars((string) $field['step'], ENT_QUOTES, 'UTF-8') . '"' : '';
        $checked = !empty($field['checked']) ? ' checked="checked"' : '';
        $selected = (string) ($field['selected'] ?? $value);

        // handle optional group wrappers
        if (!empty($field['group_start'])) {
            $groupClass = isset($field['group_class']) ? htmlspecialchars((string) $field['group_class'], ENT_QUOTES, 'UTF-8') : 'compact-row';
            $fieldsHtml .= '<div class="' . $groupClass . '">';
            $groupOpen = true;
        }

        $fieldsHtml .= '<div>';
        if ($type === 'select') {
            if ($label !== '') {
                $fieldsHtml .= '<label for="' . $id . '">' . $label . '</label>';
            }
            $fieldsHtml .= '<select id="' . $id . '" name="' . $name . '">';
            foreach (($field['options'] ?? []) as $optionValue => $optionLabel) {
                $optionValueSafe = htmlspecialchars((string) $optionValue, ENT_QUOTES, 'UTF-8');
                $optionLabelSafe = htmlspecialchars((string) $optionLabel, ENT_QUOTES, 'UTF-8');
                $optionSelected = ((string) $optionValue === $selected) ? ' selected="selected"' : '';
                $fieldsHtml .= '<option value="' . $optionValueSafe . '"' . $optionSelected . '>' . $optionLabelSafe . '</option>';
            }
            $fieldsHtml .= '</select>';
        } elseif ($type === 'checkbox') {
            $fieldsHtml .= '<input type="checkbox" id="' . $id . '" name="' . $name . '" value="1"' . $checked . ' />';
            if ($label !== '') {
                $fieldsHtml .= '<label for="' . $id . '">' . $label . '</label>';
            }
        } else {
            if ($label !== '') {
                $fieldsHtml .= '<label for="' . $id . '">' . $label . '</label>';
            }
            $valueAttr = ($type === 'number' && $value === '') ? '' : ' value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            $fieldsHtml .= '<input type="' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8') . '" id="' . $id . '" name="' . $name . '"' . $valueAttr . ' placeholder="' . $placeholder . '"' . $min . $max . $step . ' />';
        }

        $fieldsHtml .= '</div>';

        if (!empty($field['group_end']) && $groupOpen) {
            $fieldsHtml .= '</div>';
            $groupOpen = false;
            $groupClass = '';
        }
    }

    $resetHref = (string) ($config['resetHref'] ?? '');
    $resetLabel = (string) ($config['resetLabel'] ?? 'Azzera filtri');
    $resetLink = '';
    if ($resetHref !== '' && $resetLabel !== '') {
        $resetLink = '<a href="' . htmlspecialchars($resetHref, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($resetLabel, ENT_QUOTES, 'UTF-8') . '</a>';
    }

    $formAttrs = !empty($config['validate']) ? ' data-validate="data-validate"' : '';

    return renderHtmlTemplate($templatePath, [
        '[FORM_ACTION]' => htmlspecialchars((string) ($config['action'] ?? ''), ENT_QUOTES, 'UTF-8'),
        '[FORM_METHOD]' => htmlspecialchars(strtolower((string) ($config['method'] ?? 'get')) === 'post' ? 'post' : 'get', ENT_QUOTES, 'UTF-8'),
        '[FORM_CLASS]' => htmlspecialchars((string) ($config['class'] ?? 'search-form'), ENT_QUOTES, 'UTF-8'),
        '[FORM_ID]' => htmlspecialchars((string) ($config['id'] ?? 'search-form'), ENT_QUOTES, 'UTF-8'),
        '[FORM_ATTRS]' => $formAttrs,
        '[FORM_FIELDS]' => $fieldsHtml,
        '[SUBMIT_LABEL]' => htmlspecialchars((string) ($config['submitLabel'] ?? 'Filtra'), ENT_QUOTES, 'UTF-8'),
        '[RESET_LINK]' => $resetLink,
    ]);
}

function renderPagination($basePath, array $query, $pagina, $totalPagine, $ariaLabel, $prevLabel = 'Precedente', $nextLabel = 'Successiva') {
    $pagina = max(1, (int) $pagina);
    $totalPagine = max(1, (int) $totalPagine);

    if ($totalPagine <= 1) {
        return '';
    }

    $pagination = '<nav aria-label="' . htmlspecialchars((string) $ariaLabel, ENT_QUOTES, 'UTF-8') . '"><ul>';
    if ($pagina > 1) {
        $pagination .= '<li><a href="' . htmlspecialchars($basePath . '?' . http_build_query(array_merge($query, ['page' => $pagina - 1])), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) $prevLabel, ENT_QUOTES, 'UTF-8') . '</a></li>';
    }

    for ($i = 1; $i <= $totalPagine; $i++) {
        if ($i === $pagina) {
            $pagination .= '<li aria-current="page">' . $i . '</li>';
        } else {
            $pagination .= '<li><a href="' . htmlspecialchars($basePath . '?' . http_build_query(array_merge($query, ['page' => $i])), ENT_QUOTES, 'UTF-8') . '">' . $i . '</a></li>';
        }
    }

    if ($pagina < $totalPagine) {
        $pagination .= '<li><a href="' . htmlspecialchars($basePath . '?' . http_build_query(array_merge($query, ['page' => $pagina + 1])), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) $nextLabel, ENT_QUOTES, 'UTF-8') . '</a></li>';
    }

    $pagination .= '</ul></nav>';

    return $pagination;
}

function renderResultsInfo($total, $start, $end, $singularLabel, $pluralLabel, $emptyMessage) {
    $total = (int) $total;
    if ($total <= 0) {
        return '<p>' . htmlspecialchars((string) $emptyMessage, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    $label = ($total === 1) ? $singularLabel : $pluralLabel;
    return '<p>Mostrati ' . (int) $start . '-' . (int) $end . ' di ' . $total . ' ' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '.</p>';
}
