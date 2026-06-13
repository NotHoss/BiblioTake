//libreria di validazione client comune per bibliotake
//
//uso dai file per-pagina:
//
//  metodo 1, automatico:
//      aggiungere l'attributo data-validate al tag <form> e includere questo file
//      con uno <script src="js/validation.js"></script> nella pagina.
//      l'inizializzazione parte da sola al dom content loaded.
//
//  metodo 2, manuale (es. login.js, register.js, ecc.):
//      var form = document.getElementById('idForm');
//      BTValidation.initForm(form);
//
//attributi HTML5 letti dagli input (nessuna regex nel JS):
//  required, minlength, maxlength, min, max, pattern, type, accept, title
//
//attributi custom:
//  data-match="idAltroCampo"   conferma uguale a un altro campo (es. password)
//  data-error-msg="..."        messaggio per qualsiasi errore di quel campo
//  data-max-mb="2"             dimensione massima file in MB
//
//l'attributo title se presente viene usato come messaggio sostitutivo
//(standard HTML5, viene mostrato dal browser come hint sul campo).
//
//gli errori sono mostrati in uno <span role="alert" class="campo-errore">
//inserito subito dopo l'input, e l'input riceve class="input-error" +
//aria-invalid="true". il file CSS deve definire .campo-errore e .input-error.

