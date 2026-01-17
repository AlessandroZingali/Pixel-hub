<?php 
// Pagina del profilo utente:
// permette di modificare dati personali, acquistare immagini profilo
// e aggiornare informazioni salvate sia su DB che su XML

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

$service = 0;          // indica se l’utente è loggato
$utente = "";          // username dell’utente
$invalidFlag = 0;      // flag per gestire errori logici (email, password, acquisti ecc.)

// Avvio sessione
session_start();

// Controllo se l’utente è loggato
if (isset($_SESSION['userId'])) {
    $utente = $_SESSION['userName'];
    $service = 1;
}

// Nome tabella utenti
$table_users = "Tabella_Utenti";


//    CAMBIO USERNAME (DB)

if (isset($_POST["cambiaUsername"]) && !empty($_POST["newUsername"])) {

    // Connessione al database
    $db_name = "Database_Pixel_Hub";
    $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

    // Controllo errori di connessione
    if (mysqli_connect_errno()) {
        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }

    // Sanificazione input
    $new = mysqli_real_escape_string($mysqliConnection, $_POST["newUsername"]);

    // Query di aggiornamento username
    $sql = "
        UPDATE $table_users
        SET Username = '$new'
        WHERE ID = ".(int)$_SESSION['userId']."
    ";

    // Esecuzione query
    if (mysqli_query($mysqliConnection, $sql)) {
        header("Location:login.php"); // forza nuovo login
    } else {
        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }
}


//    CAMBIO EMAIL (DB)

