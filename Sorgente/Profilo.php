<?php 


    $service = 0;
    $utente = "";
    $invalidFlag=0;

    session_start();
    if(isset($_SESSION['userId'])){
        
        $utente = $_SESSION['userName'];
        $service = 1;
    }

    $table_users = "Tabella_Utenti";

if (isset($_POST["cambiaUsername"]) && !empty($_POST["newUsername"])) {


    $db_name = "Database_Pixel_Hub";
    $table_users = "Tabella_Utenti";
    $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

    if (mysqli_connect_errno()){

        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }

    $new = mysqli_real_escape_string($mysqliConnection, $_POST["newUsername"]);

    $sql = "
        UPDATE $table_users
        SET Username = '$new'
        WHERE ID = ".(int)$_SESSION['userId']."
    ";

    if (mysqli_query($mysqliConnection, $sql)) {
        
        header("Location:login.php");
    } else {
         printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }
}

if (isset($_POST["cambiaEmail"]) && !empty($_POST["newEmail"])) {

    if(preg_match('/^.*@.*$/', $_POST['newEmail'])){
        $db_name = "Database_Pixel_Hub";
        $table_users = "Tabella_Utenti";
        $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

        if (mysqli_connect_errno()){

            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

        $new = mysqli_real_escape_string($mysqliConnection, $_POST["newEmail"]);

        $sql = "
            UPDATE $table_users
            SET Email = '$new'
            WHERE ID = ".(int)$_SESSION['userId']."
        ";

        if (mysqli_query($mysqliConnection, $sql)) header("Location:login.php");
        else printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        
    }
    else $invalidFlag = 1;

    
}

if (isset($_POST["cambiaPass"]) && !empty($_POST["newPass"])) {

    if(preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['newPass'])){
            $db_name = "Database_Pixel_Hub";
            $table_users = "Tabella_Utenti";
            $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

            if (mysqli_connect_errno()){

                printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
            }

            $new = mysqli_real_escape_string($mysqliConnection, $_POST["newPass"]);

            $sql = "
                UPDATE $table_users
                SET  = '$new'
                WHERE ID = ".(int)$_SESSION['userId']."
            ";

            if (mysqli_query($mysqliConnection, $sql)) header("Location:login.php");
            else printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));    
    }
    else $invalidFlag = 2; 
}

if (isset($_POST["cambiaGenere"]) && !empty($_POST["Genere"])) {
 
    $idUtente=$_SESSION["userId"];

    $xmlString="";
                                                            
    foreach(file("XML/utenti.xml") as $node){ 
        $xmlString .= trim($node);
    }
    
    $doc= new DOMDocument();
    $doc->loadXML($xmlString);
    $doc->formatOutput = true;
    $root=$doc->documentElement;
    $elem=$root->childNodes;
    foreach($elem as $userNode){
        if($userNode->getAttribute('id_user') == $idUtente){ 
            $userNode->getElementsByTagName('GenerePreferito')->item(0)->textContent=$_POST["Genere"];
            $_SESSION['generePreferito'] = $_POST['Genere'];
            }
    }
    $doc->save("XML/utenti.xml");
}

if (isset($_POST["cambiaSocial"]) && !empty($_POST["newSocial"])) {

    $idUtente=$_SESSION["userId"];

    $xmlString="";
                                                            
    foreach(file("XML/utenti.xml") as $node){ 
        $xmlString .= trim($node);
    }
    
    $doc= new DOMDocument();
    $doc->loadXML($xmlString);
    $doc->formatOutput = true;
    $root=$doc->documentElement;
    $elem=$root->childNodes;
    foreach($elem as $userNode){
        if($userNode->getAttribute('id_user') == $idUtente) $userNode->getElementsByTagName('linkEsterno')->item(0)->textContent=$_POST["newSocial"];
    }
    $doc->save("XML/utenti.xml");
}

if (isset($_POST["cambiaCasa"]) && !empty($_POST["newCasa"])) {

    $idUtente=$_SESSION["userId"];

    $xmlString="";
                                                            
    foreach(file("XML/utenti.xml") as $node){ 
        $xmlString .= trim($node);
    }
    
    $doc= new DOMDocument();
    $doc->loadXML($xmlString);
    $doc->formatOutput = true;
    $root=$doc->documentElement;
    $elem=$root->childNodes;
    foreach($elem as $userNode){
        if($userNode->getAttribute('id_user') == $idUtente) $userNode->getElementsByTagName('CasaDiSviluppoPreferita')->item(0)->textContent=$_POST["newCasa"];
    }
    $doc->save("XML/utenti.xml");
}

