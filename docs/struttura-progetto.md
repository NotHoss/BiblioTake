# Struttura del Progetto — BiblioTake

Questo documento descrive la struttura di cartelle e file del progetto, spiega il ruolo di ogni elemento e le regole da rispettare. Va letto prima di scrivere qualsiasi riga di codice.

---

## Albero completo

```
BiblioTake/
├── database/
│   └── db.sql
├── docs/
│   ├── caratteristiche.md
│   ├── convenzioni.md
│   ├── db_pseudocodice.md
│   ├── requisiti.md
│   └── struttura-progetto.md
├── images/
├── includes/
│   ├── config.php
│   ├── variables.php
│   ├── resources.php
│   └── functions/
│       ├── db.php
│       ├── auth.php
│       ├── libro_functions.php
│       ├── prestito_functions.php
│       ├── recensione_functions.php
│       └── admin_functions.php
├── html/
│   ├── aggiungi-recensione.html
│   ├── elimina-recensione.html
│   ├── modifica-recensione.html
│   ├── restituisci.html
│   ├── richiedi-prestito.html
│   ├── show403.html
│   ├── show404.html
│   ├── show500.html
│   ├── showAbout.html
│   ├── showCatalogo.html
│   ├── showContatti.html
│   ├── showDashboard.html
│   ├── showDettaglioLibro.html
│   ├── showIndex.html
│   ├── showLogin.html
│   ├── showLogout.html
│   ├── showPrestiti.html
│   ├── showRecensioni.html
│   ├── showRegister.html
│   ├── admin/
│   │   ├── aggiungi-libro.html
│   │   ├── elimina-libro.html
│   │   ├── modifica-libro.html
│   │   ├── prestiti-utente.html
│   │   ├── showDashboardAdmin.html
│   │   ├── showModificaBiblioAdmin.html
│   │   ├── showLibri.html
│   │   ├── showRecensioni.html
│   │   └── showUtenti.html
│   └── template/
│       ├── footer.html
│       └── header.html
├── javascript/
│   └── validation.js
├── css/
│   ├── style.css
│   ├── mini.css
│   └── print.css
├── views/
│   ├── template/
│   │   ├── header.php
│   │   └── footer.php
│   ├── showIndex.php
│   ├── showCatalogo.php
│   ├── showDettaglioLibro.php
│   ├── showLogin.php
│   ├── showRegister.php
│   ├── showDashboard.php
│   ├── showPrestiti.php
│   ├── showRecensioni.php
│   ├── showDashboardAdmin.php
│   ├── showModificaBiblioAdmin.php
│   ├── showLibriAdmin.php
│   ├── showAggiungiLibroAdmin.php
│   ├── showModificaLibroAdmin.php
│   ├── showEliminaLibroAdmin.php
│   ├── showUtentiAdmin.php
│   ├── showPrestitiAdmin.php
│   └── showRecensioniAdmin.php
├── admin/
│   ├── index.php
│   ├── modifica-biblio-admin.php
│   ├── libri.php
│   ├── aggiungi-libro.php
│   ├── modifica-libro.php
│   ├── elimina-libro.php
│   ├── utenti.php
│   ├── prestiti-utente.php
│   └── recensioni.php
├── index.php
├── catalogo.php
├── dettaglio-libro.php
├── login.php
├── register.php
├── logout.php
├── user/
│   ├── aggiungi-recensione.php
│   ├── dashboard.php
│   ├── elimina-recensione.php
│   ├── elimina-user.php
│   ├── informazioni-utente.php
│   ├── modifica-user.php
│   ├── prestiti.php
│   ├── richiedi-prestito.php
│   ├── restituisci.php
│   └── recensioni.php
├── about.php
├── contatti.php
├── 403.php
├── 404.php
└── 500.php
```

---

## Pattern Model-View

Il principio fondamentale dell'architettura è la separazione netta tra **logica** e **presentazione**.

Ogni file PHP nella root (e in `admin/`) è un **modello**: legge dati, controlla sessioni, valida input, esegue redirect. Non contiene HTML oltre alle chiamate ai template.

Ogni file in `views/` è una **view**: riceve dati già pronti nelle variabili PHP e li stampa in HTML. Non esegue query, non legge `$_POST` o `$_GET`, non fa redirect.

