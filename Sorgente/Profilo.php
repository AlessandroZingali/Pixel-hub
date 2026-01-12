<?php 


    $service = 0;
    $utente = "";

    session_start();
    if(isset($_SESSION['userId'])){
        
        $utente = $_SESSION['userName'];
        $service = 1;
    }

    $table_users = "Tabella_Utenti";

if (isset($_POST["cambiaUsername"]) && !empty($_POST["newUsername"])) {

    echo"helo";
    $db_name = "Database_Pixel_Hub";
    $table_users = "Tabella_Utenti";
    $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

    if (mysqli_connect_errno()){

        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }

    $newUsername = mysqli_real_escape_string($mysqliConnection, $_POST["newUsername"]);

    $sql = "
        UPDATE $table_users
        SET Username = '$newUsername'
        WHERE ID = ".(int)$_SESSION['userId']."
    ";

    if (mysqli_query($mysqliConnection, $sql)) {
        
        echo "mod";
        header("Location:login.php");
    } else {
        echo "err";
    }
}


?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Il mio profilo</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/Profilo.css?v=3" /> 
        <script>
            <?php  
            if($service == 1 && isset($_SESSION['generePreferito'])){
                echo "sessionStorage.setItem(\"idUser\", \"".$_SESSION['userId']."\");";
                echo "sessionStorage.setItem(\"genPref\", \"".$_SESSION['generePreferito']."\");";
            }
            ?>
            
        </script>

        <script type="text/javascript" src="Script/cardProfileChanger.js?v=3"> </script>
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
                        if($service == 0) echo "<li><a href=\"login.php\">Log in </a></li>";
                        else if($service == 1){
                            echo "<script>";
                            echo "sessionStorage.removeItem(\"idUser\");";
                            echo "sessionStorage.removeItem(\"genPref\");";
                            echo "</script>";
                            echo "<li><a href=\"login.php\">Log out </a></li>";
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
                    
                        <div class="propic">
                            <img src='Loghi/propicblank.jpg' alt="Immagine di Default"/>
                        </div>
                        <div id="infoBox">
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
                
                                        </table>";
                            ?>
                        </div>
                        <div class="buttons">
                            <div class="settings">
                                <button onclick="swapperIn()"><img src="Stile/settingsicon.png" alt="settingbutton" ></button>
                            </div>
                            <div class="shop">
                                <button><img src="Stile/shopicon.png" alt="shopbutton" ></button>
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
                                <button onclick="swapperIn()"><img src="Stile/iconafreccia.png" alt="settingbutton" ></button>
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
                            <tr><form>
                                <td>Modifica Email</td>
                                <td><input type="text" placeholder="Inserisci l'email nuova..." name="newEmail" ></input>    </td>
                                <td><input type="submit" name="cambiaEmail" value="Modifica l'email"/></td></form>
                            </tr>  
                            <tr>
                                <td>Modifica Genere Preferito</td>
                            
                                    <form> 
                                        <td>
                                        <select name="Generi" id="GeneriScelta">
                                            <option value="FPS">Sparatutto in prima persona</option> 
                                            <option value="GDR">Gioco di Ruolo</option>
                                            <option value="Action">Azione</option>
                                            <option value="Souls-like">Souls</option>
                                            <option value="Strategia">Strategia</option>
                                        </select>  
                                    
                                </td> 
                                <td><input type="submit" name="cambiaGenere" value="Modifica il mio genere preferito"/></td> 
                                </form> 
                            </tr>
                                <tr>
                                <form>    
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
                                <form>
                                    <td>Modifica Password</td>
                                    <td>
                                        
                                        <input type="text" placeholder="Inserisci la tua nuova password" name="newPass" />
                                       
                                        
                                    </td>
                                    <td>
                                        <input type="submit" name="cambiaPassword" value="Modifica la tua password"/>
                                    
                                    </td>
                                </form>
                            <tr>
                        </table>
                        
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
        