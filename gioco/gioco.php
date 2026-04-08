<?php
    session_start();
    if(!isset($_SESSION["utente_id"])){
        header("Location: ../autenticazioni/login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Informatico</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile_gioco.css">
</head>
<body>

    <div id="barraSuperiore" class="barra-superiore">
        <div class="logo"></div>
        <a href="../homepage/homepage.php" class="bottone-barra bottone-homepage"></a>
        <button id="bottoneRegole" class="bottone-barra bottone-regole"></button>
        <button id="bottoneDarkmode" class="bottone-barra bottone-darkmode"></button>
        <a href="../classifica/classifica.php" class="bottone-barra bottone-classifica"></a>
        <a href="../blog/mostra_post.php" class="bottone-barra bottone-blog"></a>
        <a href="../autenticazioni/profilo.php" class="bottone-barra bottone-profilo"></a>
        <a href="../autenticazioni/logout.php" class="bottone-logout">LOG OUT</a>
    </div>

    <div id="contenitorePrincipale" class="contenitore-principale">
        <h1>Memory Informatico</h1>

        <div class="info-gioco">
            <div class="riquadro-info">
                <span>Cicli di CPU:</span> <span id="mosse">0</span>
            </div>
            <div class="riquadro-info">
                <span>Latenza: </span> <span id="tempo">00:00</span> 
                <button id="bottonePausa" class="bottone-pausa">⏸️</button>
            </div>
        </div>

        <div id="tavolo-gioco">
            <div class="carta"></div>
            <div class="carta"></div>
            <div class="carta"></div>
            <div class="carta"></div>
            
            <div class="carta"></div>
            <div class="carta"></div>
            <div class="carta"></div>
            <div class="carta"></div>
            
            <div class="carta"></div>
            <div class="carta"></div>
            <div class="carta"></div>
            <div class="carta"></div>
            
            <div class="carta"></div>
            <div class="carta"></div>
            <div class="carta"></div>
            <div class="carta"></div>
        </div>

        <input type="button" id="reset" value="NUOVA PARTITA">  
    </div>

    <div id="regoleModale" class="modale">
        <div class="contenuto-modale">
            <span class="chiusura-modale">&times;</span>
            <h2>🖥️ Protocollo di Recupero Dati</h2>
            <div class="testo-regole">
                <p><strong>Stato del Sistema:</strong> La RAM è frammentata.</p>
                <p><strong>Obiettivo:</strong> Ripristina il database trovando le coppie di icone corrispondenti.</p>
                <ul>
                    <li>Clicca su un blocco per decodificarlo.</li>
                    <li>Se i dati coincidono (✅), il blocco è stabilizzato.</li>
                    <li>Se i dati sono diversi (❌), verranno nascosti nuovamente.</li>
                </ul>
                <p><em>Ottimizza i cicli di CPU (Mosse) e riduci la Latenza (Tempo).</em></p>
                <p class="footer-regole"><strong>Buon Debugging!</strong></p>
            </div>
        </div>
    </div>

    <div id="modaleVittoria" class="modale">
        <div class="contenuto-modale contenuto-vittoria">
            <h2>🏆 Missione Compiuta!</h2>
            
            <p id="messaggioVittoria"></p>
            
            <button id="bottoneRestart" class="bottone-restart">Riavvia Sistema</button>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 GameSAW. Tutti i diritti riservati.</p>
    </footer>

    <script src="script_gioco.js"></script>

</body>
</html>