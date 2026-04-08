<?php
    require_once(__DIR__ . '/../database/database.php');
    session_start();
    header('Content-Type: application/json');

    if(!isset($_SESSION["utente_id"])){
        echo json_encode(["status" => "error", "message" => "Utente non loggato"]);
        exit();
    }

    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (isset($data['mosse']) && isset($data['tempo'])) {
        
        $mosse = intval($data['mosse']);
        $tempo = intval($data['tempo']); 
        $nickname = $_SESSION["nickname"];

        if ($mosse < 8 || $tempo <= 0) {
            echo json_encode(["status" => "error", "message" => "Dati di gioco non validi"]);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO partite (nickname, mosse, tempo_secondi) VALUES (?, ?, ?)");
        
        $stmt->bind_param("sii", $nickname, $mosse, $tempo);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Partita salvata"]);
        } else {
            error_log("Errore salvataggio partita per $nickname: " . $stmt->error);
            echo json_encode(["status" => "error", "message" => "Si è verificato un errore durante il salvataggio. Riprova più tardi."]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Dati mancanti"]);
    }

    $conn->close();
?>