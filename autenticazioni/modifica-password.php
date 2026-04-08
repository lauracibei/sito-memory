<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    if(!isset($_SESSION["utente_id"])){
        header("Location: login.php");
        exit();
    }

    $errori = array();
    $messaggioSuccesso = "";
    $utenteId = $_SESSION["utente_id"];

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){

        $passwordAttuale = $_POST["password-attuale"];
        $password = $_POST["nuova-password"];
        $confermaPassword = $_POST["nuova-password-conferma"];

        if(empty($passwordAttuale) OR empty($password) OR empty($confermaPassword)){
            array_push($errori, "Completa tutti i campi");
        }

        $sql = "SELECT Password FROM registrazione WHERE Utente_Id = '$utenteId'";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();

        if(password_verify($passwordAttuale, $row["Password"])){
            if($password === $passwordAttuale){
                array_push($errori, "Inserisci una password diversa da quella attuale");
            }
            if(strlen($password) < 8){
                array_push($errori, "La password deve contenere almeno 8 caratteri");
            }
            if($password !== $confermaPassword){
                array_push($errori, "Password di conferma diversa da quella nuova");
            }

            $res->free(); 

            if(count($errori) == 0){
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $sql = "UPDATE registrazione SET Password = ? WHERE Utente_Id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $passwordHash, $utenteId);
                $stmt->execute();
                if($stmt->affected_rows == 1){ 
                    $messaggioSuccesso = "Password modificata!";
                }
                $stmt->close(); 
            }
        }
        else if (!empty($passwordAttuale)){
            array_push($errori, "Password attuale non corretta");
            $res->free();
        }
        
        $conn->close();
    }

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel = "stylesheet" href="stile_credenziali.css">
</head>

<body>
    <div class="contenitore-principale">
        <div class="sezione-form">
            <form action="modifica-password.php" method="POST">
                <h2>Modifica password</h2> 

                <?php
                    if(count($errori) > 0){
                        foreach($errori as $errore){
                            echo "<div class='messaggio messaggio-errore'>" . $errore . "</div>";
                        }
                    }
                    if($messaggioSuccesso != ""){
                        echo "<div class='messaggio messaggio-successo'>" . $messaggioSuccesso . "</div>";
                    }
                ?>
                
                <span class="label">Password attuale</span>
                <div class="elemento-form">
                    <input type="password" class="campo-form" name="password-attuale">
                </div> 

                <span class="label">Nuova password</span>
                <div class="elemento-form">
                    <input type="password" class="campo-form" name="nuova-password">
                </div> 

                <span class="label">Conferma la tua nuova password</span>
                <div class="elemento-form">
                    <input type="password" class="campo-form" name="nuova-password-conferma">
                </div> 
                
                <div class="elemento-form">
                    <input type="submit" name="submit" value="Cambia password">
                </div>

                <p><a href="modifica-profilo.php">Torna indietro</a></p>

            </form>
        </div>
    </div>

</body>
</html>