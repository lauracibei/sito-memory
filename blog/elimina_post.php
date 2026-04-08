<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    if(!isset($_SESSION["utente_id"]) || $_SESSION["ruolo"] != "autore"){
        header("Location: ../autenticazioni/login.php");
        exit();
    }

    if(isset($_GET["Post_Id"])){
        $postId = $_GET["Post_Id"];
        
        $sql = "DELETE FROM posts WHERE Post_Id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    header("Location: mostra_post.php");
    exit();
?>