document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('modifica-profilo');
        const avatarOptions = document.querySelectorAll('.avatar-option');
        const profileImage = form.querySelector('section > img'); // Immagine corrente

        changedAvatar = false;
        // Quando fai click su un avatar, segna il radio come selezionato e aggiorna l'anteprima
        avatarOptions.forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;

                // Rimuove selected da tutti, e lo aggiunge solo a quello cliccato
                avatarOptions.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');

                // Aggiorna l'anteprima con l'immagine selezionata
                const selectedImg = this.querySelector('img');
                profileImage.src = selectedImg.src;
                profileImage.alt = selectedImg.alt;
                changedAvatar = true;
            });
        });

        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[name="new_username"], input[name="new_email"], input[name="current_password"], input[name="new_password"], input[name="confirm_new_password"]');

            let hasChanges = false;

            inputs.forEach(function(input) {
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