<?php
    header('Content-Type: application/json');
    
    require_once(__DIR__ . '/../database/database.php');

    $sql = "SELECT nickname, MIN(mosse) AS mosse, MIN(tempo_secondi) AS tempo_secondi FROM partite GROUP BY nickname ORDER BY mosse ASC, tempo_secondi ASC LIMIT 10";
    $res = $conn->query($sql);

    $classifica = [];

    while($row = $res->fetch_assoc()) {
        $classifica[] = $row;
    }

    echo json_encode($classifica);

    $res->free();
    $conn->close();
?>