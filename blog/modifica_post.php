<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    if(!isset($_SESSION["utente_id"]) || $_SESSION["ruolo"] != "autore"){
        header("Location: ../autenticazioni/login.php");
        exit();
    }

    $postId = $_GET["Post_Id"];
    $messaggio = "";

    $sql = "SELECT * FROM posts WHERE Post_Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if(isset($_POST["submit"])){
        $titolo = $_POST["titolo"];
        $contenuto = $_POST["contenuto"];

        $nome_img = $row["Immagine"];
        
        if(isset($_FILES["immagine"]) && !empty($_FILES["immagine"]["name"])){
            $nome_img = $_FILES["immagine"]["name"];
            move_uploaded_file($_FILES["immagine"]["tmp_name"], "immagini/".$nome_img);
        }

        $stmt = $conn->prepare("UPDATE posts SET Titolo=?, Contenuto=?, Immagine=? WHERE Post_Id=?");
        $stmt->bind_param("sssi", $titolo, $contenuto, $nome_img, $postId);
        
        if($stmt->execute()){
            header("Location: mostra_post.php");
            exit();
        } else {
            $messaggio = "<div class='contenitore-messaggio errore'>Errore nella modifica del post</div>";
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Post</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile_blog.css">
</head>
<body>

     <div class="barra-superiore">
        <a href="mostra_post.php" class="bottone-barra-sup bottone-homeblog">TORNA AI POST</a>
    </div>

    <div class="contenitore">
        <h1>Modifica Post</h1>
        <?php echo $messaggio; ?>

        <div class="contenitore-post">
            <form action="modifica_post.php" method="POST" enctype="multipart/form-data">
                
                <label>Titolo:</label>
                <input type="text" name="titolo" value="<?php echo htmlspecialchars($row['Titolo']); ?>" required> 
                
                <br><br>
                
                <label>Contenuto:</label>
                <textarea name="contenuto"><?php echo htmlspecialchars($row['Contenuto']); ?></textarea>
                
                <br><br>

                <label>Immagine:</label>
                <br>
                <?php if($row['Immagine']): ?>
                    <small>Attuale: <?php echo $row['Immagine']; ?></small><br>
                <?php endif; ?>
                <input type="file" name="immagine">
                
                <br><br>
                
                <input type="submit" name="submit" value="Salva Modifiche" class="bottone-salva">
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