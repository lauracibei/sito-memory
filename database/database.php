<?php
    mysqli_report(MYSQLI_REPORT_OFF);

    $servername = "localhost";
    $username = "your username";
    $password = "your password";
    $dbname = "your dbname";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_errno) {
        die("Siamo spiacenti, si è verificato un errore di connessione al sistema");
        error_log("Errore di connessione: " . $conn->connect_error);
    }
?>