(function () {

    //messaggi di default. i file per-pagina possono sovrascriverli
    //assegnando a BTValidation.MSG.<chiave> = '...' prima di initForm.
    var MSG = {
        required:  'questo campo è obbligatorio.',
        minlength: 'inserisci almeno [N] caratteri.',
        maxlength: 'non puoi superare [N] caratteri.',
        pattern:   'il formato inserito non è valido.',
        email:     'inserisci un indirizzo email valido.',
        url:       'inserisci un URL valido.',
        number:    'inserisci un numero valido.',
        min:       'il valore minimo è [N].',
        max:       'il valore massimo è [N].',
        date:      'inserisci una data valida.',
        match:     'i due campi non coincidono.',
        file:      'seleziona un file.',
        fileSize:  'il file supera [N] MB.',
        fileType:  'tipo di file non consentito.'
    };

    //tipi da ignorare nella validazione (non hanno valore utente)
    var TIPI_IGNORATI = { hidden: 1, submit: 1, button: 1, reset: 1, image: 1, file: 0 };

    function trim(s) {
        if (s === null || typeof s === 'undefined') return '';
        return ('' + s).replace(/^\s+|\s+$/g, '');
    }

    function aggiungiClasse(el, c) {
        var cur = el.className || '';
        var lista = cur.split(/\s+/);
        for (var i = 0; i < lista.length; i++) if (lista[i] === c) return;
        lista.push(c);
        el.className = trim(lista.join(' '));
    }

    function rimuoviClasse(el, c) {
        var cur = el.className || '';
        var lista = cur.split(/\s+/);
        var out = [];
        for (var i = 0; i < lista.length; i++) {
            if (lista[i] !== c && lista[i] !== '') out.push(lista[i]);
        }
        el.className = out.join(' ');
    }

    //id univoco dello span errore per un dato input
    function idErrore(input) {
        var base = input.id || input.name || 'campo';
        return base + '-error';
    }

    //ritorna lo span errore esistente o null
    function spanErroreEsistente(input) {
        return document.getElementById(idErrore(input));
    }

    //ritorna lo span errore creandolo se non esiste
    function spanErrore(input) {
        var span = spanErroreEsistente(input);
        if (span) return span;

        span = document.createElement('span');
        span.id = idErrore(input);
        span.className = 'campo-errore';
        span.setAttribute('role', 'alert');

        //inserisce subito dopo l'input nel container
        if (input.parentNode) {
            input.parentNode.insertBefore(span, input.nextSibling);
        }

        //collega lo span come aria-describedby preservando eventuali altri id
        var describedBy = input.getAttribute('aria-describedby');
        var ids = describedBy ? describedBy.split(/\s+/) : [];
        var giaPresente = false;
        for (var i = 0; i < ids.length; i++) if (ids[i] === span.id) { giaPresente = true; break; }
        if (!giaPresente) ids.push(span.id);
        input.setAttribute('aria-describedby', trim(ids.join(' ')));

        return span;
    }

    //segna l'input come errato e mostra il messaggio
    function mostraErrore(input, msg) {
        //override del messaggio se l'input ha data-error-msg o title
        var custom = input.getAttribute('data-error-msg') || input.getAttribute('title');
        if (custom && trim(custom) !== '') msg = custom;

        input.setAttribute('aria-invalid', 'true');
        aggiungiClasse(input, 'input-error');

        var span = spanErrore(input);
        span.textContent = msg;
        return false;
    }

    //pulisce lo stato di errore di un input
    function rimuoviErrore(input) {
        input.removeAttribute('aria-invalid');
        rimuoviClasse(input, 'input-error');
        var span = spanErroreEsistente(input);
        if (span) span.textContent = '';
        return true;
    }

    //valida un radio group: chiamato sul primo radio del gruppo
    function validaRadio(input) {
        if (!input.form) return true;
        var gruppo = input.form.querySelectorAll('input[type="radio"][name="' + input.name + '"]');
        if (gruppo.length === 0) return true;

        var richiesto = false;
        var scelto = false;
        for (var i = 0; i < gruppo.length; i++) {
            if (gruppo[i].required) richiesto = true;
            if (gruppo[i].checked) scelto = true;
        }
        if (richiesto && !scelto) return mostraErrore(gruppo[0], MSG.required);
        return rimuoviErrore(gruppo[0]);
    }

    //valida un singolo input/textarea/select
    function validaCampo(input) {
        if (!input || input.disabled) return true;

        var tag = (input.tagName || '').toLowerCase();
        var tipo = (input.type || '').toLowerCase();

        if (TIPI_IGNORATI[tipo] === 1) return true;

        if (tipo === 'radio') return validaRadio(input);

        if (tipo === 'checkbox') {
            if (input.required && !input.checked) return mostraErrore(input, MSG.required);
            return rimuoviErrore(input);
        }

        if (tipo === 'file') {
            if (input.required && (!input.files || input.files.length === 0)) {
                return mostraErrore(input, MSG.file);
            }
            if (input.files && input.files.length > 0) {
                var maxMB = parseFloat(input.getAttribute('data-max-mb'));
                var accept = input.getAttribute('accept');
                for (var k = 0; k < input.files.length; k++) {
                    var f = input.files[k];
                    if (!isNaN(maxMB) && f.size > maxMB * 1024 * 1024) {
                        return mostraErrore(input, MSG.fileSize.replace('[N]', maxMB));
                    }
                    if (accept && !accettato(f, accept)) {
                        return mostraErrore(input, MSG.fileType);
                    }
                }
            }
            return rimuoviErrore(input);
        }

        //input testuali, textarea, select
        var valore = (typeof input.value === 'string') ? input.value : '';
        var valoreTrim = trim(valore);

        //required
        if (input.required && valoreTrim === '') {
            return mostraErrore(input, MSG.required);
        }

        //campo vuoto e non required: ok
        if (valoreTrim === '') return rimuoviErrore(input);

        //type-specific
        if (tipo === 'email') {
            //regex volutamente permissiva (validazione finale spetta al server)
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valoreTrim)) {
                return mostraErrore(input, MSG.email);
            }
        } else if (tipo === 'url') {
            if (!/^https?:\/\/[^\s]+$/.test(valoreTrim)) {
                return mostraErrore(input, MSG.url);
            }
        } else if (tipo === 'number') {
            if (isNaN(parseFloat(valoreTrim)) || !/^-?\d*(?:\.\d+)?$/.test(valoreTrim)) {
                return mostraErrore(input, MSG.number);
            }
        } else if (tipo === 'date') {
            if (!/^\d{4}-\d{2}-\d{2}$/.test(valoreTrim) || isNaN(new Date(valoreTrim).getTime())) {
                return mostraErrore(input, MSG.date);
            }
        }

        //minlength / maxlength
        var ml = input.getAttribute('minlength');
        if (ml !== null && valore.length < parseInt(ml, 10)) {
            return mostraErrore(input, MSG.minlength.replace('[N]', ml));
        }
        var mxl = input.getAttribute('maxlength');
        if (mxl !== null && valore.length > parseInt(mxl, 10)) {
            return mostraErrore(input, MSG.maxlength.replace('[N]', mxl));
        }

        //min / max numerici e date
        var mn = input.getAttribute('min');
        if (mn !== null) {
            if (tipo === 'number' || tipo === 'range') {
                if (parseFloat(valoreTrim) < parseFloat(mn)) {
                    return mostraErrore(input, MSG.min.replace('[N]', mn));
                }
            } else if (tipo === 'date') {
                if (valoreTrim < mn) return mostraErrore(input, MSG.min.replace('[N]', mn));
            }
        }
        var mx = input.getAttribute('max');
        if (mx !== null) {
            if (tipo === 'number' || tipo === 'range') {
                if (parseFloat(valoreTrim) > parseFloat(mx)) {
                    return mostraErrore(input, MSG.max.replace('[N]', mx));
                }
            } else if (tipo === 'date') {
                if (valoreTrim > mx) return mostraErrore(input, MSG.max.replace('[N]', mx));
            }
        }

        //pattern
        var pat = input.getAttribute('pattern');
        if (pat) {
            var re = null;
            try { re = new RegExp('^(?:' + pat + ')$'); } catch (e) { re = null; }
            if (re && !re.test(valoreTrim)) {
                return mostraErrore(input, MSG.pattern);
            }
        }

        //data-match: campo deve coincidere con un altro (conferma password)
        var matchId = input.getAttribute('data-match');
        if (matchId) {
            var altro = document.getElementById(matchId);
            if (altro && altro.value !== input.value) {
                return mostraErrore(input, MSG.match);
            }
        }

        return rimuoviErrore(input);
    }

    //controlla se il file rispetta l'attributo accept (es: "image/*,.pdf")
    function accettato(file, accept) {
        var tipi = accept.split(',');
        for (var i = 0; i < tipi.length; i++) {
            var t = trim(tipi[i]);
            if (t === '') continue;
            if (t.charAt(0) === '.') {
                //estensione
                var ext = t.toLowerCase();
                var nome = file.name.toLowerCase();
                if (nome.length >= ext.length && nome.substring(nome.length - ext.length) === ext) {
                    return true;
                }
            } else if (t.indexOf('/') !== -1) {
                //mime type, eventualmente con wildcard image/*
                if (t.charAt(t.length - 1) === '*') {
                    var prefix = t.substring(0, t.length - 1);
                    if (file.type.indexOf(prefix) === 0) return true;
                } else if (file.type === t) {
                    return true;
                }
            }
        }
        return false;
    }

    //valida tutti i campi del form. ritorna true se tutto ok.
    //sposta il focus sul primo invalido.
    function validaForm(form) {
        if (!form) return true;
        var inputs = form.querySelectorAll('input, textarea, select');
        var tuttoOk = true;
        var primoInvalido = null;
        var radioVisti = {};

        for (var i = 0; i < inputs.length; i++) {
            var el = inputs[i];
            //per i radio del gruppo, valido solo una volta
            if (el.type === 'radio') {
                if (radioVisti[el.name]) continue;
                radioVisti[el.name] = 1;
            }
            var valido = validaCampo(el);
            if (!valido) {
                tuttoOk = false;
                if (primoInvalido === null) primoInvalido = el;
            }
        }

        if (!tuttoOk && primoInvalido && typeof primoInvalido.focus === 'function') {
            try { primoInvalido.focus(); } catch (e) { /* ignora se non focusabile */ }
        }
        return tuttoOk;
    }

    //attacca i listener al form. da chiamare una sola volta per form.
    function initForm(form) {
        if (!form || form.tagName.toLowerCase() !== 'form') return;
        if (form.getAttribute('data-bt-init') === '1') return; //evita doppio init
        form.setAttribute('data-bt-init', '1');

        //disabilita la validazione nativa HTML5 (richiesto dalle regole del corso)
        form.noValidate = true;

        var inputs = form.querySelectorAll('input, textarea, select');
        for (var i = 0; i < inputs.length; i++) {
            var el = inputs[i];
            var tipo = (el.type || '').toLowerCase();
            if (TIPI_IGNORATI[tipo] === 1) continue;

            //blur: valida quando il campo perde il focus
            el.addEventListener('blur', function (e) {
                validaCampo(e.target);
            });

            //change: utile per select, checkbox, radio, file
            if (tipo === 'radio' || tipo === 'checkbox' || tipo === 'file' ||
                el.tagName.toLowerCase() === 'select') {
                el.addEventListener('change', function (e) {
                    validaCampo(e.target);
                });
            }

            //per i campi data-match: rivalida quando cambia il campo riferimento
            //(es. se l'utente cambia la password, rivalida la conferma)
            var matchId = el.getAttribute('data-match');
            if (matchId) {
                var sorgente = document.getElementById(matchId);
                if (sorgente) {
                    (function (campoConferma) {
                        sorgente.addEventListener('input', function () {
                            if (trim(campoConferma.value) !== '') validaCampo(campoConferma);
                        });
                    })(el);
                }
            }
        }

        //submit: blocca se non valido
        form.addEventListener('submit', function (e) {
            if (!validaForm(form)) {
                if (typeof e.preventDefault === 'function') e.preventDefault();
                if (typeof e.stopPropagation === 'function') e.stopPropagation();
                return false;
            }
            return true;
        });
    }

    //auto-init di tutti i form con attributo data-validate
    function autoInit() {
        var forms = document.querySelectorAll('form[data-validate]');
        for (var i = 0; i < forms.length; i++) initForm(forms[i]);
    }

    //API pubblica
    window.BTValidation = {
        initForm:      initForm,
        validaForm:    validaForm,
        validaCampo:   validaCampo,
        mostraErrore:  mostraErrore,
        rimuoviErrore: rimuoviErrore,
        MSG:           MSG
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInit);
    } else {
        autoInit();
    }

})();
