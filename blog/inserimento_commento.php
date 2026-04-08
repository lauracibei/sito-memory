<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    if(!isset($_SESSION["utente_id"])){
        header("Location: ../autenticazioni/login.php");
        exit();
    } 

    if(isset($_POST["submit"])){

        if(isset($_GET['Post_Id']) && !empty($_POST['commento'])){
            $postId = $_GET['Post_Id'];
            $nickname = $_SESSION['nickname']; 
            $commento = $_POST['commento'];
            
            $sql = "INSERT INTO commenti (Post_Id, Nickname, Commento) VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $postId, $nickname, $commento);
            
            if($stmt->execute()){
                $stmt->close();
                $conn->close();
                header("Location: visualizza_post.php?Post_Id=" . $postId);
                exit();
            }
        } else {
            echo "Si è verificato un errore nell'inserimento del commento";
        }
    }
?>