if (isset($_POST["cambiaEmail"]) && !empty($_POST["newEmail"])) {

    // Controllo formato email
    if (preg_match('/^.*@.*$/', $_POST['newEmail'])) {

        $db_name = "Database_Pixel_Hub";
        $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

        if (mysqli_connect_errno()) {
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

        $new = mysqli_real_escape_string($mysqliConnection, $_POST["newEmail"]);

        // Update email
        $sql = "
            UPDATE $table_users
            SET Email = '$new'
            WHERE ID = ".(int)$_SESSION['userId']."
        ";

        if (mysqli_query($mysqliConnection, $sql)) {
            header("Location:login.php");
        } else {
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

    } else {
        $invalidFlag = 1; // email non valida
    }
}


// Cambio password
if (isset($_POST["cambiaPass"]) && !empty($_POST["newPass"])) {

    // Password con:
    // - almeno una maiuscola
    // - almeno un carattere speciale
    // - minimo 8 caratteri
    if (preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['newPass'])) {

        $db_name = "Database_Pixel_Hub";
        $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

        if (mysqli_connect_errno()) {
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

        $new = mysqli_real_escape_string($mysqliConnection, $_POST["newPass"]);

        // Query di aggiornamento password
        $sql = "
            UPDATE $table_users
            SET  = '$new'
            WHERE ID = ".(int)$_SESSION['userId']."
        ";

        if (mysqli_query($mysqliConnection, $sql)) {
            header("Location:login.php");
        } else {
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

    } else {
        $invalidFlag = 2; // password non valida
    }
}



//    CAMBIO GENERE PREFERITO (XML)

if (isset($_POST["cambiaGenere"]) && !empty($_POST["Genere"])) {

    $idUtente = $_SESSION["userId"];

    $doc = getDoc("XML/utenti.xml");
    $root = $doc->documentElement;
    $elem = $root->childNodes;

    // Aggiornamento genere preferito
    foreach ($elem as $userNode) {
        if ($userNode->getAttribute('id_user') == $idUtente) {
            $userNode->getElementsByTagName('GenerePreferito')->item(0)->textContent = $_POST["Genere"];
            $_SESSION['generePreferito'] = $_POST['Genere'];
        }
    }

    $doc->save("XML/utenti.xml");
}


//    CAMBIO SOCIAL (XML)

if (isset($_POST["cambiaSocial"]) && !empty($_POST["newSocial"])) {

    $idUtente = $_SESSION["userId"];

    $doc = getDoc("XML/utenti.xml");
    $root = $doc->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $userNode) {
        if ($userNode->getAttribute('id_user') == $idUtente) {
            $userNode->getElementsByTagName('linkEsterno')->item(0)->textContent = $_POST["newSocial"];
        }
    }

    $doc->save("XML/utenti.xml");
}



//    CAMBIO CASA DI SVILUPPO (XML)

if (isset($_POST["cambiaCasa"]) && !empty($_POST["newCasa"])) {

    $idUtente = $_SESSION["userId"];
    
    $doc = getDoc("XML/utenti.xml");
    $root = $doc->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $userNode) {
        if ($userNode->getAttribute('id_user') == $idUtente) {
            $userNode->getElementsByTagName('CasaDiSviluppoPreferita')->item(0)->textContent = $_POST["newCasa"];
        }
    }

    $doc->save("XML/utenti.xml");
}

?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Il mio profilo</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/Profilo.css?v=3" />
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" />  
        <script>
            <?php  
            if($service == 1 && isset($_SESSION['generePreferito'])){
                echo "sessionStorage.setItem(\"idUser\", \"".$_SESSION['userId']."\");";
                echo "sessionStorage.setItem(\"genPref\", \"".$_SESSION['generePreferito']."\");";
            }
            ?>
            
        </script>
        <script type="text/javascript" src="Script/cardProfileChanger.js?v=3"> </script>
        <?php
        if($invalidFlag > 0){ 
            echo "<script>";
            echo "localStorage.setItem(\"invalidFlag\", $invalidFlag);";
            echo "</script>";
        }  
    
    ?>
        
    </head>
    <body>
        <div id="container">
             <div id="header"><!--header della pagina -->
                <div id="logo"><img src='Loghi/logo pixelhub slim.png' alt="Logo di Pixel Hub" id="logoimg"/></div>
                <h2>Il tuo shop preferito di videogiochi</h2>
            </div>

            <div id="navigation">
                <div class="dropMenu">
                    <button class="botMenu"><img src="Stile/Icone/iconamenu.png" alt=""></button>
                    <ul class ="submenu">
                        <?php
                        if($service == 1) echo "<li><a href=\"login.php\">Log out </a></li>";
                        else if($service == 0){
                            if(isset($_SESSION['userId']) && isset($_SESSION['generePreferito'])){
                                echo "<script>";
                                echo "sessionStorage.removeItem(\"idUser\");";
                                echo "sessionStorage.removeItem(\"genPref\");";
                                echo "</script>"; 
                            }
                            echo "<li><a href=\"login.php\">Log in </a></li>";
                        }
                        ?>
                        
                        <li><a href="Homepage.php">Home</a></li>
                        <li><a href="carrello.php">Carrello </a></li>
                        <li><a href="catalogo.php">Catalogo </a></li>
                        
                        <!-- <li><a href="Creadatabasepixelhub.php">data</a></li> -->
                        <?php 
                        if($service == 1) echo "<li><a href=\"Profilo.php\">Profilo di $utente </a></li>";
                        ?>
                    </ul>
                </div>
                    <form id="searchBar" onsubmit="return false;">
                        <input id="searchBarInput" type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>
            <!-- all'interno del wrapper che sara il divisore principale ci saranno contenuti la card profilo dove saranno mostrate tutte le informazioni principali del l'account con gli ultimi 4 giochi acquistati e due pulsanti  -->

            <div class="wrapper">
                <div class="cardProfilo" id="card1">
                    <div class="baseProfilo" >
                
                    
                        <?php 
                            
                            $db_name = "Database_Pixel_Hub";
                            $table_users = "Tabella_Utenti";
                            $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

                            if (mysqli_connect_errno()){

                                printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                            }
                            $emailNickname = $_SESSION['user'];

                            $queryLogin = "SELECT * FROM $table_users WHERE (Email='$emailNickname' OR Username='$emailNickname')";
                            $resultQ = mysqli_query($mysqliConnection, $queryLogin);
                            $num = mysqli_num_rows($resultQ); 
                            if($num == 1){
                                $flag=1;
                                
                                $row=mysqli_fetch_array($resultQ);
                                echo "<div class=\"propic\"> 
                                <img src=\"".$row['imgProfiloPath']."\" alt=\"Immagine di Default\"/>
                                </div>";  

                                echo "<div id=\"infoBox\">";

                                $elem = xmlPointer("XML/utenti.xml");
                             // Caricamento file XML utente con le varie informazioni
                                foreach($elem as $i){
                                    if($i->getAttribute('id_user') == $row['ID']){
                                        if($i->getElementsByTagName('GenerePreferito')->item(0)->textContent != '') $GenerePref = $i->getElementsByTagName('GenerePreferito')->item(0)->textContent;
                                        else $GenerePref = "nessuno";
                                        if($i->getElementsByTagName('CasaDiSviluppoPreferita')->item(0)->textContent != '') $CasaSvilPref = $i->getElementsByTagName('CasaDiSviluppoPreferita')->item(0)->textContent;
                                        else $CasaSvilPref = "nessuna";
                                        if($i->getElementsByTagName('DataIscrizione')->item(0)->textContent != '') $DataIsc= $i->getElementsByTagName('DataIscrizione')->item(0)->textContent;
                                        else $DataIsc = "!Errore!::Informazione non presente, si prega di ricontrollare le impostazioni di iscrizione";
                                        if($i->getElementsByTagName('linkEsterno')->item(0)->textContent != '') $Contatti = $i->getElementsByTagName('linkEsterno')->item(0)->textContent;
                                        else $Contatti = "nessuno";
                                        }
                                    }
                            }
                            echo "<table class=\"info\">

                                    <tr>
                                        <td>Username:</td> <td> $utente </td>
                                    </tr>
                                    <tr>
                                        <td>Email: </td><td> ".$row['Email']."</td>
                                    </tr>
                                    
                                    

                                    <tr>
                                    <td>Numero di Pixel in possesso:</td> 
                                    <td> ".$row['Pixels']."</td>
                                    </tr>

                                    <tr>
                                    <td>Grado attuale: </td> <td> ".$row['Grado']."</td>
                                    </tr>

                                    

                                

                                    <tr>
                                        <td>Il mio genere preferito:</td><td> $GenerePref</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Data Iscrizione:</td> <td>$DataIsc</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>I miei contatti:</td> <td><a href=\"$Contatti\"> Link social </a></td>
                                    </tr>
                                    
                                    <tr>
                                        <td>La mia casa di sviluppo preferita:</td><td>$CasaSvilPref</td>
                                    </tr>
        
                                </table>
                            </div>";
                        ?>
                        <!-- nella classe buttons ci sono due pulsanti che cambiano la pagina mettendo usando una funzione in javascript la proprietà di stile hidden nella cardProfilo  e mostra rispettivamente la card delle impostazioni del profilo e quella dello store per le immagini profilo -->
                        
                        <div class="buttons">
                            <div class="settings">
                                <button onclick="swapperInSettings()"><img src="Stile/Icone/settingsicon.png" alt="settingbutton" ></button>
                            </div>
                            <div class="shop">
                                <button onclick="swapperInStore()"><img src="Stile/Icone/shopicon.png" alt="shopbutton" ></button>
                            </div>
                        </div>
                    
                    </div>


                    <!-- piccola griglia con gli ultimi 4 giochi posseduti -->
                    <div class="flexGridGames">
                        <div><h3 id="lastTitle">Ultimi Acquisti</h3></div>
                        <div class="lastGames">
                            <?php
                                $utente = xmlPointer("XML/utenti.xml");

                                foreach($utente as $u){
                                    if($u->getAttribute('id_user') == $_SESSION['userId']){
                                        $gameList = $u->getElementsByTagName('listaGiochi')->item(0)->getElementsByTagName("idGiocoPosseduto");
                                        
                                        $idContainer = [];

                                        foreach($gameList as $id){
                                            $idContainer[] =  $id->textContent;
                                        }

                                        //Come per il catalogo si inseriscono nell array idcontainer gli id di tutti i giochi posseduti dall'utente e 
                                        //si effettua un reverse sort per invertire l'ordine 
                                        rsort($idContainer);
                                    
                                        
                                        $giochi = xmlPointer("XML/Giochi.xml");

                                        $count = 0;

                                        // infine si prendono da Giochi.xml i dati dei 4 giochi 
                                            foreach($idContainer as $i){
                                                for($j = ($giochi->length)-1 ; $j>=0; $j--){
                                                $g=$giochi->item($j);
                                                if($i == $g->getAttribute("id_gioco") && $count < 4){
                                                    $titolo=$g->getElementsByTagName('Titolo')->item(0)->textContent;
                                                    $idGioco=$g->getAttribute("id_gioco");
                                                    
                                                    echo "<div class=\"lastgame\"><img src=\"".$g->getElementsByTagName('Immagine')->item(0)->textContent."\" onclick=\"location.href='Gamepage.php?titoloGioco=$titolo&idGioco=$idGioco'\" alt=\"".$g->getElementsByTagName('Titolo')->item(0)->textContent."\"/></div>";
                                                    $count++;
                                                }
                                                }
                                            }
                                        
                                    }
                                }

                            
                            ?>
                            </div>
                    
                        
                    </div>
                </div>
                <!-- bottone che fa ritornare alla pagina precedente per dalle impostazioni profilo -->
                <div class="cardSettings hideCard" id="card2">
                    <div class="buttons">
                        <div class="backarrow">
                            <button onclick="swapperInSettings()"><img src="Stile/Icone/iconafreccia.png" alt="settingbutton" ></button>
                        </div>

                    </div>
                    <div>
                    <!-- All interno della tabella vi è presente una form con la quale è possibile modificare il proprio profilo e le informazioni personali -->
                        <table>
                            <tr>
                                <form method="post" action="Profilo.php">
                                    <td>Modifica Username</td> 
                                    <td><input type="text" placeholder="Inserisci l'username nuovo..." name="newUsername" /></td> 
                                    <td><input type="submit" name="cambiaUsername" value="Modifica l'username"/></td>
                                </form>
                            </tr> 
                            <tr><form method="post" action="Profilo.php">
                                <td>Modifica Email</td>
                                <td><input type="text" placeholder="Inserisci l'email nuova..." name="newEmail" ></input>    </td>
                                <td><input type="submit" name="cambiaEmail" value="Modifica l'email"/></td></form>
                            </tr>  
                            <tr>
                                <td>Modifica Genere Preferito</td>
                            
                                    <form method="post" action="Profilo.php"> 
                                        <td>
                                        <select name="Genere" id="GeneriScelta">
                                            <option value="Sparatutto">Sparatutto</option> 
                                            <option value="RPG">RPG</option>
                                            <option value="Avventura">Avventura</option>
                                            <option value="Souls-like">Souls-like</option>
                                            <option value="Strategia">Strategia</option>
                                            <option value="Rouge-like">Rouge-like</option>
                                        </select>  
                                    
                                </td> 
                                <td><input type="submit" name="cambiaGenere" value="Modifica il mio genere preferito"/></td> 
                                </form> 
                            </tr>
                                <tr>
                                <form method="post" action="Profilo.php">    
                                    <td>Modifica Casa di sviluppo preferita</td>
                                    <td>
                                    
                                        <input type="text" placeholder="Inserisci la tua casa di sviluppo preferita" name="newCasa" />
                                      
                                    
                                    </td>
                                    <td>
                                        <input type="submit" name="cambiaCasa" value="Modifica la tua casa di sviluppo preferita"/>
                                    </td>
                                </form>
                            </tr>
                            <tr> 
                                <form method="post" action="Profilo.php">
                                    <td>Modifica Password</td>
                                    <td>
                                        
                                        <input type="text" placeholder="Inserisci la tua nuova password" name="newPass" />
                                       
                                        
                                    </td>
                                    <td>
                                        <input type="submit" name="cambiaPass" value="Modifica la tua password"/>
                                    
                                    </td>
                                </form>
                            <tr>

                            <tr> 
                                <form method="post" action="Profilo.php">
                                    <td>Modifica Link Social</td>
                                    <td>
                                        
                                        <input type="text" placeholder="Inserisci il tuo social link" name="newSocial" />
                                       
                                        
                                    </td>
                                    <td>
                                        <input type="submit" name="cambiaSocial" value="Modifica il tuo social link"/>
                                    
                                    </td>
                                </form>
                            <tr>
                                <!-- Per modificare l'immagine del profilo si carica prima da Utenti.xml l'elenco delle immagini profilo acquistate -> si prende l'id delle immagini
                                 -> e infine si carica nelle option i vari nomi corrispondenti agli id presenti in profilepic.xml  -->
                                <tr> 
                                <form method="post" action="Profilo.php">
                                    <td>Modifica Immagine Profilo</td>
                                    <td>
                                        
                                        <select name="newPropic">
                                        <?php  
                                            //Caricamento immagini profilo acquistate
                                            $elem = xmlPointer("XML/utenti.xml"); 

                                            foreach($elem as $userNode){
                                                if($userNode->getAttribute('id_user') == $_SESSION['userId']){
                                                    if($userNode->getElementsByTagName('listaPropic')->item(0) != null){
                                                        $pics= $userNode->getElementsByTagName('listaPropic')->item(0)->getElementsByTagName('idPropic');

                                                        // Caricamento immagini profilo disponibili
                                                        foreach($pics as $pic){

                                                            $imgs = xmlPointer("XML/ProfilePic.xml"); 
                                                            foreach($imgs as $img){ 
                                                                if($pic->textContent == $img->getAttribute('id_pic')) echo "<option value=\"".$img->getElementsByTagName('path')->item(0)->textContent."\">".$img->getElementsByTagName('nome')->item(0)->textContent."</option>";
                                                            }
                                                        }
                                                    }
                                                    
                                                }
                                            }
                                        ?>
                                        </select>
                                            
                                       
                                        
                                    </td>
                                    <td>
                                        <input type="submit" name="cambiaImmagine" value="Modifica la tua immagine profilo"/>
                                    
                                    </td>
                                </form>
                            <tr>
                        </table>
                    </div>
                </div>     
                <!-- Come sopra card del profilo cliccando sul pulsante si nasconde la pagina dello store e si ritorna a quella principale -->
                <div class="cardPicStore hideCard" id="card3">
                    <div class="backarrow">
                            <button onclick="swapperInStore()"><img src="Stile/Icone/iconafreccia.png" alt="settingbutton" ></button>
                        </div>
                    <div><h2>Store immagini profilo</h2></div>
                    <div class="gridPicStore">
                        <?php 
                       
                            $elem = xmlPointer("XML/ProfilePic.xml");
                            echo "<form method=\"post\" action=\"Profilo.php\">";
                            echo "<div class=\"gridPicStoreForm\">";
                            foreach($elem as $pic){
                                // nella griglia del negozio ci saranno presenti le immagini profilo il loro nome e il prezzo in pixels e un pulsante radio per la scelta di un singolo oggetto
                                
                                echo "<div class=\"sceltaPic\">";
                                echo "<div><img src=\"".$pic->getElementsbyTagName('path')->item(0)->textContent."\" alt=\"".$pic->getElementsbyTagName('nome')->item(0)->textContent."\"></div>";
                                echo "<div><p>".$pic->getElementsbyTagName('nome')->item(0)->textContent."</p></div>";
                                echo "<div><p>".$pic->getElementsbyTagName('prezzo')->item(0)->textContent." Pixels</p></div>";
                                echo "<div> <input type=\"radio\" name=\"scelta\" value=\"". $pic->getAttribute('id_pic') . "\" /></div>";
                                echo "</div>";
                            }
                            
                            echo "</div>";
                            echo "<div id=\"buttonAcquistoPic\"><input type=\"submit\" name=\"AcquistoPic\" value=\"Acquista\"/></div>";
                            echo "</form>"
                        ?>
                    </div> 
                </div> 
                </div>
            </div>
        </div>
            
            

           
            
            

        <div id="footer">
            <ul>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
        
    </body>
    

</html> 
        