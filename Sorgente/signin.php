<?php  
error_reporting(E_ALL &~E_NOTICE);

//Quando si entra nella pagina di sign in/registrazione si unsetta la sessione attuale e si eliminano tutte le eventuali informazioni salvate in $session
if(isset($_SESSION)){
   session_unset($_SESSION);
   session_destroy(); 
}

$tipoSignIn="";
$service="";
$jumper=0;
$accessKey="1999";
$unlock = 0;

//in base al link preso dalla pagina di login si passera un valore Tipoutente che varierà il tipo di form che andremo ad compilare 
// questo verra settato nel cookie per garantire un uso corretto della pagina

if(isset($_GET['TipoUtente']) && !((isset($_POST['Iscriviti'])))){
    if ($_GET['TipoUtente'] == "0"){
        $tipoSignIn = 0;
        setcookie('tipoSignIn', "0");
    }
    else if($_GET['TipoUtente'] == "1"){
        $tipoSignIn = 1;
        setcookie('tipoSignIn', "1");
    }
    else if($_GET['TipoUtente'] == "2"){
        $tipoSignIn = 2;
        setcookie('tipoSignIn', "2");
    }
   
}
//il jumper vienere utilizzato come check finale dopo aver controllato che password email partita iva e data di nascita sono corretti
if(isset($_POST['signin']) && isset($_COOKIE['tipoSignIn'])){
    if($_COOKIE['tipoSignIn'] == 1){
        if(!(preg_match('/^[0-9]{12}+$/', $_POST['PIVA']))){
            $service=("Partita Iva non valida!");
            $jumper=1;
            $tipoSignIn = 1;

        }
    }
}
//se si vuole fare la registrazione come admin bisognera inserire una key di accesso speciale qui sotto si controlla usando la chiave d'accesso se la registrazione è regolare
if(isset($_POST['accessKey'])){
    if($_POST['accessKey'] == $accessKey) $unlock = 1;
}

