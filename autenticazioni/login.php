<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    $redirect_destinazione = "";
    if(isset($_GET['redirect'])) {
        $redirect_destinazione = $_GET['redirect'];
    }
   
    if(isset($_POST['redirect_target'])) {
        $redirect_destinazione = $_POST['redirect_target'];
    }
    
    if(isset($_SESSION["utente_id"]) && $redirect_destinazione == ""){
        header("Location: ../gioco/gioco.php");
        exit();
    }

    $errori = array();

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])){
        $credenziale = trim($_POST["credenziale"]);
        $password = $_POST["password"];

        if(empty($credenziale) || empty($password)){
            array_push($errori, "Completa tutti i campi");
        } else {
            $stmt = $conn->prepare("SELECT Utente_Id, Password, Ruolo, Nickname, Email FROM registrazione WHERE Email = ? OR Nickname = ?");
            $stmt->bind_param("ss", $credenziale, $credenziale);
            
            if($stmt->execute()){
                $res = $stmt->get_result();
                
                if($row = $res->fetch_assoc()){
                    if(password_verify($password, $row['Password'])){

                        $_SESSION["nickname"] = $row["Nickname"];
                        $_SESSION["utente_id"] = $row["Utente_Id"];
                        $_SESSION["ruolo"] = $row["Ruolo"];
                        $_SESSION["email"] = $row["Email"];

                        if($redirect_destinazione == "profilo") {
                            header("Location: ../autenticazioni/profilo.php");
                        } elseif($redirect_destinazione == "blog") {
                            header("Location: ../blog/mostra_post.php");
                        } else {
                            header("Location: ../gioco/gioco.php");
                        }
                        exit();
                    } else {
                        array_push($errori, "Non è possibile effettuare l'accesso con queste credenziali"); 
                    }
                } else {
                    array_push($errori, "Non è possibile effettuare l'accesso con queste credenziali");
                }
            } else {
                array_push($errori, "Errore nel login. Riprova più tardi.");
            }
            $stmt->close();
        }
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel = "stylesheet" href="stile_credenziali.css">
</head>

<body>
    <div class="contenitore-principale">
        <div class="sezione-form" id="login-form">
            <form action="login.php" method ="POST" novalidate>
                <h2>Login</h2> 

                <input type="hidden" name="redirect_target" value="<?php echo htmlspecialchars($redirect_destinazione); ?>">
                <?php
                    if(isset($_GET['registrato']) && $_GET['registrato'] == 'true'){
                        echo "<div class='messaggio messaggio-successo'>Registrazione completata con successo! Ora puoi accedere.</div>";
                    }
                    if(count($errori) > 0){
                        foreach($errori as $errore){
                            echo "<div class='messaggio messaggio-errore'>" . $errore . "</div>";
                        }
                    }
                ?>
                <div class="elemento-form">
                    <input type="email" class="campo-form" name="credenziale" placeholder="Email o Nickname">
                </div> 
                <div class="elemento-form pos-icona">
                    <input type="password" id="password" class="campo-form" name="password" placeholder="Password">
                    <img src="../immagini/occhioaperto.png" class="icona-password" alt="Mostra Password">
                </div> 
                
                <div class="elemento-form">
                    <input type="submit" name="login" value="Login">
                </div>
                
                <p>Non sei ancora registrato? <a href="registrazione.php">Registrati</a></p>
            </form>
        </div>
    </div>
    <script src="script_password.js"></script>

</body>
</html>