<?php 
    session_start(); 
    if(!isset($_SESSION["utente_id"])){
        header("Location: ../autenticazioni/login.php?redirect=profilo");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 10 - Classifica</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile_classifica.css">
</head>
<body>

    <div class="barra-superiore">
        <a href="../gioco/gioco.php" class="bottone-barra-sup bottone-homepage" title="Torna al Gioco"></a>
    </div>
        
    <div class="contenitore-classifica"> 
        <h2>🏆 Top 10 Giocatori 🏆</h2>
        
        <div>
            <table class="classifica-tabella">
                <thead>
                    <tr>
                        <th>Giocatore</th> 
                        <th>Mosse</th>
                        <th>Tempo</th>
                    </tr>
                </thead>
                <tbody id="bodyTabella">
                    </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 GameSAW. Tutti i diritti riservati.</p>
    </footer>   

    <script src="script_classifica.js"></script>
</body>
</html>