//Quando si passano i vari check si procede alla procedee alla connessione al database 
if(isset($_POST['signin']) && $jumper==0){
    if(preg_match('/^.*@.*$/', $_POST['Email']) &&
        preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['DataNascita']) && 
        preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['Password']) ){
        $db_name = "Database_Pixel_Hub";
        $table_users = "Tabella_Utenti";
        $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);
        $num=0;

        if (mysqli_connect_errno()){
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }
        //Si controlla se l'utente non è gia presente sul database 
        $queryLogin = "SELECT * FROM $table_users WHERE (Email='".$_POST['Email']."' OR Username='{$_POST['Nickname']}') AND Password='{$_POST['Password']}'";
        $resultQ = mysqli_query($mysqliConnection, $queryLogin);
        $num = mysqli_num_rows($resultQ);
        // se il numero delle righe presente al controllo della tabella utenti 
        if($num > 0){
            $service=("Utente già registrato!");
        }
       
        else{
                if($_COOKIE['tipoSignIn'] == "2" && $unlock == 0) $service="Chiave di registrazione errata! Contattare la segreteria al numero +3906061225587";
                
                else{
                        if($_COOKIE['tipoSignIn'] == "0"){ //Tipo utente normale
                        $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente,imgProfiloPath)
                        VALUES
                        ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}','{$_POST['DataNascita']}', 2, 0, 0, 0,'ProfilePic/propicblank.png')";
                        setcookie('tipoSignIn', "", time() - 3600);
                        }
                        else if($_COOKIE['tipoSignIn'] == "1"){//tipo utente publisher che possiede una partita iva
                            $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente,imgProfiloPath, PIVA)
                        VALUES
                        ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}','{$_POST['DataNascita']}', 2, 0, 0, 1,'ProfilePic/propicblank.png','{$_POST['PIVA']}')";
                        setcookie('tipoSignIn', "", time() - 3600);
                        }
                        else if($_COOKIE['tipoSignIn'] == "2"){//Tipo utente admin
                            $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente, imgProfiloPath)
                        VALUES
                        ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}','{$_POST['DataNascita']}', 2, 0, 0, 2,'ProfilePic/propicblank.png')";
                        setcookie('tipoSignIn', "", time() - 3600);
                        }

                        
                        if (!$resultQ = mysqli_query($mysqliConnection, $sql)) {
                        echo("Query non partita! \n");
                        exit();
                        }
                        else {

                            $db_name = "Database_Pixel_Hub";
                            $table_users = "Tabella_Utenti";
                            $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

                            if (mysqli_connect_errno()) printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                            
                            $nickname = mysqli_real_escape_string($mysqliConnection, $_POST['Nickname']);
                            $sql = "SELECT ID FROM $table_users WHERE username = '$nickname'";

                            $resultQ = mysqli_query($mysqliConnection, $sql);

                            if ($resultQ){
                                $row = mysqli_fetch_array($resultQ);
                            
                                $idUtente=$row['ID'];

                                $xmlString="";
                                                                                        
                                foreach(file("XML/utenti.xml") as $node){ 
                                    $xmlString .= trim($node);
                                }
                                
                                $doc= new DOMDocument();
                                $doc->loadXML($xmlString);
                                $doc->formatOutput = true;
                                $root=$doc->documentElement;
                                $elem=$root->childNodes;
                                $utente = $doc->createElement("Utente");
                                $utente->setAttribute("id_user", $idUtente); 
                                $utente->appendChild($doc->createElement("linkEsterno", ""));
                                $utente->appendChild($doc->createElement("DataIscrizione", date("d/m/Y")));
                                $utente->appendChild($doc->createElement("CasaDiSviluppoPreferita", "$_POST[CasaDiSviluppo]"));
                                $utente->appendChild($doc->createElement("GenerePreferito", "$_POST[Genere]"));
                                $utente->appendChild($doc->createElement("listaGiochi"));
                                $utente->appendChild($doc->createElement("listaPropic"));

                                $root->appendChild($utente);
                                $doc->save("XML/utenti.xml");

                        
                                header("Location: login.php");
                            }
                            else printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                            
                                
                            
                        
                        }
                }  
            }
    }
    else if(!(preg_match('/^.*@.*$/', $_POST['Email'])) && isset($_POST['signin']) && $jumper==0 && $unlock == 0){
        $service=("Email non valida!");
    }
    else if(!(preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['DataNascita'])) && isset($_POST['signin']) && $jumper==0 && $unlock == 0){
        $service=("Data di Nascita non valida!");
    }
    else if(!(preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['Password'])) && isset($_POST['signin']) && $jumper==0 && $unlock == 0){
        $service=("Password non valida! Deve contenere almeno una lettera maiuscola, un carattere speciale (!,@,=,&) ed essere lunga almeno 8 caratteri.");
    }
    
    
}
    

    ?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Registrazione - PixelHub</title>        
        <link rel="stylesheet" type="text/css" href="Stile/Registrazione.css?v=1" /> 
        
    </head>
    <body>
        <div id="logo">
                    <a href="Homepage.php">
                    <img src="Loghi/logo pixelhub slim.png" alt="logo pixelhub">
                    </a>
        </div>
        <div id="SigninCard">
            <div class="SigninForm">
                
                <form action="signin.php" method="post">
                    <?php
                    if($service != ""){
                        echo "<div id='service'><p >$service</p></div>";
                    }  
                    ?>
                    <div>
                        <p>Nome</p>
                        <input type="text" placeholder="Mario" name="Nome" maxlength="50"/>
                    </div>
                    <div>
                        <p>Cognome</p>
                        <input type="text" placeholder="Rossi" name="Cognome" maxlength="50"/>
                    </div>
                    <div>
                        <p>Email</p>
                        <input type="text" placeholder="example@mail.com" name="Email" maxlength="100"/>   
                    </div>
                    <div>
                        <p>Password</p>
                        <input type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" name="Password"/>
                    </div>
                    <div>
                        <p>Nickname</p>
                        <input type="text" placeholder="SuperBazinga666" name="Nickname" maxlength="50"/>
                        
                    </div>
                    <div>
                        <p>Data di Nascita</p>
                        <input type="text" placeholder="01-01-1980" name="DataNascita" maxlength="50"/>
                    </div>

                    <?php
                    if($tipoSignIn == 1 || (isset($_COOKIE['tipoSignIn']) && $_COOKIE['tipoSignIn'] == "1")){
                        echo "<div id=\"Partitaiva\"> <p>Partita Iva</p> <input type=\"text\" placeholder=\"\" name=\"PIVA\" maxlenght=\"12\"/></div>";
                    }
                    ?>

                    <?php
                    if($tipoSignIn == 2 || (isset($_COOKIE['tipoSignIn']) && $_COOKIE['tipoSignIn'] == "2")){
                        echo "<div id=\"ChiaveAccesso\"> <p>Chiave di registrazione</p> <input type=\"text\" placeholder=\"&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;\" name=\"accessKey\" /></div>";
                    }
                    ?>

                    <div id="casaSviluppo">
                        <p>Casa Sviluppo preferita</p>
                        <input type="text" placeholder="DICE" name="CasaDiSviluppo" maxlength="50"/>
                    </div>

                    <div id="generePreferito">
                        <p>Genere preferito</p>
                        <select name="Genere">
                            <option value=""></option>
                            <option value="Sparatutto">Sparatutto</option> 
                            <option value="RPG">RPG</option>
                            <option value="Avventura">Avventura</option>
                            <option value="Souls-like">Souls-like</option>
                            <option value="Strategia">Strategia</option>
                            <option value="Rouge-like">Rouge-like</option>
                        </select>
                    </div>

                    <div>
                        <input type="submit" name="signin" value="Iscriviti" />
                    </div>
                    
                </form>

            </div>
                    
        </div>
        <div id="footer">
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li><br>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>