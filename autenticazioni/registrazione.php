<?php
    session_start();
    require_once(__DIR__ . '/../database/database.php');

    if(isset($_SESSION["utente_id"])){
        header("Location: ../gioco/gioco.php");
        exit();
    }

    $errori = array();

    $numeroRandom = rand(1, 999999);
    $nicknameSuggerito = "Giocatore_" . $numeroRandom;
    $valoreNickname = isset($_POST['nickname']) ? $_POST['nickname'] : $nicknameSuggerito;

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
        $nome = trim($_POST["name"]);
        $cognome = trim($_POST["cognome"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $confermaPassword = $_POST["conferma-password"];

        $compleanno = !empty($_POST["compleanno"]) ? $_POST["compleanno"] : null;
        $genere = !empty($_POST["genere"]) ? $_POST["genere"] : null;
        $telefono = !empty($_POST["telefono"]) ? trim($_POST["telefono"]) : null;
        $provincia = !empty($_POST["provincia"]) ? trim($_POST["provincia"]) : null;
        
        $inputNickname = trim($_POST["nickname"]);
        
        if(empty($inputNickname)){
            $nuovoRandom = rand(1, 999999);
            $nickname = "Giocatore_" . $nuovoRandom;
            $valoreNickname = $nickname; 
        } else {
            $nickname = $inputNickname;
        }
        
        if(empty($nome) OR empty($cognome) OR empty($email) OR empty($password) OR empty($confermaPassword)){
            array_push($errori, "Completa tutti i campi obbligatori");
        }
        if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
            array_push($errori, "Formato email non valido");
        }
        if(strlen($password) < 8 AND !empty($password)){
            array_push($errori, "La password deve contenere almeno 8 caratteri");
        }
        if(!empty($confermaPassword) AND $password !== $confermaPassword){
            array_push($errori, "Password e conferma password sono diversi");
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

        $query = "SELECT Nickname FROM registrazione WHERE Nickname=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nickname);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows == 1){
            array_push($errori, "Nickname già in uso");
        }

        if (count($errori) == 0) {
            $stmt = $conn->prepare("SELECT Email FROM registrazione WHERE Email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute(); 
            $res = $stmt->get_result();

            if($res->num_rows == 1){
                array_push($errori, "Impossibile completare la registrazione con le credenziali fornite");
            } else {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $ruolo = "utente";

                $sql = "INSERT INTO registrazione (Nome, Cognome, Email, Password, DataNascita, Genere, Telefono, Provincia, Nickname, Ruolo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssss", $nome, $cognome, $email, $passwordHash, $compleanno, $genere, $telefono, $provincia, $nickname, $ruolo);
                if($stmt->execute()){
                    header("Location: login.php?registrato=true");
                    exit(); 
                } else {
                    array_push($errori, "Errore durante la registrazione");
                }
                $stmt->close();
                $conn->close();
            }
            $res->free();
            $conn->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel ="stylesheet" href="stile_credenziali.css">
</head>

<body>
    <div class="contenitore-principale">
        <div class="sezione-form" id="register-form">
            <form action="registrazione.php" method="POST" novalidate>
                <h2>Registrazione</h2>
                <?php
                    if(count($errori) > 0){
                        foreach($errori as $errore){
                            echo "<div class='messaggio messaggio-errore'>" . $errore . "</div>";
                        }
                    }
                ?>
                <div class="elemento-form">
                    <input type="text" class="campo-form" name="name" placeholder="Nome*" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div> 
                <div class="elemento-form">
                    <input type="text" class="campo-form" name="cognome" placeholder="Cognome*"value="<?php echo isset($_POST['cognome']) ? htmlspecialchars($_POST['cognome']) : '';?>">
                </div> 
                <div class="elemento-form">
                    <input type="email" class="campo-form" name="email" placeholder="Email*" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';?>">
                </div> 
                <div class="elemento-form pos-icona">
                    <input type="password" id="password" class="campo-form" name="password" placeholder="Password*">
                    <img src="../immagini/occhioaperto.png" class="icona-password" alt="Mostra Password">
                </div> 
                <div class="elemento-form pos-icona">
                    <input type="password" id="confermaPassword" class="campo-form" name="conferma-password" placeholder="Conferma password*">
                    <img src="../immagini/occhioaperto.png" class="icona-password" alt="Mostra Password">
                </div> 

                <div class="elemento-form">
                    <p>Campi opzionali: </p>
                </div> 

                <div class="elemento-form">
                    <input type="date" class="campo-form" name="compleanno" placeholder="Data di nascita" value="<?php echo isset($_POST['compleanno']) ? htmlspecialchars($_POST['compleanno']) : ''; ?>">
                </div> 
                <div class="elemento-form">
                    <select class="campo-form" name="genere">
                        <option value=""> Genere</option>
                        <option value="Uomo" <?php if(isset($_POST['genere']) && $_POST['genere'] == 'Uomo') echo 'selected'; ?>>Uomo</option>
                        <option value="Donna" <?php if(isset($_POST['genere']) && $_POST['genere'] == 'Donna') echo 'selected'; ?>>Donna</option>
                        <option value="Altro" <?php if(isset($_POST['genere']) && $_POST['genere'] == 'Altro') echo 'selected'; ?>>Altro</option>
                    </select>
                </div>
                <div class="elemento-form">
                    <input type="tel" class="campo-form" name="telefono" placeholder="Numero di Telefono" value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                </div>

                <div class="elemento-form">
                <select class="campo-form" name="provincia">
                    <option value="">Provincia di residenza</option>
                    <option value="AG" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AG') echo 'selected'; ?>>Agrigento</option>
                    <option value="AL" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AL') echo 'selected'; ?>>Alessandria</option>
                    <option value="AN" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AN') echo 'selected'; ?>>Ancona</option>
                    <option value="AO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AO') echo 'selected'; ?>>Aosta</option>
                    <option value="AR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AR') echo 'selected'; ?>>Arezzo</option>
                    <option value="AP" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AP') echo 'selected'; ?>>Ascoli Piceno</option>
                    <option value="AT" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AT') echo 'selected'; ?>>Asti</option>
                    <option value="AV" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AV') echo 'selected'; ?>>Avellino</option>
                    <option value="BA" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BA') echo 'selected'; ?>>Bari</option>
                    <option value="BT" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BT') echo 'selected'; ?>>Barletta-Andria-Trani</option>
                    <option value="BL" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BL') echo 'selected'; ?>>Belluno</option>
                    <option value="BN" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BN') echo 'selected'; ?>>Benevento</option>
                    <option value="BG" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BG') echo 'selected'; ?>>Bergamo</option>
                    <option value="BI" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BI') echo 'selected'; ?>>Biella</option>
                    <option value="BO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BO') echo 'selected'; ?>>Bologna</option>
                    <option value="BZ" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BZ') echo 'selected'; ?>>Bolzano</option>
                    <option value="BS" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BS') echo 'selected'; ?>>Brescia</option>
                    <option value="BR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'BR') echo 'selected'; ?>>Brindisi</option>
                    <option value="CA" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CA') echo 'selected'; ?>>Cagliari</option>
                    <option value="CL" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CL') echo 'selected'; ?>>Caltanissetta</option>
                    <option value="CB" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CB') echo 'selected'; ?>>Campobasso</option>
                    <option value="CE" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CE') echo 'selected'; ?>>Caserta</option>
                    <option value="CT" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CT') echo 'selected'; ?>>Catania</option>
                    <option value="CZ" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CZ') echo 'selected'; ?>>Catanzaro</option>
                    <option value="CH" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CH') echo 'selected'; ?>>Chieti</option>
                    <option value="CO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CO') echo 'selected'; ?>>Como</option>
                    <option value="CS" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CS') echo 'selected'; ?>>Cosenza</option>
                    <option value="CR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CR') echo 'selected'; ?>>Cremona</option>
                    <option value="KR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'KR') echo 'selected'; ?>>Crotone</option>
                    <option value="CN" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'CN') echo 'selected'; ?>>Cuneo</option>
                    <option value="EN" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'EN') echo 'selected'; ?>>Enna</option>
                    <option value="FM" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'FM') echo 'selected'; ?>>Fermo</option>
                    <option value="FE" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'FE') echo 'selected'; ?>>Ferrara</option>
                    <option value="FI" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'FI') echo 'selected'; ?>>Firenze</option>
                    <option value="FG" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'FG') echo 'selected'; ?>>Foggia</option>
                    <option value="FC" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'FC') echo 'selected'; ?>>Forl&igrave;-Cesena</option>
                    <option value="FR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'FR') echo 'selected'; ?>>Frosinone</option>
                    <option value="GE" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'GE') echo 'selected'; ?>>Genova</option>
                    <option value="GO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'GO') echo 'selected'; ?>>Gorizia</option>
                    <option value="GR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'GR') echo 'selected'; ?>>Grosseto</option>
                    <option value="IM" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'IM') echo 'selected'; ?>>Imperia</option>
                    <option value="IS" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'IS') echo 'selected'; ?>>Isernia</option>
                    <option value="AQ" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'AQ') echo 'selected'; ?>>L'Aquila</option>
                    <option value="SP" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'SP') echo 'selected'; ?>>La Spezia</option>
                    <option value="LT" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'LT') echo 'selected'; ?>>Latina</option>
                    <option value="LE" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'LE') echo 'selected'; ?>>Lecce</option>
                    <option value="LC" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'LC') echo 'selected'; ?>>Lecco</option>
                    <option value="LI" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'LI') echo 'selected'; ?>>Livorno</option>
                    <option value="LO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'LO') echo 'selected'; ?>>Lodi</option>
                    <option value="LU" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'LU') echo 'selected'; ?>>Lucca</option>
                    <option value="MC" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'MC') echo 'selected'; ?>>Macerata</option>
                    <option value="MN" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'MN') echo 'selected'; ?>>Mantova</option>
                    <option value="MS" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'MS') echo 'selected'; ?>>Massa-Carrara</option>
                    <option value="MT" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'MT') echo 'selected'; ?>>Matera</option>
                    <option value="ME" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'ME') echo 'selected'; ?>>Messina</option>
                    <option value="MI" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'MI') echo 'selected'; ?>>Milano</option>
                    <option value="MO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'MO') echo 'selected'; ?>>Modena</option>
                    <option value="MB" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'MB') echo 'selected'; ?>>Monza e Brianza</option>
                    <option value="NA" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'NA') echo 'selected'; ?>>Napoli</option>
                    <option value="NO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'NO') echo 'selected'; ?>>Novara</option>
                    <option value="NU" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'NU') echo 'selected'; ?>>Nuoro</option>
                    <option value="OR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'OR') echo 'selected'; ?>>Oristano</option>
                    <option value="PD" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PD') echo 'selected'; ?>>Padova</option>
                    <option value="PA" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PA') echo 'selected'; ?>>Palermo</option>
                    <option value="PR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PR') echo 'selected'; ?>>Parma</option>
                    <option value="PV" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PV') echo 'selected'; ?>>Pavia</option>
                    <option value="PG" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PG') echo 'selected'; ?>>Perugia</option>
                    <option value="PU" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PU') echo 'selected'; ?>>Pesaro e Urbino</option>
                    <option value="PE" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PE') echo 'selected'; ?>>Pescara</option>
                    <option value="PC" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PC') echo 'selected'; ?>>Piacenza</option>
                    <option value="PI" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PI') echo 'selected'; ?>>Pisa</option>
                    <option value="PT" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PT') echo 'selected'; ?>>Pistoia</option>
                    <option value="PN" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PN') echo 'selected'; ?>>Pordenone</option>
                    <option value="PZ" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PZ') echo 'selected'; ?>>Potenza</option>
                    <option value="PO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'PO') echo 'selected'; ?>>Prato</option>
                    <option value="RG" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'RG') echo 'selected'; ?>>Ragusa</option>
                    <option value="RA" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'RA') echo 'selected'; ?>>Ravenna</option>
                    <option value="RC" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'RC') echo 'selected'; ?>>Reggio Calabria</option>
                    <option value="RE" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'RE') echo 'selected'; ?>>Reggio Emilia</option>
                    <option value="RI" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'RI') echo 'selected'; ?>>Rieti</option>
                    <option value="RN" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'RN') echo 'selected'; ?>>Rimini</option>
                    <option value="RM" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'RM') echo 'selected'; ?>>Roma</option>
                    <option value="RO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'RO') echo 'selected'; ?>>Rovigo</option>
                    <option value="SA" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'SA') echo 'selected'; ?>>Salerno</option>
                    <option value="SS" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'SS') echo 'selected'; ?>>Sassari</option>
                    <option value="SV" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'SV') echo 'selected'; ?>>Savona</option>
                    <option value="SI" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'SI') echo 'selected'; ?>>Siena</option>
                    <option value="SR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'SR') echo 'selected'; ?>>Siracusa</option>
                    <option value="SO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'SO') echo 'selected'; ?>>Sondrio</option>
                    <option value="SU" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'SU') echo 'selected'; ?>>Sud Sardegna</option>
                    <option value="TA" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'TA') echo 'selected'; ?>>Taranto</option>
                    <option value="TE" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'TE') echo 'selected'; ?>>Teramo</option>
                    <option value="TR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'TR') echo 'selected'; ?>>Terni</option>
                    <option value="TO" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'TO') echo 'selected'; ?>>Torino</option>
                    <option value="TP" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'TP') echo 'selected'; ?>>Trapani</option>
                    <option value="TN" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'TN') echo 'selected'; ?>>Trento</option>
                    <option value="TV" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'TV') echo 'selected'; ?>>Treviso</option>
                    <option value="TS" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'TS') echo 'selected'; ?>>Trieste</option>
                    <option value="UD" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'UD') echo 'selected'; ?>>Udine</option>
                    <option value="VA" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'VA') echo 'selected'; ?>>Varese</option>
                    <option value="VE" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'VE') echo 'selected'; ?>>Venezia</option>
                    <option value="VB" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'VB') echo 'selected'; ?>>Verbano-Cusio-Ossola</option>
                    <option value="VC" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'VC') echo 'selected'; ?>>Vercelli</option>
                    <option value="VR" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'VR') echo 'selected'; ?>>Verona</option>
                    <option value="VV" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'VV') echo 'selected'; ?>>Vibo valentia</option>
                    <option value="VI" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'VI') echo 'selected'; ?>>Vicenza</option>
                    <option value="VT" <?php if(isset($_POST['provincia']) && $_POST['provincia'] == 'VT') echo 'selected'; ?>>Viterbo</option>
                </select>
                </div>
                
                <label class="label">Nickname:</label>
                <div class="elemento-form">
                    <input type="text" class="campo-form" name="nickname" value="<?php echo htmlspecialchars($valoreNickname); ?>">
                </div>

                <div class="elemento-form">
                    <input type="submit" name="submit" value="Registrati">
                </div>
                
                <p>Hai già un account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
    <script src="script_password.js"></script>
</body>
</html>