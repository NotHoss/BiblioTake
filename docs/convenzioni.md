# Convenzioni di Naming — BiblioTake

Questo documento definisce le convenzioni da seguire in tutto il progetto.
Ogni membro del team deve rispettarle per garantire coerenza e leggibilità del codice.

---

## Database

### Tabelle

- `snake_case`, singolare, tutto minuscolo
- Esempi: `biblioteca`, `libro`, `utente`, `prestito`, `recensione`, `tag`, `libro_tag`

### Colonne

- `snake_case`, tutto minuscolo
- **Primary key**: sempre `id INT AUTO_INCREMENT` per ogni tabella
- **Foreign key**: `{nome_tabella}_id` (es. `libro_id`, `utente_id`, `biblioteca_id`)
- **Campi multi-parola**: `snake_case` (es. `data_inizio`, `codice_isbn`, `casa_editrice`)
- **Booleani**: `is_{nome}` oppure aggettivo diretto (es. `is_attivo`, `attivo`)
- **Campi naturalmente univoci**: dichiarati come `UNIQUE`, non come PK
  - `utente.email` → `UNIQUE`
  - `libro.codice_isbn` → `UNIQUE`

---

## PHP

| Elemento | Convenzione | Esempio |
|---|---|---|
| Funzioni | camelCase | `getLibriByCategoria()`, `getUserById()` |
| Variabili | camelCase | `$libroId`, `$pageTitle`, `$currentUser` |
| Costanti | UPPER\_SNAKE\_CASE | `DB_HOST`, `SITE_NAME`, `MAX_RESULTS` |
| File di include/funzioni | snake\_case | `auth.php`, `db.php`, `libro_functions.php` |
| File view | prefisso `show` + sostantivo PascalCase | `showCatalogo.php`, `showLibro.php`, `showDashboard.php` |
| File modello (root) | lowercase, parole separate da trattino | `catalogo.php`, `dettaglio-libro.php`, `login.php` |

---

## HTML

| Elemento | Convenzione | Esempio |
|---|---|---|
| Attributo `id` | kebab-case | `main-content`, `login-form`, `book-card-42` |
| Attributo `class` | kebab-case | `book-card`, `nav-breadcrumb`, `error-message` |
| Attributo `name` nei form | kebab-case | `name="codice-isbn"`, `name="data-inizio"` |

---

## CSS

| Elemento | Convenzione | Esempio |
|---|---|---|
| Selettori classe | kebab-case | `.book-card`, `.nav-breadcrumb` |
| Selettori ID | kebab-case | `#main-content`, `#login-form` |
| Nome file | lowercase | `general.css`, `layout.css`, `print.css` |

---

## JavaScript

| Elemento | Convenzione | Esempio |
|---|---|---|
| Variabili | camelCase | `formErrors`, `submitButton` |
| Funzioni | camelCase | `validateForm()`, `showError()` |
| Costanti | UPPER\_SNAKE\_CASE | `MAX_LENGTH`, `BASE_URL` |
| Nome file | kebab-case | `validation.js`, `catalogo-filters.js` |

---

## Git

### Branch

| Tipo | Pattern | Esempio |
|---|---|---|
| Feature | `feat/nome-feature` | `feat/auth`, `feat/catalogo`, `feat/prestiti` |
| Bugfix | `fix/descrizione-bug` | `fix/login-redirect`, `fix/isbn-validation` |
| Documentazione | `docs/nome` | `docs/db-schema`, `docs/convenzioni` |

Nomi branch: tutto minuscolo, parole separate da trattino.

### Commit

Conventional Commits:

```
<tipo>(<scope>): <descrizione breve>
```

Tipi: `feat`, `fix`, `refactor`, `docs`, `chore`, `test`, `style`

Esempi:
```
feat(auth): aggiungi login con sessione
fix(prestiti): correggi calcolo data restituzione
docs(db): aggiorna schema entità
```
