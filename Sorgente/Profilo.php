<?php 


$service = 0;
$utente = "";

session_start();
if(isset($_SESSION['userId'])){
    
    $utente = $_SESSION['userName'];
    $service = 1;
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
        <script type="text/javascript" src="Script/GameTableGestione.js?v=3">  </script>
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
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
                        <li><a href="carrello.html">Carrello </a></li>
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

            <div class="cardProfilo">
                <div class="lastGames">
                    <?php
                    $xmlString = "";
                    
                    ?>
                </div>
            </div>
            
            <div class="baseProfilo">
                
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
                                    <td>Username: $utente </td>
                                </tr>
                                <tr>
                                    <td>Email: ".$row['Email']."</td>
                                </tr>


                                <tr>
                                    <td>Il mio genere preferito: $GenerePref</td>
                                </tr>
                                 <tr>
                                    <td>Data Iscrizione: $DataIsc</td>
                                </tr>
                                <tr>
                                    <td>I miei contatti: $Contatti</td>
                                </tr>
                                <tr>
                                    <td>La mia casa di sviluppo preferita:$CasaSvilPref</td>
                                </tr>

                                
                              </table>";
            ?>
            </div>
            <div id="settings">
                <button><img src="Stile/settingsicon.png" alt="settingbutton" ></button>
            </div>
                
            

            </div>

        <div id="footer">
            <ul>
                <li><a href="">Contact Us</a></li>
                <li><a href="">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
        
    </body>
</html> 
        