<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    if(!isset($_SESSION["utente_id"])){
        header("Location: login.php");
        exit();
    }

    $messaggioSuccesso = "";
    $errori = array();
    $utenteId = $_SESSION["utente_id"];

    $sql = "SELECT * FROM registrazione WHERE Utente_Id = '$utenteId'";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    if($res->num_rows == 1){
        if (!empty($row['DataNascita']) && $row['DataNascita'] != '0000-00-00') {
            $dataVal = date('Y-m-d', strtotime($row['DataNascita']));
        } else {
            $dataVal = "";
        }
        $nomeVal = $row['Nome'];
        $cognomeVal = $row['Cognome'];
        $emailVal = $row['Email'];
        $telVal = $row['Telefono'];
        $genereVal = $row['Genere'];
        $provVal = $row['Provincia'];
        $nicknameVal = $row['Nickname'];
    }

    $res->free();

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["salva"])){
   
        $nome = trim($_POST["nome"]); 
        $cognome = trim($_POST["cognome"]);
        $telefono = trim($_POST["telefono"]);
        $email = trim($_POST["email"]);
        
        $compleanno = !empty($_POST["compleanno"]) ? $_POST["compleanno"] : null;
        $genere = !empty($_POST["genere"]) ? $_POST["genere"] : null;
        $provincia = !empty($_POST["provincia"]) ? $_POST["provincia"] : null;
        $telefono = !empty($_POST["telefono"]) ? $_POST["telefono"] : null;

        $inputNickname = trim($_POST["nickname"]);

        if(empty($inputNickname)){
            $nickname = "Giocatore_" . rand(1, 999999);
        } else {
            $nickname = $inputNickname;
        }

        if(empty($nome) OR empty($cognome) OR empty($email)){
            array_push($errori, "Nome, cognome ed email sono obbligatori");
        }
        if(!empty($compleanno)){
            $dataOggi = date("Y-m-d");
            if ($compleanno >= $dataOggi) {
                array_push($errori, "Data di nascita non valida");
            }
        }
        if(!empty($telefono)){
            if (!preg_match("/^3\d{2}\s?\d{3}\s?\d{4}$/", $telefono)) {
                array_push($errori, "Numero di telefono non valido");
            }
        }
        if(!empty($email) AND !filter_var($email, FILTER_VALIDATE_EMAIL)){
            array_push($errori, "Formato email non valido");
        }
        
        $sql = "SELECT Email FROM registrazione WHERE Email=? AND Utente_Id!=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $utenteId);
        $stmt->execute();   
        if($stmt->affected_rows == 1){
            array_push($errori, "Impossibile modificare il profilo con queste credenziali");
        }
        $stmt->close();

        $query = "SELECT Nickname FROM registrazione WHERE Nickname=? AND Utente_Id!=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $nickname, $utenteId);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows == 1){
            array_push($errori, "Nickname già in uso");
        }

        $nomeVal = $nome;
        $cognomeVal = $cognome;
        $emailVal = $email;
        $telVal = $telefono;
        $dataVal = $compleanno;
        $genereVal = $genere;
        $provVal = $provincia;
        $nicknameVal = $nickname;

        $sql = "UPDATE registrazione SET Nome=?, Cognome=?, Email=?, Telefono=?, DataNascita=?, Genere=?, Provincia=?, Nickname=? WHERE Utente_Id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $nome, $cognome, $email, $telefono, $compleanno, $genere, $provincia, $nickname, $utenteId);

        if ($stmt->execute()) {
            $messaggioSuccesso = "Profilo aggiornato con successo!";
        } else {
            array_push($errori, "Errore nella modifica del profilo");
        }
        
        $stmt->close();
        $conn->close();
    }   
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Profilo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stile_credenziali.css">
</head>
<body>
    <div class="contenitore-principale">
        <div class="sezione-form">
            <h2>Modifica il tuo profilo</h2>

            <?php 
                if(count($errori) > 0){
                    foreach($errori as $errore){
                        echo "<div class='messaggio messaggio-errore'>" . $errore . "</div>";
                    }
                }
                if($messaggioSuccesso != ""){
                    echo "<div class='messaggio messaggio-successo'>" . $messaggioSuccesso . "</div>";
                }
            ?>
            
            <form action="modifica-profilo.php" method="POST" novalidate>
                
                <span class="label">Nome</span>
                <div class="elemento-form">
                    <input type="text" class="campo-form" name="nome" value="<?php echo htmlspecialchars($nomeVal); ?>">
                </div>

                <span class="label">Cognome</span>
                <div class="elemento-form">
                    <input type="text" class="campo-form" name="cognome" value="<?php echo htmlspecialchars($cognomeVal); ?>">
                </div>

                <span class="label">Email</span>
                <div class="elemento-form">
                    <input type="text" class="campo-form" name="email" value="<?php echo htmlspecialchars($emailVal); ?>">
                </div>

                <span class="label">Telefono</span>
                <div class="elemento-form">
                    <input type="text" class="campo-form" name="telefono" value="<?php echo htmlspecialchars($telVal); ?>">
                </div>

                <span class="label">Provincia</span>
                <div class="elemento-form">
                    <select class="campo-form" name="provincia">
                        <option value="">Provincia di residenza</option>
                        <option value="AG" <?php if($provVal == 'AG') echo 'selected'; ?>>Agrigento</option>
                        <option value="AL" <?php if($provVal == 'AL') echo 'selected'; ?>>Alessandria</option>
                        <option value="AN" <?php if($provVal == 'AN') echo 'selected'; ?>>Ancona</option>
                        <option value="AO" <?php if($provVal == 'AO') echo 'selected'; ?>>Aosta</option>
                        <option value="AR" <?php if($provVal == 'AR') echo 'selected'; ?>>Arezzo</option>
                        <option value="AP" <?php if($provVal == 'AP') echo 'selected'; ?>>Ascoli Piceno</option>
                        <option value="AT" <?php if($provVal == 'AT') echo 'selected'; ?>>Asti</option>
                        <option value="AV" <?php if($provVal == 'AV') echo 'selected'; ?>>Avellino</option>
                        <option value="BA" <?php if($provVal == 'BA') echo 'selected'; ?>>Bari</option>
                        <option value="BT" <?php if($provVal == 'BT') echo 'selected'; ?>>Barletta-Andria-Trani</option>
                        <option value="BL" <?php if($provVal == 'BL') echo 'selected'; ?>>Belluno</option>
                        <option value="BN" <?php if($provVal == 'BN') echo 'selected'; ?>>Benevento</option>
                        <option value="BG" <?php if($provVal == 'BG') echo 'selected'; ?>>Bergamo</option>
                        <option value="BI" <?php if($provVal == 'BI') echo 'selected'; ?>>Biella</option>
                        <option value="BO" <?php if($provVal == 'BO') echo 'selected'; ?>>Bologna</option>
                        <option value="BZ" <?php if($provVal == 'BZ') echo 'selected'; ?>>Bolzano</option>
                        <option value="BS" <?php if($provVal == 'BS') echo 'selected'; ?>>Brescia</option>
                        <option value="BR" <?php if($provVal == 'BR') echo 'selected'; ?>>Brindisi</option>
                        <option value="CA" <?php if($provVal == 'CA') echo 'selected'; ?>>Cagliari</option>
                        <option value="CL" <?php if($provVal == 'CL') echo 'selected'; ?>>Caltanissetta</option>
                        <option value="CB" <?php if($provVal == 'CB') echo 'selected'; ?>>Campobasso</option>
                        <option value="CE" <?php if($provVal == 'CE') echo 'selected'; ?>>Caserta</option>
                        <option value="CT" <?php if($provVal == 'CT') echo 'selected'; ?>>Catania</option>
                        <option value="CZ" <?php if($provVal == 'CZ') echo 'selected'; ?>>Catanzaro</option>
                        <option value="CH" <?php if($provVal == 'CH') echo 'selected'; ?>>Chieti</option>
                        <option value="CO" <?php if($provVal == 'CO') echo 'selected'; ?>>Como</option>
                        <option value="CS" <?php if($provVal == 'CS') echo 'selected'; ?>>Cosenza</option>
                        <option value="CR" <?php if($provVal == 'CR') echo 'selected'; ?>>Cremona</option>
                        <option value="KR" <?php if($provVal == 'KR') echo 'selected'; ?>>Crotone</option>
                        <option value="CN" <?php if($provVal == 'CN') echo 'selected'; ?>>Cuneo</option>
                        <option value="EN" <?php if($provVal == 'EN') echo 'selected'; ?>>Enna</option>
                        <option value="FM" <?php if($provVal == 'FM') echo 'selected'; ?>>Fermo</option>
                        <option value="FE" <?php if($provVal == 'FE') echo 'selected'; ?>>Ferrara</option>
                        <option value="FI" <?php if($provVal == 'FI') echo 'selected'; ?>>Firenze</option>
                        <option value="FG" <?php if($provVal == 'FG') echo 'selected'; ?>>Foggia</option>
                        <option value="FC" <?php if($provVal == 'FC') echo 'selected'; ?>>Forl&igrave;-Cesena</option>
                        <option value="FR" <?php if($provVal == 'FR') echo 'selected'; ?>>Frosinone</option>
                        <option value="GE" <?php if($provVal == 'GE') echo 'selected'; ?>>Genova</option>
                        <option value="GO" <?php if($provVal == 'GO') echo 'selected'; ?>>Gorizia</option>
                        <option value="GR" <?php if($provVal == 'GR') echo 'selected'; ?>>Grosseto</option>
                        <option value="IM" <?php if($provVal == 'IM') echo 'selected'; ?>>Imperia</option>
                        <option value="IS" <?php if($provVal == 'IS') echo 'selected'; ?>>Isernia</option>
                        <option value="AQ" <?php if($provVal == 'AQ') echo 'selected'; ?>>L'Aquila</option>
                        <option value="SP" <?php if($provVal == 'SP') echo 'selected'; ?>>La Spezia</option>
                        <option value="LT" <?php if($provVal == 'LT') echo 'selected'; ?>>Latina</option>
                        <option value="LE" <?php if($provVal == 'LE') echo 'selected'; ?>>Lecce</option>
                        <option value="LC" <?php if($provVal == 'LC') echo 'selected'; ?>>Lecco</option>
                        <option value="LI" <?php if($provVal == 'LI') echo 'selected'; ?>>Livorno</option>
                        <option value="LO" <?php if($provVal == 'LO') echo 'selected'; ?>>Lodi</option>
                        <option value="LU" <?php if($provVal == 'LU') echo 'selected'; ?>>Lucca</option>
                        <option value="MC" <?php if($provVal == 'MC') echo 'selected'; ?>>Macerata</option>
                        <option value="MN" <?php if($provVal == 'MN') echo 'selected'; ?>>Mantova</option>
                        <option value="MS" <?php if($provVal == 'MS') echo 'selected'; ?>>Massa-Carrara</option>
                        <option value="MT" <?php if($provVal == 'MT') echo 'selected'; ?>>Matera</option>
                        <option value="ME" <?php if($provVal == 'ME') echo 'selected'; ?>>Messina</option>
                        <option value="MI" <?php if($provVal == 'MI') echo 'selected'; ?>>Milano</option>
                        <option value="MO" <?php if($provVal == 'MO') echo 'selected'; ?>>Modena</option>
                        <option value="MB" <?php if($provVal == 'MB') echo 'selected'; ?>>Monza e della Brianza</option>
                        <option value="NA" <?php if($provVal == 'NA') echo 'selected'; ?>>Napoli</option>
                        <option value="NO" <?php if($provVal == 'NO') echo 'selected'; ?>>Novara</option>
                        <option value="NU" <?php if($provVal == 'NU') echo 'selected'; ?>>Nuoro</option>
                        <option value="OR" <?php if($provVal == 'OR') echo 'selected'; ?>>Oristano</option>
                        <option value="PD" <?php if($provVal == 'PD') echo 'selected'; ?>>Padova</option>
                        <option value="PA" <?php if($provVal == 'PA') echo 'selected'; ?>>Palermo</option>
                        <option value="PR" <?php if($provVal == 'PR') echo 'selected'; ?>>Parma</option>
                        <option value="PV" <?php if($provVal == 'PV') echo 'selected'; ?>>Pavia</option>
                        <option value="PG" <?php if($provVal == 'PG') echo 'selected'; ?>>Perugia</option>
                        <option value="PU" <?php if($provVal == 'PU') echo 'selected'; ?>>Pesaro e Urbino</option>
                        <option value="PE" <?php if($provVal == 'PE') echo 'selected'; ?>>Pescara</option>
                        <option value="PC" <?php if($provVal == 'PC') echo 'selected'; ?>>Piacenza</option>
                        <option value="PI" <?php if($provVal == 'PI') echo 'selected'; ?>>Pisa</option>
                        <option value="PT" <?php if($provVal == 'PT') echo 'selected'; ?>>Pistoia</option>
                        <option value="PN" <?php if($provVal == 'PN') echo 'selected'; ?>>Pordenone</option>
                        <option value="PZ" <?php if($provVal == 'PZ') echo 'selected'; ?>>Potenza</option>
                        <option value="PO" <?php if($provVal == 'PO') echo 'selected'; ?>>Prato</option>
                        <option value="RG" <?php if($provVal == 'RG') echo 'selected'; ?>>Ragusa</option>
                        <option value="RA" <?php if($provVal == 'RA') echo 'selected'; ?>>Ravenna</option>
                        <option value="RC" <?php if($provVal == 'RC') echo 'selected'; ?>>Reggio Calabria</option>
                        <option value="RE" <?php if($provVal == 'RE') echo 'selected'; ?>>Reggio Emilia</option>
                        <option value="RI" <?php if($provVal == 'RI') echo 'selected'; ?>>Rieti</option>
                        <option value="RN" <?php if($provVal == 'RN') echo 'selected'; ?>>Rimini</option>
                        <option value="RM" <?php if($provVal == 'RM') echo 'selected'; ?>>Roma</option>
                        <option value="RO" <?php if($provVal == 'RO') echo 'selected'; ?>>Rovigo</option>
                        <option value="SA" <?php if($provVal == 'SA') echo 'selected'; ?>>Salerno</option>
                        <option value="SS" <?php if($provVal == 'SS') echo 'selected'; ?>>Sassari</option>
                        <option value="SV" <?php if($provVal == 'SV') echo 'selected'; ?>>Savona</option>
                        <option value="SI" <?php if($provVal == 'SI') echo 'selected'; ?>>Siena</option>
                        <option value="SR" <?php if($provVal == 'SR') echo 'selected'; ?>>Siracusa</option>
                        <option value="SO" <?php if($provVal == 'SO') echo 'selected'; ?>>Sondrio</option>
                        <option value="SU" <?php if($provVal == 'SU') echo 'selected'; ?>>Sud Sardegna</option>
                        <option value="TA" <?php if($provVal == 'TA') echo 'selected'; ?>>Taranto</option>
                        <option value="TE" <?php if($provVal == 'TE') echo 'selected'; ?>>Teramo</option>
                        <option value="TR" <?php if($provVal == 'TR') echo 'selected'; ?>>Terni</option>
                        <option value="TO" <?php if($provVal == 'TO') echo 'selected'; ?>>Torino</option>
                        <option value="TP" <?php if($provVal == 'TP') echo 'selected'; ?>>Trapani</option>
                        <option value="TN" <?php if($provVal == 'TN') echo 'selected'; ?>>Trento</option>
                        <option value="TV" <?php if($provVal == 'TV') echo 'selected'; ?>>Treviso</option>
                        <option value="TS" <?php if($provVal == 'TS') echo 'selected'; ?>>Trieste</option>
                        <option value="UD" <?php if($provVal == 'UD') echo 'selected'; ?>>Udine</option>
                        <option value="VA" <?php if($provVal == 'VA') echo 'selected'; ?>>Varese</option>
                        <option value="VE" <?php if($provVal == 'VE') echo 'selected'; ?>>Venezia</option>
                        <option value="VB" <?php if($provVal == 'VB') echo 'selected'; ?>>Verbano-Cusio-Ossola</option>
                        <option value="VC" <?php if($provVal == 'VC') echo 'selected'; ?>>Vercelli</option>
                        <option value="VR" <?php if($provVal == 'VR') echo 'selected'; ?>>Verona</option>
                        <option value="VV" <?php if($provVal == 'VV') echo 'selected'; ?>>Vibo Valentia</option>
                        <option value="VI" <?php if($provVal == 'VI') echo 'selected'; ?>>Vicenza</option>
                        <option value="VT" <?php if($provVal == 'VT') echo 'selected'; ?>>Viterbo</option>
                    </select>
                </div>

                <span class="label">Data di Nascita</span>
                <div class="elemento-form">
                    <input type="date" class="campo-form" name="compleanno" value="<?php echo htmlspecialchars($dataVal); ?>">
                </div>

                <span class="label">Genere</span>
                <div class="elemento-form">
                    <select class="campo-form" name="genere">
                        <option value="">Seleziona Genere</option>
                        <option value="Uomo" <?php if($genereVal == 'Uomo') echo 'selected'; ?>>Uomo</option>
                        <option value="Donna" <?php if($genereVal == 'Donna') echo 'selected'; ?>>Donna</option>
                        <option value="Altro" <?php if($genereVal == 'Altro') echo 'selected'; ?>>Altro</option>
                    </select>
                </div>

                <label class="label">Nickname:</label>
                <div class="elemento-form">
                    <input type="text" class="campo-form" name="nickname" value="<?php echo htmlspecialchars($nicknameVal); ?>">
                </div>

                <div class="contenitore-bottoni">
                    <a href="modifica-password.php" class="bottoni-azioni bottone">CAMBIA PASSWORD</a>
                    
                    <button type="submit" name="salva" class="bottoni-azioni bottone-logout">SALVA MODIFICHE</button>                    
                </div>
                <p><a href="profilo.php">Torna al profilo</a></p>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2023 GameSAW. Tutti i diritti riservati.</p>
    </footer>

</body>
</html>