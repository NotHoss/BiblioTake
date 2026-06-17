document.addEventListener('DOMContentLoaded', function () {
    // Crea il pulsante se non esiste nell'HTML
    var btn = document.getElementById('back-to-top');
    if (!btn) {
        btn = document.createElement('button');
        btn.id = 'back-to-top';
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Torna in alto');
        btn.innerHTML = '&#9650;';
        document.body.appendChild(btn);
    }

    btn.style.display = 'none';

    function toggle() {
        if (window.scrollY > 200) {
            btn.style.display = 'block';
        } else {
            btn.style.display = 'none';
        }
    }

    window.addEventListener('scroll', toggle, { passive: true });
    toggle();

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});