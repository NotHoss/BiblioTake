(function () {
    function aggiornaCampoNuovaCategoria(select, input) {
        var row = input.parentNode;
        var nuovaCategoria = select.value === '__NEW__';

        if (nuovaCategoria) {
            if (row) {
                row.hidden = false;
                row.removeAttribute('aria-hidden');
            }
            input.disabled = false;
            input.required = true;
            input.setAttribute('aria-required', 'true');
        } else {
            input.value = '';
            input.disabled = true;
            input.required = false;
            input.removeAttribute('aria-required');
            if (window.BTValidation) {
                window.BTValidation.rimuoviErrore(input);
            }
            if (row) {
                row.hidden = true;
                row.setAttribute('aria-hidden', 'true');
            }
        }
    }

    function initCategoria(selectId, inputId) {
        var select = document.getElementById(selectId);
        var input = document.getElementById(inputId);

        if (!select || !input) return;

        aggiornaCampoNuovaCategoria(select, input);
        select.addEventListener('change', function () {
            aggiornaCampoNuovaCategoria(select, input);
        });
    }

    function init() {
        initCategoria('categoria-select', 'categoria-nuova');
        initCategoria('categoria-select-edit', 'categoria-nuova-edit');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
