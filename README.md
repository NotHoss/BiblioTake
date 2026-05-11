# BiblioTake

Progetto universitario per il corso di Tecnologie Web — UNIPD 2025/2026.

Sistema di gestione di una biblioteca: catalogo libri, prestiti, recensioni e area amministrativa.

**Stack**: PHP, HTML5, CSS3, JavaScript vanilla — nessun framework esterno.

---

## Link utili

- **Mockup Figma**: https://www.figma.com/design/INdihnrPXHwWoxZfs26ySh/Mocup-Tecweb?node-id=0-1&t=Lk4cmSqeMUkULH3J-1
- **Schema ER database**: https://drive.google.com/file/d/1XSvOi1RL1UKvB4frGMFJFqvLO1QHbAT8/view?usp=drive_link

---

## Struttura branch

```
main          ← codice stabile, riceve solo merge da develop
  └── develop ← branch di integrazione principale
        ├── feat/auth
        ├── feat/catalogo
        ├── feat/prestiti
        └── feat/admin
```

---

## Documentazione

| File | Contenuto |
|---|---|
| `docs/db_pseudocodice.md` | Schema entità/relazioni del database |
| `docs/caratteristiche.md` | Funzionalità e aree del sito |
| `docs/requisiti.md` | Requisiti tecnici e di accessibilità |
| `docs/convenzioni.md` | Convenzioni di naming per tutto il progetto |

## Login
### Admin
- Email: admin@bibliotake.it
- Password: adminadmin
### User
- Email: user@gmail.com
- Password: useruser
