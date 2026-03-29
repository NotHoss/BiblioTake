# Requisiti (formattati e raggruppati)

## HTML semantico e struttura

- Progetto HTML semantico: `header`, `footer`, `main`, `nav`, `aside`, `section`, `article`.
- Tag per identificare le keyword nel testo:
  - `<em>`: enfasi debole.
  - `<strong>`: enfasi forte.
- Intestazione:
  - Nell'`header` si mette `h1`.
  - Per ogni pagina il titolo deve essere `h2`, il resto poi a scalare.
  - Ogni pagina ha un titolo (`h2`) diverso, max 60 caratteri, conciso; mettere le keyword solo se ha senso.
- Aggiungere al testo attributi per identificare un formato di testo:
  - `abbr` per abbreviazioni, `date` per date, `lang` per lingua, `alt` per immagini, ecc.

## Link, pulsanti e testo

- Usare correttamente i pulsanti:
  - `button` quando si parla di form.
  - Se è un “pulsante per link”, usare il CSS per farlo sembrare un pulsante.
- Sottolineare solo i link.
- Come scrivere i numeri:
  - `20€` / `20 Euro` (no `20.00€`).
  - `10 milioni` (no `10000000`).
  - `40 su 100` / `4 su 10` (no `40%`).

## Tipografia

- Font:
  - Usare senza grazie e accessibile quando si visualizza su schermo.
  - Per la stampa usare con grazie.
- Interlinea almeno 1.5.

## Layout e responsive (CSS)

- Per impaginare usare per lo più `float`.
- Si può usare insieme anche `grid`, ma non fare `grid` annidate.
- Usare breakpoint per ridimensionare la pagina e renderla fluida:
  - Più BP = più fluida ma più file CSS.
  - Meno BP = meno fluida ma meno CSS.
- Menù intelligente in CSS mobile: mettere il menù in fondo, far caricare la pagina a scorrimento verticale dal fondo e poi il menu tramite ancore ti manda al punto richiesto. Una volta a fine pagina hai il menù a portata di mano.

## Accessibilità (AA)

- Standard doppia A.
- Mostrare sempre la posizione del focus.
- Dimensione minima degli elementi di interazione:
  - `24×24` su PC.
  - `44×30` su mobile.
- Le breadcrumbs sono obbligatorie per legge, servono per l'accessibilità.

## Immagini

- `alt` immagini tra 75 e 100 caratteri.
- `alt` vuoto per immagini di background, scritto in questo modo senza spazio: `alt=""`.
- Non usare troppe immagini: tagliarle nelle dimensioni corrette e usare formati compressi come `.jpg`.
  - Le `.gif` pesano troppo; se possibile sostituirle con animazioni con CSS (lente).
  - Le `.webp` vanno bene, ma quando sono poche.
- Usare image replace se si vuole sostituire un testo con un'immagine.
- Quando l'immagine è pesante ma non è necessaria la qualità si usa `.jpg`; il `.webp` è ottimo per la compressione ma è lento nel rendering.

## Colori e contrasto

- Se l'informazione è veicolata solo tramite colore deve essere rinforzata (grassetto, sottolineature, ecc.).
- Evitare riferimenti al colore nel testo (es. “come si vede nella scritta in rosso”).
- Rapporto di contrasto: `4.5:1` e `3:1` per testo grande (`>24pt`, `1em ~ 18pt`).
- Contrasto: chiaro/scuro, complementari, freddo/caldo, saturazione, stessa tinta e stessa saturazione ma luminosità diversa.
- Per gli errori non mettere solo il colore ma anche un simbolo es. `*` (per cose giuste si può mettere un tic).
- Palette di colori:
  - Color Universal Design Organisation.
  - Brian Suda contiene colori per persone con difficoltà, utile per La Stampa perché si traduce bene in toni di grigio.
- Colori diversi tra link visitato e non visitato: importante contrasto di entrambi col background; meglio se in contrasto elevato anche tra di loro.

## SEO: keyword e description

- Il singolare e plurale delle keyword si arrangia il browser.
- Le keyword aumentano la trustness, influenzano il ranking solo in alcuni motori di ricerca.
- La description è obbligatoria, ma le keyword sono un consiglio (circa 150 caratteri).
- Le keyword si usano come base per la description.
- Le keyword e la descrizione devono essere differenti e per ogni pagina.

## Note su heading e navigazione

- Filosofia di pensiero:
  - Avere un unico `h1` implica tutte le pagine hanno lo stesso `h1` e penalizza il ranking.
  - Avere un `h1` nell'header e uno nel main content migliora il ranking.
- Nel link della pagina home non si mette come link perché si creerebbe un link circolare.

## Semantica: `section` vs `article`

- Differenza section-article: `section` se cambia l'ordine è un problema, `article` non interessa l'ordine.
- Nel progetto della pallavolo si usa una lista perché si parla di giocatrici/giocatori tutti con la stessa struttura.

## Snippet/annotazioni CSS

- iniziare semre con *{padding: 0em;margin: 0em}
- `html, body {` → il `, body` può essere omesso perché serviva per vecchi browser.
- `font-family: "Lexend", Roboto, sans-serif;` → font accessibili: Roboto c’è sui sistemi operativi, invece Lexend no quindi lo importiamo da Google.
- `linear-gradient(to bottom right, rgb(22 63 119/100%), rgb(255, 255, 255));` → gradiente che parte da un colore al 100% e va al bianco con direzione da top left verso il bottom right.
- CSS in header: padding verticale `0em`, orizzontale `1em`.

## Strumenti per controllare il codice

- W3C (può dare falsi positivi).
- Total Validator (licenza completa in lab).
- WebAIM (non controlla la validità del codice).
- Lighthouse (solo per il CEO).
- Silktide (simula disabilità).
- NVDA (screen reader).
- ARC Toolkit.
- https://web.math.unipd.it/accessibility

## Sicurezza
- File get e post con password crittografate
- nei form (o cmq dove si inseriscono dati) bisogna mantenere la sicurezza contro attacchi hacker. Es. SQL injection ecc. 
