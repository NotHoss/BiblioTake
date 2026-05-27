<?php
session_start();
require_once '../includes/resources.php';
//require_once '../includes/functions/user_functions.php';
requireRole('utente');

$pageTitle = 'Profilo | BiblioTake';
$currentPage = 'dashboard';
$errorMessage = '';
$successMessage = '';

$userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0  ;
if ($conn instanceof mysqli && $errorMessage === '' && $userId > 0) {
    $userInfo = getUserInfo($conn, $userId);
}
else {
    $errorMessage = 'Connessione al database non disponibile.';
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && $conn instanceof mysqli && $errorMessage === '' && $userId > 0) {
    if(isset($_FILES['new_profile_image']) && $_FILES['new_profile_image']['error'] === UPLOAD_ERR_OK){
        $newProfileImage = 'images/profili/' . $userId . '.jpg';
        move_uploaded_file($_FILES['new_profile_image']['tmp_name'], __DIR__ . '/../' . $newProfileImage);
        $updatedProfileImage = changeFotoProfilo($conn, $userInfo['id'], $newProfileImage);
        if($updatedProfileImage === true){
            $userInfo['foto_profilo'] = $newProfileImage; // Aggiorna l'informazione dell'immagine del profilo nell'array $userInfo
            $successMessage = 'Immagine del profilo aggiornata con successo.';
            header('Location: /user/dashboard.php');
            exit();
        }
        else{
            $errorMessage = is_string($updatedProfileImage) ? $updatedProfileImage : 'Si è verificato un errore durante l\'aggiornamento dell\'immagine del profilo.';
        }
        
    }

    if(isset($_POST['new_username']) && $_POST['new_username'] !== $userInfo['username']){
        $newUsername = $_POST['new_username'];
        $result = changeUsername($conn, $userInfo['id'], $newUsername);
        if($result === true){
            $successMessage = 'Username aggiornato con successo.';
            header('Location: /user/dashboard.php');
            exit();
        }
        else{
            $errorMessage = is_string($result) ? $result : 'Si è verificato un errore durante l\'aggiornamento dell\'username.';
        }
    }

    if(isset($_POST['new_email']) && $_POST['new_email'] !== $userInfo['email']){
        $newEmail = $_POST['new_email'];
        $result = changeEmail($conn, $userInfo['id'], $newEmail);
        if($result === true){
            $successMessage = 'Email aggiornata con successo.';
            header('Location: /user/dashboard.php');
            exit();
        }
        else{
            $errorMessage = is_string($result) ? $result : 'Si è verificato un errore durante l\'aggiornamento dell\'email.';
        }
    }

    if(!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_new_password'])){
        $stmt = $conn->prepare("SELECT password FROM utente WHERE id = ?");
        if (!$stmt) {throw new RuntimeException("Errore prepare SQL: " . $conn->error);}            //errore inizializzazione query
        if (!$stmt->bind_param('i', $userId)) {throw new RuntimeException("Errore bind_param");}    //errore bind parametri
        if (!$stmt->execute()) {throw new RuntimeException("Errore esecuzione query");}             //errore esecuzione query
        $passwordDB = $stmt->get_result();
        if (!$passwordDB) {throw new RuntimeException("Errore recupero risultati");}

        $passwordDB = $passwordDB->fetch_assoc();
        $passwordDB = $passwordDB['password'] ?? '';

        if(!password_verify($_POST['current_password'], $passwordDB)){
            $errorMessage = 'La password attuale non è corretta.';
        }
        else{
            if($_POST['new_password'] === $_POST['confirm_new_password']){
                $result = changePassword($conn, $userInfo['id'], $_POST['confirm_new_password']);
                if($result === true){
                    $successMessage = 'Password aggiornata con successo.';
                    header('Location: /user/dashboard.php');
                    exit();
                }
                else{
                    $errorMessage = is_string($result) ? $result : 'Si è verificato un errore durante l\'aggiornamento della password.';
                }
            } 
            else{$errorMessage = 'Le nuove password non corrispondono.';}
        }
        $stmt->close();
    }
}

require_once __DIR__ . '/../views/template/header.php';
require_once __DIR__ . '/../views/showModificaUser.php';
require_once __DIR__ . '/../views/template/footer.php';

if ($conn instanceof mysqli) {
    $conn->close();
}

?>