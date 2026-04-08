<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    $menu_utente = '';
    $bottone_gioco = '';

    if(isset($_SESSION["utente_id"])) {
        $menu_utente = '<li><span class="utenteLoggato">Ciao Gamer!</span></li> <li><a href="../autenticazioni/logout.php" class="bottoneRegistrazione">LOG OUT</a></li>';
        $bottone_gioco = '<a href="../gioco/gioco.php" class="bottoniCorpo">VAI AL GIOCO 🎮</a>';
    } else {
        $menu_utente = '<li><a href="../autenticazioni/login.php" class="bottoneLogin">ACCEDI</a></li> <li><a href="../autenticazioni/registrazione.php" class="bottoneRegistrazione">REGISTRATI</a></li>';
        $bottone_gioco = '<a href="../autenticazioni/login.php" class="bottoniCorpo">INIZIA A GIOCARE ORA 🎮</a>';
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="stile_homepage.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../cookie_banner/cookiebanner.style.css">

    <?php include '../cookie_banner/cookie-banner.php'; ?>

    <script>
        $(document).ready(function() {
            cookieBanner.init();
        });
    </script>
</head>
<body>

    <div class="barra-superiore">
        <div class="logo"></div>
        <nav>
            <ul>
                <li class="categorie"><a href="#chi-siamo">Chi Siamo</a></li>
                <li class="categorie"><a href="./underconstruction.html">Storia</a></li>
                <li class="categorie"><a href="./underconstruction.html">Servizi</a></li> 
                <li class="categorie"><a href="./underconstruction.html">App</li>
                
                <?php echo $menu_utente; ?>
            </ul>
        </nav>
    </div>   

    <section class="corpo">
        <h1>Benvenuti su MemInfo!</h1>
        <p>Il gioco del memory in versione informatica</p>

        <img src="../immagini/gioco.png" alt="esempio della schermata di gioco" class="immagine">
        
        <?php echo $bottone_gioco; ?>
    </section>

    <div class="contenitore">
        <div class="div-titolo">
            <h2 class="sezione-titolo">Cosa Facciamo</h2>
        </div>
        
        <div class="griglia-info">
            <div class="singola-info">
                <h3>💾 Il Gioco</h3>
                <p>Offriamo un'esperienza alternativa del gioco "Memory" implementato in versione informatica. </p>
            </div>
            <div class="singola-info">
                <h3>🏆 Classifiche</h3>
                <p>Registrati per salvare i tuoi punteggi e competere con altri utenti per scalare la classifica globale.</p>
            </div>
        </div>

        <div class="div-titolo">
            <h2 id="chi-siamo" class="sezione-titolo">Chi Siamo</h2>
        </div>
        
        <div class="info-centrale">
            <p>
                Siamo un team di sviluppatori appassionati di web gaming. <br>
                Abbiamo creato una versione alternativa del memory dedicata agli amanti dell'informatica.
            </p>
        </div>
    </div>

    <footer id="dove-siamo">
        <h3>Contatti & Dove Siamo</h3>
        <p>Indirizzo: Via dell'Università, 123 - Genova</p>
        <p>Email: memory@example.it | Tel: +39 06 1234567</p>
        <h3>Seguici Su</h3>
        <div class="social">
            <a href="./underconstruction.html" class="bottoni-social">
                <img src="../immagini/instagram.png" alt="logo di Instagram">
            </a>
            <a href="./underconstruction.html" class="bottoni-social">
                <img src="../immagini/facebook.png" alt="logo di Facebook">
            </a>
            <a href="./underconstruction.html" class="bottoni-social">
                <img src="../immagini/twitter.png" alt="logo di Twitter">
            </a>
            <a href="./underconstruction.html" class="bottoni-social">
                <img src="../immagini/tiktok.png" alt="logo di TikTok">
            </a>
            <a href="./underconstruction.html" class="bottoni-social">
                <img src="../immagini/youtube.png" alt="logo di YouTube">
            </a>
        </div>
        <p>&copy; 2023 GameSAW. Tutti i diritti riservati.</p>
    </footer>

</body>
</html>