### Schema di ogni modello

```php
<?php
// 1. Carica tutte le dipendenze PHP
require_once 'includes/resources.php';

// 2. Variabili di pagina (SEO e template)
$pageTitle       = 'Catalogo libri — BiblioTake';
$pageDescription = 'Sfoglia il catalogo completo della biblioteca.';
$pageKeywords    = 'catalogo, libri, biblioteca, prestito';
$currentPage     = 'catalogo';   // usato dal template per evidenziare la voce di menu

// 3. Logica PHP pura (query, controlli, redirect)
$filtri  = [ ... ];
$libri   = getLibri($conn, $filtri, $pagina);

// 4. Composizione della pagina — sempre in questo ordine
require_once 'views/template/header.php';
require_once 'views/showCatalogo.php';
require_once 'views/template/footer.php';
?>
```

Questo schema si replica identico per ogni pagina. L'ordine dei quattro blocchi non cambia mai.

Nota: alcune pagine legate all'area utente sono collocate nella directory `user/` (ad esempio `user/dashboard.php`, `user/modifica-user.php`). I file in `user/` seguono lo stesso schema dei modelli in root ma usano i percorsi relativi corretti per includere le view e le risorse.

---

## Cartelle e file — spiegazione dettagliata

### `database/`

Contiene un unico file: `db.sql`.

