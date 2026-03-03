<?php
/* Pagina per il login utente. La pagina effetua una chiamata al DB per validare le informazioni di nome utente o Email e Password. In caso di successo. l'utente verra indirizzato
all'homepage. In caso contrario, attraverso varie espressioni regolari (attraverso preg_match) verra restituito qule è stato il problema nell'accesso.  */

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

require_once 'gestioneEsperienzauser.php';
error_reporting(E_ALL &~E_NOTICE);
session_start();
// Se si apre la pagina si unsetta la sessione precendente togliendo i vari valori su session
if(isset($_COOKIE{'tipoSignIn'})) setcookie('tipoSignIn', "", time() - 3600);;

if(isset($_SESSION['userId'])){
    session_unset();
    session_destroy(); 
    $removeClientSession = true;
}

// Variabili per la gestione degli esiti del login
$esitovuoto="I campi sono vuoti";
$esitoerrore="Email e/o password errati";
$esitogrado="Hai grado zero! Non puoi accedere manda un messaggio agli admin.";
$flag=1; // variabile di controllo per gli esiti

// Se viene premuto il tasto accedi si effettua la connessione al DB e si fa partire la query di login
if(isset($_POST['Accedi']) ){
session_start();

    
    $table_users = "Tabella_Utenti";

    if (mysqli_connect_errno()){

        printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
    }
    $emailNickname = $_POST['EmailNickname'];
    $password = $_POST['Password'];
    // Si effettua la connessione al database usando password e l'email/nickname e fa partire la query 
    $queryLogin = "SELECT * FROM $table_users WHERE (Email='$emailNickname' OR Username='$emailNickname') AND Password='$password'";
    $resultQ = mysqli_query(connectDB(), $queryLogin);
    $num = mysqli_num_rows($resultQ);

    // Se il numero di righe restituite dalla query è 1 allora l'utente esiste e puo essere loggato
    if($num == 1){
        $flag=1;
        session_start();
        $row=mysqli_fetch_array($resultQ);
        
        // Caricamento del genere preferito dell'utente dal suo file XML, sfruttando il suo ID e il risultato della query
        $elem = xmlPointer('XML/utenti.xml');
        foreach($elem as $i){
            if($i->getAttribute('id_user') == $row['ID']){
                if($i->getElementsByTagName('GenerePreferito')->item(0)->textContent != '') $_SESSION['generePreferito'] = $i->getElementsByTagName('GenerePreferito')->item(0)->textContent;
                if($row['Tipologia_utente'] == 1) $agencyElem = $i->getElementsByTagName('ToggleAgency')->item(0)->textContent; 
                }
            }

        
    
        //All'interno della Session verranno caricate le informazioni fondamentali quali grado utente tipologia username e il suo id
        $_SESSION['userId'] = $row['ID'];
        $_SESSION['Email']=$row['Email'];
        $_SESSION['userName']=$row['Username'];
        $_SESSION['Esperienza']=$row['Esperienza'];
        $_SESSION['tipoUtente'] = $row['Tipologia_utente'];
        $_SESSION['Grado']=$row['Grado'];
        $_SESSION['Pixels']=$row['Pixels'];
        $_SESSION['Saldo']=$row['Saldo_attuale'];
        $_SESSION['agencyMod']=$agencyElem;
        $commenti=xmlPointer("XML/Commenti.xml");
        $mod = calcoloModCommenti($commenti);
        $_SESSION['modCommenti'] = $mod;

        $doc = getDoc("XML/Recovery.xml");
        $root = $doc->documentElement;
        $controllerRecovery = $root->childNodes;

        foreach($controllerRecovery as $c){
            if($c->getElementsByTagName('text')[0]->textContent == $_SESSION['Email']){
                $root->removeChild($c);
            }
        }
        $doc->save("XML/Recovery.xml");


        
        

        
        
        
        // Una volta fatto questo verremo reindirizzati alla Homepage
        if($_SESSION['Grado']>0) header("Location: Homepage.php");
        else if ($_SESSION['Grado'] == 0){ // Controllo se l'utente ha grado 0
        $flag=4;
    }
    }
    
    if($flag != 4){
    if($emailNickname==="" ?? $password ===""){ // Controllo se i campi sono vuoti
         $flag=2;
    }

    else if($num<1){ // Controllo se i dati inseriti sono errati
        $flag=3;
    }

   } 

}
    

?>




<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Login - PixelHub</title>        
        <link rel="stylesheet" type="text/css" href="Stile/Login.css?v=1" />
        <script type="text/javascript" src="Script/estensioneLink.js"></script>
        <?php 
        if(isset($removeClientSession)){
            echo "<script>";
            echo "sessionStorage.removeItem(\"idUser\");";
            echo "sessionStorage.removeItem(\"genPref\");";
            echo "</script>"; 
        }
         ?>
        
    </head>
    <body>
        <div id="logo">
                    <a href="Homepage.php">
                    <img src="Loghi/logo pixelhub slim.png" alt="logo pixelhub" />
                    </a>
                </div>
        <!-- Card centrale per il login utente -->
        <div id="LoginCard">
            <div class="loginForm">
                
                <?php
                    // Gestione degli esiti del login in base al valore della variabile flag
                    if($flag == 2){
                    echo "<div id=\"esito\"> <p>$esitovuoto</p> </div>"; 
                    }
                    if($flag == 3){
                    echo "<div id=\"esito\"> <p>$esitoerrore</p> </div>"; 
                    }
                    if($flag == 4){
                    echo "<div id=\"esito\"> <p>$esitogrado</p> </div>"; 
                    }
                    
                    ?>
                <!-- Form per il login utente che invia i dati alla stessa pagina mediante POST -->
                <form action="login.php" method="post">
                    
                    <div id="Email">
                        <p>Email or Nickname</p>
                        <input type="text" placeholder="example@mail.com" name="EmailNickname"/>
                        
                    </div>
                    <div id="Password">
                        <p>Password</p>
                        <input type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" name="Password"/>
                    </div>
                    <div class="recovery">

                         <!-- attraverso questi link si puo passare alla pagina del sighin passando il tipo di utente che vorrebbe iscriversi mediante GET -->
                        <p>Hai dimeticato le tue <a href="Recovery.php">credenziali</a>?</p>
                        <p>Sei nuovo?   <a href="signin.php?TipoUtente=0">Iscriviti</a>!</p>
                        <p>Sei un nuovo    <a href="signin.php?TipoUtente=1">publisher</a>?</p>     
                        <p>Sei un nuovo    <a href="signin.php?TipoUtente=2">admin</a>?</p>
                       

                    </div>

                    <div>
                        <input id="submit" type="submit" name="Accedi" value="Accedi"  />
                    </div>
                    
                   
                </form>
            </div>
            
        </div>
        <div id="footer">
            <ul>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li><br /> 
                <li>&copy; 2026 Pixel Hub. Tutti i diritti riservati.</li>
                </ul>
        </div>
    </body>
</html>