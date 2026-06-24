document.addEventListener('DOMContentLoaded', function () {
    // Crea il link se non esiste nell'HTML
    var btn = document.getElementById('back-to-top');
    if (!btn) {
        btn = document.createElement('a');
        btn.id = 'back-to-top';
        btn.href = '#top';
        btn.setAttribute('aria-label', 'Torna in alto');
        btn.innerHTML = '&#9650;';
        document.body.appendChild(btn);
    }

    btn.style.display = 'none';

    function toggle() {
        if (window.scrollY > 200) {
            btn.style.display = 'flex';
        } else {
            btn.style.display = 'none';
        }
    }

    window.addEventListener('scroll', toggle, { passive: true });
    toggle();

    btn.addEventListener('click', function (event) {
        event.preventDefault();
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });

        if (!document.body.hasAttribute('tabindex')) {
            document.body.setAttribute('tabindex', '-1');
        }
        try {
            document.body.focus({ preventScroll: true });
        } catch (error) {
            document.body.focus();
        }
    });
});
