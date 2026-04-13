# Prerequisiti tecnici — BiblioTake

Questo documento spiega tutto quello che serve sapere su HTML, CSS, JavaScript e PHP per lavorare a questo progetto. Non è un tutorial generico: ogni sezione è calibrata sui pattern effettivamente usati nel codice.

Leggi questo documento insieme a `convenzioni.md` e `struttura-progetto.md` prima di scrivere qualsiasi file.

---

## Indice

1. [HTML](#html)
2. [CSS](#css)
3. [JavaScript](#javascript)
4. [PHP](#php)

---

## HTML

### Struttura obbligatoria di ogni pagina

Ogni pagina del sito è generata da `views/template/header.php` + una view specifica + `views/template/footer.php`. L'HTML che ne risulta deve sempre rispettare questa struttura:

```html
<!DOCTYPE html>
<html lang="it" xml:lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Titolo pagina — BiblioTake</title>
    <meta name="description" content="Descrizione della pagina, circa 150 caratteri.">
    <meta name="keywords" content="parola1, parola2, parola3">
    <link rel="stylesheet" type="text/css" href="styles/resources.css">
</head>
<body>

<a href="#main-content" class="skip-link">Vai al contenuto</a>

<header>
    <div id="logo">...</div>
    <nav aria-label="Navigazione principale">...</nav>
</header>

<nav class="breadcrumb" aria-label="Percorso nel sito">...</nav>

<main id="main-content">
    <!-- contenuto della view -->
</main>

<footer>...</footer>

<script src="js/validation.js"></script>
</body>
</html>
```

Dettagli importanti:
- `lang="it"` e `xml:lang="it"` su `<html>` sono entrambi obbligatori.
- `<meta charset="UTF-8">` deve essere il primo tag dentro `<head>`.
- Il titolo usa la variabile PHP `$pageTitle` impostata dal modello.
- Lo skip link è il primo elemento dopo `<body>` — serve per gli screen reader.
- `<main id="main-content">` riceve il focus quando si attiva lo skip link.
- Il `<script>` va in fondo al `<body>`, non nell'`<head>`.

---

### Tag semantici

HTML5 mette a disposizione tag con significato. Usarli correttamente vale punti in valutazione e migliora l'accessibilità.

| Tag | Quando si usa |
|-----|---------------|
| `<header>` | Intestazione del sito (logo, nav). Una sola per pagina. |
| `<nav>` | Gruppo di link di navigazione. Usare `aria-label` per distinguerli se ce ne sono più di uno. |
| `<main>` | Contenuto principale della pagina. Una sola per pagina. |
| `<footer>` | Piè di pagina con info biblioteca e badge di validazione. |
| `<section>` | Sezione tematica della pagina. Deve avere un heading (`h2`–`h6`) al suo interno. |
| `<article>` | Contenuto autonomo e riutilizzabile (es. scheda libro, recensione). |
| `<aside>` | Contenuto correlato ma non indispensabile (es. filtri laterali del catalogo). |
| `<figure>` + `<figcaption>` | Immagine con didascalia (es. copertina libro con titolo). |

Tag vietati (sostituiti da CSS o da equivalenti semantici):

| Vietato | Alternativa corretta |
|---------|----------------------|
| `<b>` | `<strong>` (enfasi forte) |
| `<i>` | `<em>` (enfasi debole) |
| `<center>` | `text-align: center` in CSS |
| `<font>` | proprietà CSS `font-family`, `color`, `font-size` |
| `<br><br>` multipli | `margin-bottom` in CSS |

---

### Heading

- L'`<h1>` va nell'`<header>` del sito (logo o nome del sito).
- Il titolo di ogni pagina è un `<h2>` dentro `<main>`, diverso per ogni pagina, max 60 caratteri.
- I livelli successivi seguono in ordine: `<h3>` dentro `<h2>`, `<h4>` dentro `<h3>`, ecc. Non si salta mai un livello.
- `<section>` e `<article>` devono avere un heading al loro interno.

---

### Link e pulsanti

- I link (`<a>`) servono per navigare verso un'altra pagina o risorsa.
- I pulsanti (`<button>`) servono per azioni all'interno di un form.
- Non usare `<a href="#">` come placeholder — se non c'è una destinazione, usa `<span>` o testo semplice.
- Non usare `<button>` fuori da un form per fare navigazione — usa un `<a>` con stile CSS da pulsante.
- La pagina corrente nel menu di navigazione non ha un `<a>` (link circolare): si usa testo semplice con `aria-current="page"`.

```html
<!-- SBAGLIATO: link circolare -->
<li><a href="catalogo.php">Catalogo</a></li>  <!-- se siamo già su catalogo.php -->

<!-- CORRETTO -->
<li aria-current="page" class="current-page">Catalogo</li>
```

---

### Form

Ogni form deve avere:
- `action` con il file PHP che gestisce il POST.
- `method="post"` per dati sensibili o operazioni di scrittura; `method="get"` solo per ricerche e filtri.
- Ogni campo ha un `<label>` con `for` che corrisponde all'`id` del campo.
- I campi obbligatori hanno `required` e `aria-required="true"`.
- Un `<div role="alert">` vuoto per mostrare gli errori di validazione JS.

```html
<form action="login.php" method="post">
    <div>
        <label for="email">Indirizzo email</label>
        <input type="email" id="email" name="email"
               required aria-required="true" autocomplete="email">
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               required aria-required="true" autocomplete="current-password">
    </div>
    <div role="alert" id="form-errors" style="display:none"></div>
    <button type="submit">Accedi</button>
</form>
```

---

### Immagini

- Ogni `<img>` ha sempre: `src`, `alt`, `width`, `height`.
- `alt` descrittivo (75–100 caratteri) per immagini informative.
- `alt=""` (vuoto, senza spazio) per immagini decorative.
- Non scalare via CSS un'immagine ad alta risoluzione — tagliarla alle dimensioni di utilizzo prima di inserirla.
- Formati ammessi: `.jpg` per foto, `.png` per trasparenza, `.svg` per icone.

```html
<!-- Immagine informativa -->
<img src="images/copertina-42.jpg" alt="Copertina del libro Il Nome della Rosa di Umberto Eco"
     width="120" height="180">

<!-- Immagine decorativa -->
<img src="images/divider.png" alt="" width="800" height="2">
```

---

### Abbreviazioni, date, lingue

```html
<!-- Prima occorrenza di un'abbreviazione -->
<abbr title="International Standard Book Number">ISBN</abbr>

<!-- Date: il tag <time> con datetime machine-readable -->
<time datetime="2024-03-15">15 marzo 2024</time>

<!-- Parole in lingua straniera -->
<span lang="en">Home</span>
<span lang="la">et al.</span>
```

---

### Tabelle

Le tabelle si usano solo per dati tabulari, mai per il layout.

```html
<table>
    <caption>Prestiti attivi dell'utente</caption>
    <thead>
        <tr>
            <th scope="col">Libro</th>
            <th scope="col">Data inizio</th>
            <th scope="col">Data fine</th>
            <th scope="col">Stato</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Il Nome della Rosa</td>
            <td><time datetime="2024-03-01">1 marzo 2024</time></td>
            <td><time datetime="2024-03-31">31 marzo 2024</time></td>
            <td>Attivo</td>
        </tr>
    </tbody>
</table>
```

`scope="col"` sulle intestazioni di colonna, `scope="row"` sulle intestazioni di riga.

---

### Breadcrumb

Obbligatoria su tutte le pagine tranne la homepage. L'ultimo elemento non è un link.

```html
<nav class="breadcrumb" aria-label="Percorso nel sito">
    <ul>
        <li><a href="index.php" lang="en">Home</a></li>
        <li><a href="catalogo.php">Catalogo</a></li>
        <li><span aria-current="page">Il Nome della Rosa</span></li>
    </ul>
</nav>
```

---

### Numeri e unità

- Prezzi e quantità: `20 Euro` o `20€`, non `20.00€`.
- Grandi numeri: `10 milioni`, non `10000000`.
- Percentuali: `40 su 100` o `4 su 10`, non `40%`.
- Informazioni veicolate solo dal colore devono avere un rinforzo testuale o simbolico (es. asterisco per errore, spunta per successo).

---

## CSS

### Tre file, sempre presenti

Il progetto usa tre file CSS separati per responsabilità:

| File | Contenuto |
|------|-----------|
| `general.css` | Reset, tipografia, colori, elementi globali (`body`, `a`, `h1`–`h6`, tabelle, form) |
| `layout.css` | Struttura delle pagine: header, nav, main, footer, colonne, media query |
| `print.css` | Stile di stampa: nasconde nav e form, imposta font serif, colori neutri |

I modelli includono solo `styles/resources.css`, che aggrega i tre con `@import`:

```css
@import url('css/general.css');
@import url('css/layout.css');
@import url('css/print.css');
```

Non si includono i singoli CSS direttamente nell'HTML.

---

### Reset obbligatorio

Il primo blocco di `general.css` azzera i margini di default del browser:

```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
```

`box-sizing: border-box` fa sì che `width` e `height` includano padding e border — evita calcoli manuali nelle griglie.

---

### Tipografia

```css
body {
    font-family: "Lexend", Roboto, sans-serif;
    font-size: 16px;
    line-height: 1.6;  /* minimo 1.5 per accessibilità */
    color: #333;
}

/* Font con grazie per la stampa */
@media print {
    body { font-family: Georgia, "Times New Roman", serif; }
}
```

- Per lo schermo: font senza grazie e accessibile (Lexend o Roboto).
- Per la stampa: font con grazie.
- Interlinea minima 1.5.
- Solo i link sono sottolineati — non sottolineare altro testo.

---

### Layout con float

Il progetto usa `float` per impaginare colonne, non `flexbox` né `grid` annidato (compatibilità con browser più datati richiesta dal corso).

```css
/* Contenitore */
.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
}

/* Due colonne: contenuto + sidebar */
.main-content {
    float: left;
    width: 70%;
}

.sidebar {
    float: left;
    width: 30%;
}

/* Clearfix: necessario sul contenitore dopo elementi floatati */
.container::after {
    content: "";
    display: table;
    clear: both;
}
```

Regola: dopo un float, il contenitore padre collassa. Il clearfix (`::after` con `clear: both`) lo risolve.

---

### Media query

Le media query stanno dentro i file CSS, **mai** come attributo `media=""` nel tag `<link>` HTML.

```css
/* Desktop (default) */
.main-content { width: 70%; float: left; }
.sidebar       { width: 30%; float: left; }

/* Tablet */
@media screen and (max-width: 1024px) {
    .main-content { width: 100%; float: none; }
    .sidebar       { width: 100%; float: none; }
}

/* Mobile */
@media screen and (max-width: 768px) {
    body { font-size: 14px; }
    nav ul { /* menu verticale */ }
}
```

---

### Stile di stampa

`print.css` è obbligatorio. Nasconde gli elementi non utili su carta e imposta il testo per la stampa:

```css
@media print {
    nav,
    footer,
    .skip-link,
    .breadcrumb,
    button,
    form { display: none; }

    body {
        font-family: Georgia, serif;
        font-size: 12pt;
        color: #000;
        background: #fff;
    }

    a::after {
        content: " (" attr(href) ")";
        font-size: 10pt;
    }
}
```

---

### Skip link

Il primo elemento dopo `<body>` è lo skip link, visibile solo quando riceve il focus:

```css
.skip-link {
    position: absolute;
    top: -40px;
    left: 0;
    background: #000;
    color: #fff;
    padding: 8px 16px;
    z-index: 999;
    text-decoration: none;
}

.skip-link:focus {
    top: 0;
}
```

---

### Classe screen-reader-only

Usata per testo visibile agli screen reader ma nascosto visivamente:

```css
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
```

---

### Contrasto colori

- Testo normale: rapporto minimo 4.5:1 rispetto allo sfondo (WCAG AA).
- Testo grande (maggiore di 24px o 18px grassetto): rapporto minimo 3:1.
- Link visitato e non visitato devono avere colori diversi, entrambi con contrasto sufficiente rispetto allo sfondo.
- Verificare sempre con WebAIM Contrast Checker prima della consegna.

---

### Versioni minificate

Prima della consegna, ogni file CSS in `styles/css/` va minificato e salvato in `styles/min/` con suffisso `.min.css`. Si usa uno strumento online (cssnano, CSS Minifier). Si consegnano entrambe le versioni: quella leggibile e quella minificata.

---

### Niente framework

Bootstrap, Tailwind, Foundation e qualsiasi altro framework CSS sono vietati. Tutto lo stile è scritto a mano. Questo è un requisito esplicito del corso.

---

## JavaScript

### Ruolo del JavaScript in questo progetto

JavaScript ha un solo compito: **duplicare lato client le validazioni già presenti nel PHP**. Non aggiunge logica applicativa, non fa chiamate HTTP, non manipola il DOM per mostrare o nascondere sezioni di pagina.

Se JavaScript è disattivato, il sito deve funzionare normalmente — l'unica differenza è che la validazione avviene solo lato server.

---

### Struttura di `validation.js`

Il file usa un unico pattern: al caricamento della pagina, cerca il form; se esiste, aggiunge un listener sul submit.

```javascript
document.addEventListener('DOMContentLoaded', function() {

    // Guard: se il form non esiste in questa pagina, esci subito.
    // Questo permette di includere lo stesso file su tutte le pagine.
    var form = document.getElementById('login-form');
    if (!form) return;

    form.addEventListener('submit', function(event) {
        var errors = [];

        var email = document.getElementById('email').value.trim();
        if (email === '' || email.indexOf('@') === -1) {
            errors.push('Inserisci un indirizzo email valido.');
        }

        var password = document.getElementById('password').value;
        if (password.length < 8) {
            errors.push('La password deve contenere almeno 8 caratteri.');
        }

        if (errors.length > 0) {
            event.preventDefault();  // blocca l'invio del form
            var errorDiv = document.getElementById('form-errors');
            errorDiv.innerHTML = '<ul><li>' + errors.join('</li><li>') + '</li></ul>';
            errorDiv.setAttribute('style', 'display:block');
        }
    });
});
```

Punti chiave:
- `document.addEventListener('DOMContentLoaded', ...)` — il codice parte solo dopo che il DOM è pronto.
- Il guard `if (!form) return` — permette di includere il file su ogni pagina senza errori alla console quando il form non c'è.
- `event.preventDefault()` — blocca l'invio del form se ci sono errori.
- `errorDiv.setAttribute('style', 'display:block')` — mostra il div degli errori (di default nascosto con `style="display:none"` nell'HTML).

---

### Regole JavaScript

- Solo vanilla JS — niente jQuery, niente librerie esterne.
- Nessun `onclick`, `onsubmit` o altro event handler inline nell'HTML. Tutti i listener si aggiungono via JavaScript nel file separato.
- Nessuna animazione o effetto decorativo via JavaScript — se serve un'animazione, si usa CSS.
- `var` invece di `let` e `const` (compatibilità con browser datati richiesta dal corso).

---

### Tag noscript

Ogni pagina con un form deve avere un `<noscript>` che avvisa l'utente:

```html
<noscript>
    <p class="warning">
        JavaScript è disattivato. La validazione dei dati avverrà solo lato server.
    </p>
</noscript>
```

---

## PHP

### Pattern Model-View — la regola più importante

Ogni file PHP ha un ruolo preciso. Non si mescolano mai logica e HTML.

**Modello** (file nella root o in `admin/`):
- Carica le dipendenze.
- Controlla la sessione.
- Legge i dati da `$_GET` e `$_POST`.
- Esegue query tramite le funzioni in `includes/functions/`.
- Include header, view e footer nell'ordine corretto.
- Non contiene HTML (tranne le tre chiamate `require_once`).

**View** (file in `views/`):
- Riceve variabili già pronte dal modello.
- Stampa HTML.
- Non esegue query.
- Non legge `$_GET`, `$_POST`, `$_SESSION`.
- Non chiama `header()` o `exit`.

Schema di ogni modello:

```php
<?php
// 1. Carica tutte le dipendenze
require_once 'includes/resources.php';

// 2. Controllo accesso (solo nelle pagine protette)
requireLogin();           // redirect a login.php se non autenticato
// oppure:
requireRole('admin');     // redirect a 403.php se non admin

// 3. Variabili di pagina (usate dal template)
$pageTitle       = 'I miei prestiti — BiblioTake';
$pageDescription = 'Visualizza i tuoi prestiti attivi e lo storico.';
$pageKeywords    = 'prestiti, libri, biblioteca, storico';
$currentPage     = 'prestiti';

// 4. Logica PHP: lettura input, query, preparazione dati
$utenteId       = (int) $_SESSION['user_id'];
$prestitiAttivi = getPrestitiAttivi($conn, $utenteId);
$storico        = getStoricoPrestiti($conn, $utenteId);

// 5. Composizione della pagina — sempre in questo ordine
require_once 'views/template/header.php';
require_once 'views/showPrestiti.php';
require_once 'views/template/footer.php';
?>
```

---

### `require_once` ovunque

Sempre `require_once`, mai `include`, mai `require` semplice.

- `include`: se il file manca, stampa un warning e continua — comportamento silenzioso e pericoloso.
- `require`: blocca l'esecuzione se il file manca, ma non controlla i duplicati.
- `require_once`: blocca l'esecuzione se il file manca **e** garantisce che non venga incluso due volte.

---

### `includes/resources.php` — l'unico file da includere

I modelli includono un solo file: `includes/resources.php`. Questo file si occupa di includere tutto il resto nell'ordine corretto e di aprire la connessione al database.

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

`__DIR__` è la directory del file corrente — il percorso funziona correttamente sia dai modelli nella root che da quelli in `admin/`.

---

### Sessioni

`session_start()` deve essere chiamato **prima di qualsiasi output** (anche prima di uno spazio bianco fuori dal tag `<?php ?>`).

```php
<?php
session_start();

// Login: salva dati in sessione
$_SESSION['user_id']   = $row['id'];
$_SESSION['user_role'] = $row['ruolo'];

// Lettura dati sessione
$utenteId = (int) $_SESSION['user_id'];
$ruolo    = $_SESSION['user_role'];

// Logout
session_unset();
session_destroy();
header('Location: index.php');
exit;
?>
```

---

### Sanitizzazione dell'input

Ogni valore proveniente dall'esterno va trattato come non affidabile prima dell'uso.

```php
// ID numerici: cast a intero — elimina qualsiasi carattere non numerico
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Stringhe in output HTML: htmlspecialchars converte < > " ' & in entità HTML
$titolo = isset($_POST['titolo'])
    ? htmlspecialchars(trim($_POST['titolo']), ENT_QUOTES, 'UTF-8')
    : '';

// Validazione server-side: sempre, anche se c'è già la validazione JS
$errors = [];

if ($titolo === '' || strlen($titolo) > 255) {
    $errors[] = 'Il titolo non è valido.';
}

if (!empty($errors)) {
    // gestisci errori: mostrali nella view o redirect
}
```

---

### Prepared statements

Qualsiasi variabile che arriva dall'esterno (utente, form, URL) non va mai concatenata in una query SQL. Si usano i prepared statements.

```php
// SBAGLIATO — vulnerabile a SQL injection
$result = $conn->query("SELECT * FROM utente WHERE email = '$email'");

// CORRETTO — prepared statement
$stmt = $conn->prepare('SELECT * FROM utente WHERE email = ? AND attivo = 1');
$stmt->bind_param('s', $email);   // 's' = string, 'i' = integer, 'd' = double
$stmt->execute();
$result = $stmt->get_result();
$utente = $result->fetch_assoc();
$stmt->close();
```

Tipi per `bind_param`:
- `'s'` — stringa
- `'i'` — intero
- `'d'` — decimale
- `'b'` — BLOB

Se ci sono più parametri si concatenano: `'si'` per una stringa e un intero.

---

### Password

Le password non si salvano mai in chiaro. Non si usano `md5()` o `sha1()`. Si usa solo `password_hash()` con bcrypt.

```php
// Registrazione: salva l'hash nel DB
$hash = password_hash($password, PASSWORD_BCRYPT);
// $hash è una stringa di 60 caratteri da salvare nella colonna password

// Login: verifica la password inserita rispetto all'hash nel DB
if (password_verify($passwordInserita, $hashDalDb)) {
    // credenziali corrette — avvia sessione
} else {
    // credenziali errate
}
```

---

### Connessione al database

La connessione viene aperta una sola volta in `includes/functions/db.php` tramite `getConnection()` e salvata in `$conn`. Tutti i modelli usano questa variabile. Nessuna funzione usa variabili globali per la connessione: `$conn` viene passato esplicitamente come primo parametro.

```php
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log('Connessione fallita: ' . $conn->connect_error);
        header('Location: 500.php');
        exit;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
```

---

### Redirect e operazioni POST

I modelli che gestiscono solo operazioni POST (es. `richiedi-prestito.php`, `elimina-recensione.php`) non includono una view. Al termine dell'operazione eseguono sempre un redirect:

```php
<?php
require_once 'includes/resources.php';
requireLogin();

$libroId = isset($_POST['libro_id']) ? (int) $_POST['libro_id'] : 0;

if ($libroId > 0 && isLibroDisponibile($conn, $libroId)) {
    richiediPrestito($conn, (int) $_SESSION['user_id'], $libroId);
    header('Location: prestiti.php?success=1');
} else {
    header('Location: dettaglio-libro.php?id=' . $libroId . '&error=1');
}
exit;  // sempre dopo header('Location: ...')
?>
```

`exit` dopo `header('Location: ...)` è obbligatorio — senza di esso l'esecuzione continua anche dopo il redirect.

---

### Funzioni: regole

- Ogni funzione riceve `$conn` come primo parametro — non usa variabili globali.
- Ogni funzione restituisce dati (array, bool, null) — non stampa HTML, non fa `echo`.
- Nessuna funzione chiama `header()` o `exit` tranne quelle in `auth.php`.
- Ogni file in `includes/functions/` copre un solo dominio (auth, libro, prestito, recensione, admin).

```php
// SBAGLIATO: funzione che stampa HTML
function mostraLibro($conn, $id) {
    $libro = ...;
    echo '<h2>' . $libro['titolo'] . '</h2>';  // vietato
}

// CORRETTO: funzione che restituisce dati
function getLibroById($conn, $id) {
    $stmt = $conn->prepare('SELECT * FROM libro WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $libro  = $result->fetch_assoc();
    $stmt->close();
    return $libro;  // array o null
}
```

---

### Pagine di errore

Le pagine di errore impostano il codice HTTP corretto prima di qualsiasi output:

```php
<?php
http_response_code(404);
require_once 'includes/resources.php';
$pageTitle       = 'Pagina non trovata — BiblioTake';
$pageDescription = 'La pagina richiesta non esiste.';
$currentPage     = '';
require_once 'views/template/header.php';
require_once 'views/show404.php';
require_once 'views/template/footer.php';
?>
```

---

### Errori PHP

In sviluppo, tutti gli errori sono visibili:

```php
// includes/config.php — sviluppo
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

Prima della consegna, disattivare la visualizzazione degli errori (ma non il log):

```php
// produzione
error_reporting(0);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
```

Non usare mai `@` per sopprimere errori. Se un errore appare, si trova la causa e si corregge.
