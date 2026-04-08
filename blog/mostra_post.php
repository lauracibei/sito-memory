<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    if(!isset($_SESSION["utente_id"])){
        header("Location: ../autenticazione/login.php?redirect=blog");
        exit();
    }

    $posts = []; 
    $sql = "SELECT * FROM posts ORDER BY Post_Id DESC";
    $res = $conn->query($sql);

    if($res && $res->num_rows > 0){
        while($row = $res->fetch_assoc()) {

            $row['titolo_safe'] = htmlspecialchars($row["Titolo"]);

            $testoPuro = strip_tags(html_entity_decode($row["Contenuto"]));
            if(mb_strlen($testoPuro) > 100){
                $row['anteprima'] = htmlspecialchars(mb_substr($testoPuro, 0, 100)) . '...';
            } else {
                $row['anteprima'] = htmlspecialchars($testoPuro);
            }

            $row['img_url'] = '';
            if(!empty($row["Immagine"])) {
                $row['img_url'] = '../immagini/' . urlencode(basename($row["Immagine"]));
            }

            $posts[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Home Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile_blog.css">
</head>

<body>
    <div class="barra-superiore">
        <a href="../gioco/gioco.php" class="bottone-barra-sup bottone-homepage"></a>
        
        <?php if(isset($_SESSION["ruolo"]) && $_SESSION["ruolo"] == "autore"): ?>
            <a href="nuovo_post.php" class="bottone-testo">NUOVO POST</a>   
        <?php endif; ?>
    </div>

    <div class="contenitore">
        <h1>Blog di MemInfo💻</h1>

        <div class="griglia-post">
            <?php if(count($posts) > 0): ?>
                
                <?php foreach($posts as $post): ?>  
                    <div class="anteprima-post">
                        <a href="visualizza_post.php?Post_Id=<?= $post['Post_Id'] ?>" class="anteprima-zona">
                            <?php if($post['img_url']): ?>
                                <div class="anteprima-post-immagine" style="background-image: url('<?= $post['img_url'] ?>');"></div>
                            <?php else: ?>
                                <div class="anteprima-post-immagine anteprima-post-nessuna-immagine"></div>
                            <?php endif; ?>
                        </a>

                        <div class="anteprima-post-testo">
                            <a href="visualizza_post.php?Post_Id=<?= $post['Post_Id'] ?>" class="anteprima-zona">
                                <h2><?= $post['titolo_safe'] ?></h2>
                                <p><?= $post['anteprima'] ?></p>
                            </a>
                            
                            <?php if(isset($_SESSION["ruolo"]) && $_SESSION["ruolo"] == "autore"): ?>
                                <div class="contenitore-anteprima-bottoni">
                                    <a href="modifica_post.php?Post_Id=<?= $post['Post_Id'] ?>" class="bottoni-anteprima modifica">Modifica</a>
                                    <button type="button" class="bottoni-anteprima elimina js-elimina" data-id="<?= $post['Post_Id'] ?>">Elimina</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php else: ?>
                <p>Nessun post presente.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="modaleEliminazione" class="modale">
        <div class="contenuto-modale">
            <span class="chiusura-modale" onclick="chiudiModale()">&times;</span>
            
            <h2>⚠️ Attenzione</h2>
            <p>Sei sicuro di voler eliminare definitivamente questo post?</p>
            
            <div class="modale-bottoni">
                <button onclick="chiudiModale()" class="btn-modale btn-annulla">ANNULLA</button>
                
                <a id="linkConfermaEliminazione" href="#" class="btn-modale btn-conferma">ELIMINA</a>
            </div>
        </div>
    </div>

    <script src="finestra.js"></script>

    <footer>
        <p>&copy; 2023 GameSAW. Tutti i diritti riservati.</p>
    </footer>
</body>
</html>