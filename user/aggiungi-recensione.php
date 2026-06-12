<?php
require_once '../includes/resources.php';
requireRole('user');

$pageTitle = 'Recensione — User BiblioTake';
$currentPage = 'user';
//$message = '';
$errorMessage = '';

//Viene eseguito ogni volta che la pagina viene caricata con metodo POST, cioè quando un form HTML con method="POST" e action="user/commento.php" viene inviato.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {    //se la richiesta è di tipo POST, significa che l'utente ha inviato il form per aggiungere una recensione
    $libroId = isset($_POST['libro_id']) ? (int) $_POST['libro_id'] : 0;
    $testo = isset($_POST['testo']) ? trim($_POST['testo']) : '';   
    $voto = isset($_POST['valutazione']) ? (int) $_POST['valutazione'] : null;

    if ($libroId <= 0) {    //libroId non è valido
        header('Location: 400.php');  //stando a quello che mi ha detto l'IA è un errore 400 (Bad Request), quindi potremmo creare una pagina 400.php
        //header('Location: index.php?msg=' . urlencode('errore: Il libro selezionato non è valido.'));     //oppure possiamo fare una pagina di errore generica che mostra il messaggio passato come parametro, in questo caso "Il libro selezionato non è valido."
        exit;
    }

    $libro = getLibroById($conn, $libroId);     //se il libroId è valido ma non corrisponde a nessun libro esistente, reindirizza alla pagina 404
    if(!$libro){
        header('Location: 404.php');
        exit;
    }

    $risultato = aggiungiRecensione($conn, $_SESSION['user_id'], $libroId, $testo, $voto);

    if($risultato === true){header('Location: index.php?msg=recensione_aggiunta'); exit;} //se la recensione è stata aggiunta con successo, reindirizza alla pagina principale con un messaggio di successo
    else{$errorMessage = $risultato;} //se c'è stato un errore durante l'aggiunta della recensione, mostra il messaggio di errore nella stessa pagina (potrebbe essere utile se vogliamo restare sulla pagina del libro invece di tornare alla homepage)
    //else {header('Location: index.php?msg=' . urlencode('errore: ' . $risultato));} //se c'è stato un errore durante l'aggiunta della recensione, reindirizza alla pagina principale con un messaggio di errore che include il motivo dell'errore
    exit;
}
?>