Deve essere un dump **completo e aggiornato** che un professore può importare direttamente per far girare il progetto. Deve contenere:
- `DROP TABLE IF EXISTS` per ogni tabella (nell'ordine inverso delle FK).
- `CREATE TABLE` con tutti i vincoli, indici e CHECK.
- `INSERT` con dati di esempio significativi (almeno un admin, qualche utente, qualche libro, prestiti e recensioni).

Il file viene consegnato insieme al codice. Se il database non si importa senza errori, il progetto non viene valutato.

---

### `docs/`

Documentazione di progettazione in Markdown. Non contiene codice.

I file già presenti (`caratteristiche.md`, `convenzioni.md`, `db_pseudocodice.md`, `requisiti.md`) documentano le scelte fatte prima dello sviluppo. Aggiungere qui note su decisioni rilevanti prese durante lo sviluppo, soprattutto se ci si discosta da quanto pianificato.

---

### `images/`

Immagini usate nel sito (copertine libri, logo, icone).

Regole:
- Formati ammessi: `.jpg` per foto, `.png` per immagini con trasparenza, `.svg` per icone.
- Niente `.gif` animate — usare animazioni CSS se necessario.
- Tagliare le immagini alle dimensioni di utilizzo reale prima di inserirle (non scalare via CSS un'immagine 3000px).
- Ogni `<img>` deve avere `width` e `height` espliciti nell'HTML per evitare layout shift.

---

### `includes/`

Tutto il codice PHP riutilizzabile. Non contiene HTML.

#### `includes/config.php`

Costanti globali del progetto. Definite con `define()`, non come variabili.

```php
define('DB_HOST',            'localhost');
define('DB_USER',            'root');
define('DB_PASS',            '');
define('DB_NAME',            'bibliotake');
define('SITE_NAME',          'BiblioTake');
define('MAX_RESULTS_PER_PAGE', 12);

// Sviluppo: mostra errori. In produzione invertire i valori.
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

Nessun altro file definisce costanti. `config.php` è il punto di verità unico per la configurazione.

#### `includes/variables.php`

Inizializza le variabili ricavate da `$_GET` e `$_POST` che vengono riusate in più modelli. Evita di scrivere `isset($_GET['id']) ? (int) $_GET['id'] : 0` in ogni file.

```php
$id     = isset($_GET['id'])     ? (int) $_GET['id']                                          : 0;
$action = isset($_GET['action']) ? htmlspecialchars($_GET['action'], ENT_QUOTES, 'UTF-8')     : '';
$page   = isset($_GET['page'])   ? max(1, (int) $_GET['page'])                                : 1;
```

Non mettere qui variabili specifiche di una singola pagina — quelle vanno nel modello corrispondente.

#### `includes/resources.php`

L'unico file che i modelli includono direttamente con `require_once`. Il suo unico compito è includere tutto il resto nell'ordine corretto.

```php
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions/db.php';
require_once __DIR__ . '/functions/auth.php';
require_once __DIR__ . '/functions/libro_functions.php';
require_once __DIR__ . '/functions/prestito_functions.php';
require_once __DIR__ . '/functions/recensione_functions.php';
require_once __DIR__ . '/functions/admin_functions.php';
require_once __DIR__ . '/variables.php';

$conn = getConnection();
?>
```

Grazie a `__DIR__`, il percorso è corretto indipendentemente da dove viene incluso il file (utile per i modelli dentro `admin/`).

Usare `require_once` e non `include` o `require`: se il file manca, `require_once` blocca l'esecuzione con un errore fatale invece di continuare silenziosamente.

#### `includes/functions/`

Una funzione per file, raggruppate per dominio:

| File | Responsabilità |
|---|---|
| `db.php` | Connessione al database (`getConnection()`) |
| `auth.php` | Login, logout, registrazione, controllo sessione e ruolo |
| `libro_functions.php` | Query su `libro` e `tag` |
| `prestito_functions.php` | Query su `prestito` |
| `recensione_functions.php` | Query su `recensione` |
| `admin_functions.php` | Statistiche, operazioni amministrative |

Regole per le funzioni:
- Ogni funzione riceve `$conn` come primo parametro — non usa una variabile globale.
- Ogni funzione usa **prepared statements** per qualsiasi parametro proveniente dall'esterno.
- Nessuna funzione stampa HTML o fa `echo` — restituisce dati, array o booleani.
- Nessuna funzione chiama `header()` o `exit` tranne quelle in `auth.php` che gestiscono redirect di sicurezza.

---

### `js/`

Contiene un unico file: `validation.js`.

Il suo unico scopo è **duplicare lato client** le validazioni già presenti lato server. Non aggiunge logica applicativa, non fa chiamate HTTP, non manipola il DOM per mostrare/nascondere sezioni di pagina.

Struttura obbligatoria:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Recupera il form — se non esiste nella pagina corrente, esci subito
    var form = document.getElementById('login-form');
    if (!form) return;

    form.addEventListener('submit', function(event) {
        var errors = [];

        // Validazioni...

        if (errors.length > 0) {
            event.preventDefault();
            var errorDiv = document.getElementById('form-errors');
            errorDiv.innerHTML = '<ul><li>' + errors.join('</li><li>') + '</li></ul>';
            errorDiv.setAttribute('style', 'display:block');
        }
    });
});
```

Il guard `if (!form) return` permette di includere sempre lo stesso file su tutte le pagine senza errori alla console.

Nessuna libreria esterna. Nessun `onclick` inline nell'HTML.

---

### `styles/`

#### `styles/css/`

Tre file, sempre presenti:

| File | Contenuto |
|---|---|
| `general.css` | Reset, tipografia, colori, elementi globali (`body`, `a`, `h1`-`h6`, tabelle, form) |
| `layout.css` | Struttura delle pagine: header, nav, main, footer, grid/float del contenuto, media query responsive |
| `print.css` | Stile di stampa: nasconde nav, footer, form; imposta font serif, colori neutri, font-size in `pt` |

Le **media query** stanno dentro i file CSS, mai come attributo `media=""` nel tag `<link>` HTML.

Primo blocco obbligatorio in `general.css`:

```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
```

#### `styles/min/`

Versioni minificate di ogni file CSS. Da generare con uno strumento online (es. cssnano, CSS Minifier) prima della consegna. Vanno consegnati entrambi: sorgente leggibile e versione minificata.

#### `styles/resources.css`

File aggregatore — contiene solo `@import`:

```css
@import url('css/general.css');
@import url('css/layout.css');
@import url('css/print.css');
```

I modelli HTML includono solo questo file, non i singoli CSS. In produzione si può sostituire con un import verso `min/`.

---

### `views/`

#### `views/template/header.php`

Apre il documento HTML e lo chiude fino a `<main id="main-content">`. Include:

1. `<!DOCTYPE html>` e `<html lang="it" xml:lang="it">`
2. `<head>` completo con charset, viewport, title, description, link al CSS
3. Skip link (primo elemento dopo `<body>`)
4. `<header>` con logo e navigazione principale
5. Breadcrumb (su tutte le pagine tranne la homepage)
6. `<main id="main-content">`

Il template usa le variabili `$pageTitle`, `$pageDescription`, `$pageKeywords` e `$currentPage` impostate dal modello chiamante. Non le definisce.

```php
<title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
```

Per la voce di menu della pagina corrente, non si usa `<a>` (evita link circolari):

```php
<li <?= ($currentPage === 'catalogo') ? 'aria-current="page" class="current-page"' : '' ?>>
    <?php if ($currentPage === 'catalogo'): ?>
        Catalogo
    <?php else: ?>
        <a href="catalogo.php">Catalogo</a>
    <?php endif; ?>
</li>
```

#### `views/template/footer.php`

Chiude `</main>`, aggiunge `<footer>` con info biblioteca e badge di validazione, chiude `</body>` e `</html>`.

I badge W3C HTML, W3C CSS e accessibilità vanno nel footer. Sono richiesti per la valutazione.

#### `views/show*.php`

Una view per ogni pagina. Riceve i dati già pronti nelle variabili PHP e li stampa.

Regole:
- Nessuna query al database.
- Nessun accesso a `$_GET`, `$_POST`, `$_SESSION`.
- Nessun `header()` o `exit`.
- Tutto l'HTML è qui, niente HTML nei modelli.

---

### `admin/`

Sottocartella per l'area amministrativa. Stessa struttura dei modelli nella root, ma ogni file deve iniziare con il controllo del ruolo prima di qualsiasi altra operazione:

```php
<?php
require_once '../includes/resources.php';
requireRole('admin');   // redirect a 403.php se non admin

$pageTitle   = 'Dashboard — Admin BiblioTake';
$currentPage = 'admin';
// ...
require_once '../views/template/header.php';
require_once '../views/showDashboardAdmin.php';
// La modifica delle generalità della biblioteca usa la pagina dedicata:
// require_once '../views/showModificaBiblioAdmin.php';
require_once '../views/template/footer.php';
?>
```

Il percorso di `require_once` parte da `../` perché i file si trovano un livello più in profondità rispetto alla root.

---

### File modello nella root

Ogni file PHP nella root segue il pattern descritto sopra. Alcuni casi particolari:

#### `logout.php`

Non ha una view. Distrugge la sessione e reindirizza:

```php
<?php
require_once 'includes/resources.php';
session_start();
session_unset();
session_destroy();
header('Location: index.php');
exit;
?>
```

#### `403.php`, `404.php`, `500.php`

Pagine di errore. Vanno impostate nell'header HTTP corretto prima di qualsiasi output:

```php
<?php
http_response_code(404);
require_once 'includes/resources.php';
$pageTitle = 'Pagina non trovata — BiblioTake';
require_once 'views/template/header.php';
require_once 'views/show404.php';
require_once 'views/template/footer.php';
?>
```

I modelli che gestiscono operazioni POST (es. `richiedi-prestito.php`, `elimina-recensione.php`) non hanno una view propria: al termine dell'operazione eseguono sempre un redirect con `header('Location: ...')` seguito da `exit`. Non stampano mai HTML direttamente.

---

## Regole trasversali

### `require_once` ovunque

Mai `include`, mai `require` semplice. `require_once` garantisce che un file venga incluso al massimo una volta e blocca l'esecuzione se il file manca.

### Nessun output prima degli header

`session_start()` e `header()` devono essere chiamati prima di qualsiasi stampa HTML (anche prima dello spazio bianco fuori dai tag `<?php ?>`). Tenere il tag di apertura `<?php` alla prima riga del file senza righe vuote precedenti.

### Nessun errore PHP visibile

In sviluppo, `E_ALL` è attivo per vedere tutto. In produzione si disattiva. In nessun caso si usa `@` per sopprimere errori — si corregge la causa.

### Sicurezza input

Ogni valore da `$_GET`, `$_POST` o `$_COOKIE` viene sanificato prima dell'uso:
- ID numerici: cast `(int)`
- Stringhe in output HTML: `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- Stringhe nelle query: prepared statement con `bind_param`
- Password: mai in chiaro, mai con md5/sha1. Solo `password_hash()` e `password_verify()`
