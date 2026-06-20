document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('modifica-profilo');
    if (!form) return;

    var avatarOptions = document.querySelectorAll('.avatar-option');
    var avatarRadios = form.querySelectorAll('input[name="selected_avatar"]');
    var profileImage = form.querySelector('section > img');
    var changedAvatar = false;

    function aggiornaAvatar(radio) {
        if (!radio) return;

        avatarOptions.forEach(function (option) {
            option.classList.remove('selected');
        });

        var option = radio.closest('.avatar-option');
        if (option) {
            option.classList.add('selected');
            var selectedImg = option.querySelector('img');
            if (selectedImg && profileImage) {
                profileImage.src = selectedImg.src;
                profileImage.alt = selectedImg.alt;
            }
        }

        changedAvatar = true;
    }

    avatarRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (radio.checked) aggiornaAvatar(radio);
        });
    });

    form.addEventListener('submit', function (e) {
        var inputs = form.querySelectorAll('input[name="new_username"], input[name="new_email"], input[name="current_password"], input[name="new_password"], input[name="confirm_new_password"]');
        var hasChanges = false;

        inputs.forEach(function (input) {
            if (input.value !== input.defaultValue) {
                hasChanges = true;
            }
        });

        if (!hasChanges && changedAvatar === false) {
            e.preventDefault();
            alert('Nessuna modifica rilevata.');
        }
    });
});
