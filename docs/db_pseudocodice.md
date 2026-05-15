# Schema DB — BiblioTake

Naming conventions: vedere `convenzioni.md`.
Tabelle: `snake_case` singolare minuscolo. PK: `id INT`. FK: `{tabella}_id INT`.

---

## Entità

- **biblioteca**
    - <u>id</u>: *INT*
    - indirizzo: *VARCHAR* `UNIQUE`
    - telefono: *VARCHAR*
    - email: *VARCHAR*

- **utente**
    - <u>id</u>: *INT*
    - email: *VARCHAR* `UNIQUE`
    - username: *VARCHAR*
    - password: *VARCHAR* *(hash bcrypt)*
    - foto_profilo: *VARCHAR(255)* *(path immagine profilo, es. `images/place-holder.jpg`)*
    - attivo: *BOOLEAN*
    - ruolo: *ENUM* (`utente`, `admin`)

- **libro**
    - <u>id</u>: *INT*
    - codice_isbn: *VARCHAR* `UNIQUE`
    - titolo: *VARCHAR*
    - autore: *VARCHAR*
    - casa_editrice: *VARCHAR*
    - edizione: *INT*
    - anno: *YEAR*
    - lingua: *VARCHAR*
    - descrizione: *TEXT*
    - pagine: *INT*
    - copertina: *VARCHAR(255)* *(path copertina, es. `images/place-holder.jpg`)*
    - categoria: *VARCHAR*

- **prestito**
    - <u>id</u>: *INT*
    - data_inizio: *DATETIME*
    - data_fine: *DATETIME*
    - stato: *ENUM* (`attivo`, `concluso`, `in_ritardo`)
    - biblioteca_id: *INT → FK* → biblioteca.id
    - libro_id: *INT → FK* → libro.id
    - utente_id: *INT → FK* → utente.id

- **recensione**
    - <u>id</u>: *INT*
    - valutazione: *INT* *(1–5)*
    - testo: *TEXT*
    - data: *DATETIME*
    - libro_id: *INT → FK* → libro.id
    - utente_id: *INT → FK* → utente.id

---

## Relazioni

- **biblioteca — prestito**: Ha luogo in (1,N)
- **libro — prestito**: Contenuto in (1,N)
- **utente — prestito**: Effettua (1,N)
- **utente — recensione**: Scrive (0,N)
- **libro — recensione**: È recensito da (0,N)

---

## Note

- **Valutazione media** di `libro`: dato derivato — si calcola come `AVG(recensione.valutazione)` al momento della query, non viene salvato.
- **Disponibilità** di `libro`: dato derivato — si calcola contando i `prestito` con `stato = 'attivo'` per quel libro.
- La PK di `biblioteca` è `id` numerico: cambiare l'indirizzo non rompe le FK in `prestito`.
- I campi `edizione`, `anno`, `lingua`, `pagine` sono stati mantenuti da `libro` perché utili alla visualizzazione del catalogo.
