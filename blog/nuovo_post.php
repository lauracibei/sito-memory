<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    $messaggio = "";

    if(!isset($_SESSION["utente_id"])){
        header("Location: ../autenticazioni/login.php");
        exit();
    }
    
    if(isset($_SESSION["ruolo"]) && $_SESSION["ruolo"] != "autore"){
        header("Location: ../gioco/gioco.php");
        exit();
    }

    if(isset($_POST["submit"])){
        $titolo = $_POST["titolo"];
        $contenuto = $_POST["contenuto"];

        $nome_img = "";
        if(isset($_FILES["immagine"]) && !empty($_FILES["immagine"]["name"])){
            $nome_img = $_FILES["immagine"]["name"];
            $temp_loc = $_FILES["immagine"]["tmp_name"];
            $mia_loc = "immagini/";
            move_uploaded_file($temp_loc, $mia_loc.$nome_img);
        }

        $nickname = $_SESSION["nickname"];
        $stmt = $conn->prepare("INSERT INTO posts (Titolo, Contenuto, Autore_nickname, Immagine) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $titolo, $contenuto, $nickname, $nome_img);
        
        if($stmt->execute()){
            header("Location: mostra_post.php"); 
            exit();
        } else {
            $messaggio = "<div class='contenitore-messaggio errore'>Errore nell'inserimento del nuovo post</div>";
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Post</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile_blog.css">
    <script src="https://cdn.tiny.cloud/1/q7e58ddi1h89wz8xrj00p8bzbf3dqmwapinwx0vjgc9bi3gv/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: 'textarea',
        skin: 'oxide-dark',
        content_css: 'dark'
      });
    </script>
</head>
<body>

    <div class="barra-superiore">
        <a href="mostra_post.php" class="bottone-barra-sup bottone-homeblog">TORNA AI POST</a>
    </div>

    <div class="contenitore">
        <h1>Scrivi un nuovo post</h1>
        
        <?php echo $messaggio; ?>

        <div class="contenitore-post">
            <form action="nuovo_post.php" method="POST" enctype="multipart/form-data">
                
                <label>Titolo:</label>
                <input type="text" name="titolo" placeholder="Inserisci il titolo..." required> 
                
                <br><br>
                
                <label>Contenuto:</label>
                <textarea name="contenuto"></textarea>
                
                <br><br>

                <label>Immagine di copertina:</label>
                <input type="file" name="immagine">
                
                <br><br>
                
                <input type="submit" name="submit" value="Pubblica Post" class="bottone-salva">
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 GameSAW. Tutti i diritti riservati.</p>
    </footer>

    <script src="https://cdn.tiny.cloud/1/q7e58ddi1h89wz8xrj00p8bzbf3dqmwapinwx0vjgc9bi3gv/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="tinymcescript.js"></script>
</body>
</html>