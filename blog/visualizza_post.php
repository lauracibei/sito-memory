<?php
    session_start();
    
    require_once(__DIR__ . '/../database/database.php');

    if(!isset($_SESSION["utente_id"])){
        header("Location: ../autenticazioni/login.php");
        exit();
    }

    $postId = $_GET["Post_Id"];
    $postDettagli = [];

    $sql = "SELECT * FROM posts WHERE Post_Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);
    $stmt->execute();   
    $res = $stmt->get_result();
    
    if($res && $res->num_rows > 0){
        $row = $res->fetch_assoc();

        $postDettagli['titolo_safe'] = htmlspecialchars($row['Titolo']);
        $postDettagli['autore'] = htmlspecialchars($row['Autore_nickname']);
        
        $contenutoPuro = html_entity_decode($row['Contenuto']);
        $postDettagli['contenuto_html'] = nl2br(htmlspecialchars($contenutoPuro)); 
        
        $postDettagli['img_url'] = '';
        if(!empty($row['Immagine'])){
            $postDettagli['img_url'] = 'immagini/' . $row['Immagine'];
        }
    } else {
        echo "Post non trovato.";
        exit();
    }
    $stmt->close();

    $listaCommenti = [];

    $sql2 = "SELECT Nickname, Commento FROM commenti WHERE Post_Id ='$postId'";
    $res2 = $conn->query($sql2);

    if($res2 && $res2->num_rows > 0){
        while($row2 = $res2->fetch_assoc()){
            $listaCommenti[] = [
                'nick' => htmlspecialchars($row2['Nickname']),
                'testo' => htmlspecialchars($row2['Commento'])
            ];
        }
    }

    $conn->close();
   
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['Titolo']; ?></title>

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
        <div class="contenitore-post">

            <div class="titolo">
                <?php echo htmlspecialchars($row['Titolo']); ?>
            </div>

            <div class="immagine">
                <?php if(!empty($row['Immagine'])): ?>
                    <img src="../immagini/<?php echo $row['Immagine']; ?>" alt="Immagine del post">
                <?php endif; ?>
            </div>
           
            <div>
                <?php echo $row['Contenuto']; ?>
            </div>
        
            <label>Autore:</label> <?php echo htmlspecialchars($row['Autore_nickname']) ?>
            
            <label>Commenti:</label>
            <div class="sezione-commenti">
                <?php if(count($listaCommenti) > 0): ?>
                    
                    <?php foreach($listaCommenti as $commento): ?>
                        <div class="singolo-commento">
                            <strong><?= $commento['nick'] ?>:</strong> 
                            <?= $commento['testo'] ?>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <p>Non ci sono ancora commenti. Sii il primo!</p>
                <?php endif; ?>
            </div>
            <form action="inserimento_commento.php?Post_Id=<?php echo $postId; ?>" method="POST">
                <textarea name="commento" placeholder="Scrivi un commento"></textarea>
                <input type="submit" name="submit" value="Aggiungi un commento" class="bottone-salva">
            </form>
        </div>  
    </div>

    <footer>
        <p>&copy; 2023 GameSAW. Tutti i diritti riservati.</p>
    </footer>
</body>
</html>