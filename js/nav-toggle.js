document.addEventListener('DOMContentLoaded', function () {
    var button = document.getElementById('nav-toggle');
    var menu = document.getElementById('primary-navigation');

    if (!button || !menu) return;

    function setOpen(open) {
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
        button.setAttribute('aria-label', open ? 'Chiudi menu di navigazione' : 'Apri menu di navigazione');
    }

    setOpen(false);

    button.addEventListener('click', function () {
        setOpen(button.getAttribute('aria-expanded') !== 'true');
    });

    menu.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setOpen(false);
            button.focus();
        }
    });
});