if (isset($_POST["AcquistoPic"]) && isset($_POST["scelta"])) {

    $idpicscelto=$_POST["scelta"];

    $idUtente=$_SESSION["userId"];

    $xmlString="";
                                                            
    foreach(file("XML/utenti.xml") as $node){ 
        $xmlString .= trim($node);
    }
    
    $doc= new DOMDocument();
    $doc->loadXML($xmlString);
    $root=$doc->documentElement;
    $elem=$root->childNodes;
    foreach($elem as $userNode){
        if($userNode->getAttribute('id_user') == $_SESSION['userId']){
            if($userNode->getElementsByTagName('listaPropic')->item(0) != null){
                $pics= $userNode->getElementsByTagName('listaPropic')->item(0)->getElementsByTagName('idPropic');
                foreach($pics as $pic){ 
                    if($pic->textContent == $idpicscelto) $invalidFlag = 4;
                }
            }
        }
    }
    if($invalidFlag == 0){
        $xmlString="";
                                                            
        foreach(file("XML/ProfilePic.xml") as $node){ 
            $xmlString .= trim($node);
        }
        
        $doc= new DOMDocument();
        $doc->loadXML($xmlString);
        $root=$doc->documentElement;
        $elem=$root->childNodes; 
        foreach($elem as $pics){
            if($pics->getAttribute('id_pic') == $idpicscelto){
                $prezzo=$pics->getElementsByTagName('prezzo')->item(0)->textContent;
                $newPath = $pics->getElementsByTagName('path')->item(0)->textContent;
            }
        }

        $db_name = "Database_Pixel_Hub";
        $table_users = "Tabella_Utenti";
        $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

        if (mysqli_connect_errno()){

            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

        $sql="SELECT Pixels FROM $table_users WHERE ID = ".(int)$_SESSION['userId'].";";
        $resultQ = mysqli_query($mysqliConnection, $sql);
        
        if ($resultQ){
            $row = mysqli_fetch_array($resultQ);
            if($row['Pixels']<$prezzo) $invalidFlag=3;
        }
        
        if($invalidFlag == 0){

            $sql = "
            UPDATE $table_users
            SET  Pixels = Pixels - $prezzo, imgProfiloPath = \"$newPath\"
            WHERE ID = ".(int)$_SESSION['userId'].";
            ";

            if (mysqli_query($mysqliConnection, $sql)) {
                $xmlString="";
                                                                    
                foreach(file("XML/utenti.xml") as $node){ 
                    $xmlString .= trim($node);
                }
                
                $doc= new DOMDocument();
                $doc->loadXML($xmlString);
                $doc->formatOutput = true;
                $root=$doc->documentElement;
                $elem=$root->childNodes;
                foreach($elem as $userNode){
                    
                    if($userNode->getAttribute('id_user') == $idUtente){
                        $nuovaPic = $doc->createElement("idPropic");
                        $nuovaPic->textContent=$idpicscelto;
                        $picRoot = $userNode->getElementsByTagName("listaPropic")->item(0);
                        $picRoot->appendChild($nuovaPic);
                    }


                }
                $doc->save("XML/utenti.xml");
                /*header("Location:Profilo.php");*/
            }
            else printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        
        }
    }
    
   
}

if (isset($_POST["cambiaImmagine"]) && !empty($_POST["newPropic"])){


    $newPath = $_POST['newPropic'];
                    

    $db_name = "Database_Pixel_Hub";
    $table_users = "Tabella_Utenti";
    $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

    if (mysqli_connect_errno()){

        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }

    $sql="UPDATE $table_users SET imgProfiloPath = \"$newPath\"
    WHERE ID = ".(int)$_SESSION['userId'].";
        ";
    

    if(!(mysqli_query($mysqliConnection, $sql))) printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    else header('Location: Profilo.php');
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
            <div id="header">
                <div id="logo">
                
                    <img src='Loghi/logo pixelhub slim.png' alt="Logo di Pixel Hub" id="logoimg"/>
                
                </div>
                
                <h2>Il tuo shop preferito di videogiochi</h2>
 
            </div>

            <div id="navigation">
                <div class="dropMenu">
                    <button class="botMenu"><img src="Stile/iconamenu.png" alt=""></button>
                    <ul class ="submenu">
                        <?php
                        //Gestiamo la visualizzazione del link di login o logout in base allo stato di $service, il quale ricordiamo è la flag di stato dell'utente (guest o loggato).
                        // Come si può vedere se il service non è attivo (guest) eliminiamo anche le informazioni salvate in sessionStorage riguardo l'utente.
                        if($service == 1) echo "<li><a href=\"login.php\">Log out </a></li>";
                        else if($service == 0){
                            echo "<script>";
                            echo "sessionStorage.removeItem(\"idUser\");";
                            echo "sessionStorage.removeItem(\"genPref\");";
                            echo "</script>";
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


                        

                            
                                

                                $xmlString="";
                                                        
                                foreach(file("XML/utenti.xml") as $node){ 
                                    $xmlString .= trim($node);
                                }
                                
                                $doc= new DOMDocument();
                                $doc->loadXML($xmlString);
                                $root=$doc->documentElement;
                                $elem=$root->childNodes;

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
                        
                        <div class="buttons">
                            <div class="settings">
                                <button onclick="swapperInSettings()"><img src="Stile/settingsicon.png" alt="settingbutton" ></button>
                            </div>
                            <div class="shop">
                                <button onclick="swapperInStore()"><img src="Stile/shopicon.png" alt="shopbutton" ></button>
                            </div>
                        </div>
                    
                    </div>


                    
                    <div class="flexGridGames">
                        <div><h3 id="lastTitle">Ultimi Acquisti</h3></div>
                        <div class="lastGames">
                            <?php
                            $xmlString = "";
                            foreach(file("XML/utenti.xml") as $node){
                                $xmlString.=trim($node);
                            }
                            $doc = new DOMDocument();
                            $doc->loadXML($xmlString);
                            $root = $doc->documentElement;
                            $utente = $root->childNodes;

                            foreach($utente as $u){
                                if($u->getAttribute('id_user') == $_SESSION['userId']){
                                    $gameList = $u->getElementsByTagName('listaGiochi')->item(0)->getElementsByTagName("idGiocoPosseduto");
                                    //print_r($gameList);
                                    $idContainer = [];

                                    foreach($gameList as $id){
                            
                                        $idContainer[] =  $id->textContent;
                                        }
                                
                                    rsort($idContainer);
                                    //print_r($idContainer);
                                    
                                    $xmlString = "";
                                    foreach(file("XML/Giochi.xml") as $node){
                                        $xmlString.=trim($node);
                                    }
                                    $doc = new DOMDocument();
                                    $doc->loadXML($xmlString);
                                    $root = $doc->documentElement;
                                    $giochi = $root->childNodes;
                                    //print_r($giochi);
                                    $count = 0;

                                    
                                        foreach($idContainer as $i){
                                            for($j = ($giochi->length)-1 ; $j>=0; $j--){
                                            $g=$giochi->item($j);
                                            //echo "Container: ".$i." id: ".$g->getAttribute("id_gioco");
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
                <div class="cardSettings hideCard" id="card2">
                    <div class="buttons">
                        <div class="backarrow">
                            <button onclick="swapperInSettings()"><img src="Stile/iconafreccia.png" alt="settingbutton" ></button>
                        </div>

                    </div>
                    <div>

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
                                                        <tr> 
                                <form method="post" action="Profilo.php">
                                    <td>Modifica Immagine Profilo</td>
                                    <td>
                                        
                                        <select name="newPropic">
                                        <?php  
                                            $xmlString="";
                                            foreach(file("XML/utenti.xml") as $node){ 
                                                $xmlString .= trim($node);
                                            }
                                            
                                            $doc= new DOMDocument();
                                            $doc->loadXML($xmlString);
                                            $root=$doc->documentElement;
                                            $elem=$root->childNodes; 
                                            foreach($elem as $userNode){
                                                if($userNode->getAttribute('id_user') == $_SESSION['userId']){
                                                    if($userNode->getElementsByTagName('listaPropic')->item(0) != null){
                                                        $pics= $userNode->getElementsByTagName('listaPropic')->item(0)->getElementsByTagName('idPropic');
                                                        foreach($pics as $pic){
                                                            $xmlString="";
                                                            foreach(file("XML/ProfilePic.xml") as $node){ 
                                                                $xmlString .= trim($node);
                                                            }
                                                
                                                            $doc2= new DOMDocument();
                                                            $doc2->loadXML($xmlString);
                                                            $root2=$doc2->documentElement;
                                                            $imgs=$root2->childNodes; 
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
                
                <div class="cardPicStore hideCard" id="card3">
                    <div class="backarrow">
                            <button onclick="swapperInStore()"><img src="Stile/iconafreccia.png" alt="settingbutton" ></button>
                        </div>
                    <div><h2>Store immagini profilo</h2></div>
                    <div class="gridPicStore">
                        <?php 
                       
                            $xmlString = "";
                            foreach(file("XML/ProfilePic.xml") as $node){
                                $xmlString.=trim($node);
                            }
                            $doc = new DOMDocument();
                            $doc->loadXML($xmlString);
                            $root = $doc->documentElement;
                            $elem = $root->childNodes;
                            echo "<form method=\"post\" action=\"Profilo.php\">";
                            echo "<div class=\"gridPicStoreForm\">";
                            foreach($elem as $pic){
                                
                                echo "<div class=\"sceltaPic\">";
                                echo "<div><img src=\"".$pic->getElementsbyTagName('path')->item(0)->textContent."\" alt=\"".$pic->getElementsbyTagName('nome')->item(0)->textContent."\"></div>";
                                echo "<div><p>".$pic->getElementsbyTagName('nome')->item(0)->textContent."</p></div>";
                                echo "<div><p>".$pic->getElementsbyTagName('prezzo')->item(0)->textContent." Pixels</p></div>";
                                echo "<div> <input type=\"radio\" name=\"scelta\" value=\"". $pic->getAttribute('id_pic') . "\" /></div>";
                                echo "</div>";
                            }
                            
                            echo "</div>";
                            echo "<div><input type=\"submit\" name=\"AcquistoPic\" value=\"Acquista\"/></div>";
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
        