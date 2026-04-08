<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');
    
    if(!isset($_SESSION["utente_id"])){
        header("Location: ../autenticazioni/login.php?redirect=profilo");
        exit();
    }
    
    $utenteId = $_SESSION["utente_id"];

    $sql = "SELECT * FROM registrazione WHERE Utente_Id = $utenteId";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    
    $res->free();
    $conn->close();

    function mostraDato($valore) {
        if (isset($valore) && $valore !== "") {
            return $valore;
        } else {
            return '<span class="non-specificato">Non specificato</span>';
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile_credenziali.css">
</head>
<body>

    <div class="contenitore-principale">
        <div class="sezione-form">
            <h2>Profilo</h2>
            
            <span class="label">Nome</span>
            <div class="label-valore">
                <?php echo mostraDato(htmlspecialchars($row['Nome'])); ?>
            </div>

            <span class="label">Cognome</span>
            <div class="label-valore">
                <?php echo mostraDato(htmlspecialchars($row['Cognome'])); ?>
            </div>

            <span class="label">Email</span>
            <div class="label-valore">
                <?php echo mostraDato(htmlspecialchars($row['Email'])); ?>
            </div>

            <span class="label">Telefono</span>
            <div class="label-valore">
                <?php echo mostraDato(htmlspecialchars($row['Telefono'])); ?>
            </div>

            <span class="label">Provincia</span>
            <div class="label-valore">
                <?php echo mostraDato(htmlspecialchars($row['Provincia'])); ?>
            </div>

            <span class="label">Data di Nascita</span>
            <div class="label-valore">
                <?php 
                    if (!empty($row['DataNascita'])) {
                        echo date("d-m-Y", strtotime(htmlspecialchars($row['DataNascita']))); 
                    } else {
                        echo '<span class="non-specificato">Non specificato</span>';
                    }
                ?>
            </div>

            <span class="label">Genere</span>
            <div class="label-valore">
                <?php echo mostraDato(htmlspecialchars($row['Genere'])); ?>
            </div>

            <span class="label">Nickname</span>
            <div class="label-valore">
                <?php echo mostraDato(htmlspecialchars($row['Nickname'])); ?>
            </div>

            <div class="contenitore-bottoni">
                <a href="../gioco/gioco.php" style="flex: 1;">
                    <button class="bottoni-azioni bottone">TORNA AL GIOCO</button>
                </a>
                
                <a href="modifica-profilo.php" style="flex: 1;">
                    <button class="bottoni-azioni bottone-logout">MODIFICA</button>
                </a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 GameSAW. Tutti i diritti riservati.</p>
    </footer>
</body>
</html>