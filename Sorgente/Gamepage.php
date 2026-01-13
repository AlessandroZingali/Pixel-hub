<?php 
$service = 0;
$utente = "";
$titoloGioco = "";
$gioco = 0;

if(!isset($_GET['titoloGioco']) || !isset($_GET['idGioco'])){
    header("Location: Homepage.php");
}

$titoloGioco = $_GET['titoloGioco'];
$idGioco = $_GET['idGioco'];

session_start();
if(isset($_SESSION['userId'])){
    $utente = $_SESSION['userName'];
    $service = 1;
}
echo "";
?>

<?xml version="1.0" encoding="UTF-8"?>
<?php 
    if(isset($_POST["invioCommento"])){

        $xmlString="";
                                
        foreach(file("XML/Commenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
            foreach($elem as $i){
                if($i->getAttribute("id_gioco")==$idGioco){
                    $gioco = (int)$i->getAttribute("id_gioco");
                    break;
                }
            }
        if($gioco == 0){
            $newId = 1;

            $gioco=$doc->createElement("Gioco");
            $gioco->setAttribute("id_gioco", $idGioco);
            $commento = $doc->createElement("Commento");

            $testo = $doc->createElement("text", htmlspecialchars($_POST["commentoUtente"]));

            $commento->setAttribute("id_commento", $newId);
            $commento->setAttribute("id_utente", $_SESSION["userId"]);
            $commento->setAttribute("data", date("d/m/Y"));
            $commento->setAttribute("ore", date("H"));
            $commento->setAttribute("minuti", date("i"));
            $commento->setAttribute("like", 0);  
            $commento->setAttribute("dislike", 0);

            $commento->appendChild($testo);
            $gioco->appendChild($commento);
            $root->appendChild($gioco);
        }
        else{
            
            if($gioco->hasChildNodes()){
                
                $lastCommento = $gioco->firstChild;
                $newId = (intval($lastCommento->getAttribute("id_commento")));

                $newId += 1;
                $commento = $doc->createElement("Commento");

                $testo = $doc->createElement("text", htmlspecialchars($_POST["commentoUtente"]));

                $commento->setAttribute("id_commento", $newId);
                $commento->setAttribute("id_utente", $_SESSION["userId"]);
                $commento->setAttribute("data", date("d/m/Y"));
                $commento->setAttribute("ore", date("H"));
                $commento->setAttribute("minuti", date("i"));
                $commento->setAttribute("like", 0);  
                $commento->setAttribute("dislike", 0);
                $commento->appendChild($testo);
                $gioco->insertBefore($commento, $lastCommento);
           }
            else { 
                $newId = 1;

                $commento = $doc->createElement("Commento");

                $testo = $doc->createElement("text", htmlspecialchars($_POST["commentoUtente"]));

                $commento->setAttribute("id_commento", $newId);
                $commento->setAttribute("id_utente", $_SESSION["userId"]);
                $commento->setAttribute("data", date("d/m/Y"));
                $commento->setAttribute("ore", date("H"));
                $commento->setAttribute("minuti", date("i"));
                $commento->setAttribute("like", 0);  
                $commento->setAttribute("dislike", 0);

                $commento->appendChild($testo);
                $gioco->appendChild($commento);

            }
        }
        
        $doc->save("XML/Commenti.xml");
        header("Location: Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco");
    }

    if(isset($_POST["invioRecensione"])){

        $xmlString="";
                                
        foreach(file("XML/Recensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
            foreach($elem as $i){
                if($i->getAttribute("id_gioco")==$idGioco){
                    $gioco = $i;
                    break;
                }
            }
        if($gioco == 0){
            $newId = 1;

            $gioco=$doc->createElement("Gioco");
            $gioco->setAttribute("id_gioco", $idGioco);
            $recensione = $doc->createElement("Recensione");

            $testo = $doc->createElement("text", htmlspecialchars($_POST["recensioneUtente"]));

            $recensione->setAttribute("id_recensione", $newId);
            $recensione->setAttribute("id_utente", $_SESSION["userId"]);
            $recensione->setAttribute("data", date("d/m/Y"));
            $recensione->setAttribute("ore", date("H"));
            $recensione->setAttribute("minuti", date("i"));
            $recensione->setAttribute("like", 0);  
            $recensione->setAttribute("dislike", 0);
            $recensione->setAttribute("voto", $_POST["votoUtente"]);

            $recensione->appendChild($testo);
            $gioco->appendChild($recensione);
            $root->appendChild($gioco);
        }
        else{
           if($gioco->hasChildNodes()){
                $lastRecensione = $gioco->firstChild;
                $newId = (intval($lastRecensione->getAttribute("id_recensione")));

                $newId += 1;
                $recensione = $doc->createElement("Recensione");

                $testo = $doc->createElement("text", htmlspecialchars($_POST["recensioneUtente"]));

                $recensione->setAttribute("id_recensione", $newId);
                $recensione->setAttribute("id_utente", $_SESSION["userId"]);
                $recensione->setAttribute("data", date("d/m/Y"));
                $recensione->setAttribute("ore", date("H"));
                $recensione->setAttribute("minuti", date("i"));
                $recensione->setAttribute("like", 0);  
                $recensione->setAttribute("dislike", 0);
                $recensione->setAttribute("dislike", 0);
                $recensione->setAttribute("voto", $_POST["votoUtente"]);

                $recensione->appendChild($testo);
                $gioco->insertBefore($recensione, $lastRecensione);
            }
            else{ 
                $newId = 1;

                $recensione = $doc->createElement("Recensione");

                $testo = $doc->createElement("text", htmlspecialchars($_POST["recensioneUtente"]));

                $recensione->setAttribute("id_recensione", $newId);
                $recensione->setAttribute("id_utente", $_SESSION["userId"]);
                $recensione->setAttribute("data", date("d/m/Y"));
                $recensione->setAttribute("ore", date("H"));
                $recensione->setAttribute("minuti", date("i"));
                $recensione->setAttribute("like", 0);  
                $recensione->setAttribute("dislike", 0);
                $recensione->setAttribute("voto", $_POST["votoUtente"]);

                $recensione->appendChild($testo);
                $gioco->appendChild($recensione);
            }
        }

        
        $doc->save("XML/Recensioni.xml");
        header("Location: Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco");
        
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <?php echo " 
        <title> Game Page -".$_GET['titoloGioco']."</title> " ;
      
        
        ?>
        <link rel="stylesheet" type="text/css" href="Stile/Gamepage.css?v=3" /> 
        <script src="Script/likeAndDislikeGestione.js?v=3" defer="true"></script>
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

            <div id="presentazioneGioco">
                
                <div id="immagineGioco">
                    <?php  
                        $xmlString="";

                        foreach(file("XML/Giochi.xml") as $node){ 
                            $xmlString .= trim($node);
                        }
                            
                        $doc= new DOMDocument();
                        $doc->loadXML($xmlString);
                        $root=$doc->documentElement;
                        $elem=$root->childNodes;

                        foreach($elem as $i){
                            if($i->getAttribute("id_gioco")==$idGioco) $imagePath=$i->getElementsByTagName("Immagine")->item(0)->textContent;
                        }

                        echo "<img src=\"$imagePath\" alt=\"GameImage\" title=\"$titoloGioco\"></img>"

                    ?>
                    
                </div>
                <div id="statGioco">
                    <div>
                        <?php 
                        $xmlString="";

                        foreach(file("XML/Giochi.xml") as $node){ 
                            $xmlString .= trim($node);
                        }
                            
                        $doc= new DOMDocument();
                        $doc->loadXML($xmlString);
                        $root=$doc->documentElement;
                        $elem=$root->childNodes;

                        foreach($elem as $i){
                            if($i->getAttribute("id_gioco")==$idGioco){
                               $PrezzoGioco = $i->getElementsByTagName('Prezzo')->item(0)->textContent;
                               $DataUscitaGioco = $i->getElementsByTagName('DataDiUscita')->item(0)->textContent;
                               $GenereGioco = $i->getElementsByTagName('Generi')->item(0)->textContent;
                               $PublisherGioco = $i->getElementsByTagName('Publisher')->item(0)->textContent;
                               $CasaSviluppoGioco = $i->getElementsByTagName('CasaSviluppo')->item(0)->textContent;
                               $DescrizioneGioco = $i->getElementsByTagName('Descrizione')->item(0)->textContent;
                            }
                        }
                        echo "<table>
                                <tr>
                                    <td> ID Gioco</td>
                                    <td>$idGioco</td>
                                </tr>
                                <tr>
                                    <td>Titolo</td>
                                    <td>$titoloGioco</td>
                                </tr>
                                <tr>
                                    <td>Prezzo</td>
                                    <td>$PrezzoGioco €</td>
                                </tr>
                                <tr>
                                    <td>Generi</td>
                                    <td>$GenereGioco </td>
                                </tr>
                                <tr>
                                    <td>Data di Uscita</td>
                                    <td>$DataUscitaGioco</td>
                                </tr>
                                <tr>
                                    <td>Publisher</td>
                                    <td>$PublisherGioco</td>
                                </tr>
                                <tr>
                                    <td>Sviluppatore</td>
                                    <td>$CasaSviluppoGioco</td>
                                </tr>
                              </table>";
                     ?>
                    </div>
                    <div id="consigliati">

                            <h3>Potrebbero piacerti anche:</h3>

                        <?php
                        $xmlString = "";
                        foreach (file("XML/Giochi.xml") as $node) {
                            $xmlString .= trim($node);
                        }

                        $doc = new DOMDocument();
                        $doc->loadXML($xmlString);
                        $root = $doc->documentElement;
                        $giochi = $root->getElementsByTagName("Gioco");
                        

                        $idCorrelati = [];

                        
                        foreach ($giochi as $gioco) {
                            if ($gioco->getAttribute("id_gioco") ==$idGioco) {
                                $lista = $gioco->getElementsByTagName("idGiocoCorrelato");
                                if ($lista) {
                                    foreach ($lista as $id) {
                                        $idCorrelati[] = $id->textContent;
                                    }
                                }
                                break;
                            }
                        }

                        




                        echo "<ul>";
                        $len = count($idCorrelati);
                        
                            
                        for ($k=0; $k<$len; $k++) {
                            $xmlString = "";
                            foreach (file("XML/Giochi.xml") as $node) {
                                $xmlString .= trim($node);
                            }

                            $doc = new DOMDocument();
                            $doc->loadXML($xmlString);
                            $root = $doc->documentElement;
                            $giochi = $root->getElementsByTagName("Gioco");

                            foreach ($giochi as $gioco) {
                                
                                if ($gioco->getAttribute("id_gioco") == $idCorrelati[$k]) {

                                    $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
                                    echo "<li><a href='Gamepage.php?titoloGioco=$titolo&idGioco=" . $idCorrelati[$k] . "'>" . $titolo . "</a></li>";                          
                                      }
                            }
                        }

                        echo "</ul>";
                        ?>
                    </div>
                    

                </div>

                <div id="specGioco">
                    <?php 

                    $xmlString="";

                    foreach(file("XML/Giochi.xml") as $node){ 
                        $xmlString .= trim($node);
                    }
                        
                    $doc= new DOMDocument();
                    $doc->loadXML($xmlString);
                    $root=$doc->documentElement;
                    $elem=$root->childNodes;
                    foreach($elem as $i){
                            if($i->getAttribute("id_gioco")==$idGioco){
                                $interoSpecMin = $i->getElementsByTagName('RequisitiMinimi')->item(0)->textContent;
                                $interoSpecRac = $i->getElementsByTagName('RequisitiRaccomandati')->item(0)->textContent;
                            }
                        }

                    $partiSpecMin = array_map('trim', explode(';', $interoSpecMin));
                    $partiSpecRac = array_map('trim', explode(';', $interoSpecRac));

                    echo " 
                    
                    <table>
                    <tr>
                        <th colspan=\"2\">Specifiche Tecniche Minime</th>
                    </tr>
                    <tr>
                        <td>$partiSpecMin[0]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecMin[1]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecMin[2]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecMin[3]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecMin[4]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecMin[5]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecMin[6]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecMin[7]</td>
                    </tr>


                    </table>

                    <table>
                    <tr>
                        <th colspan=\"2\">Specifiche Tecniche Raccomandate</th>
                    </tr>
                    <tr>

                        <td>$partiSpecRac[0]</td>
                    </tr>
                    <tr>

                        <td>$partiSpecRac[1]</td>
                    </tr>
                    <tr>

                        <td>$partiSpecRac[2]</td>
                    </tr>
                    <tr>

                        <td>$partiSpecRac[3]</td>
                    </tr>
                    <tr>
                    
                        <td>$partiSpecRac[4]</td>
                    </tr>
                    <tr>
                        
                        <td>$partiSpecRac[5]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecRac[6]</td>
                    </tr>
                    <tr>
                        <td>$partiSpecRac[7]</td>
                    </tr>
                    </table>
                    
                    "
                    ?>
            
                </div>

            </div>
            
            <div id="descAndBuy">
              <div id="descGioco">
                    <?php 
                        echo "<h3>Descrizione:</h3> <p>$DescrizioneGioco</p>";
                    ?>
              </div>
              <?php
                
                    $xmlString = "";
                        foreach (file("XML/utenti.xml") as $node) {
                            $xmlString .= trim($node);
                        }

                        $doc = new DOMDocument();
                        $doc->loadXML($xmlString);

                        $utenti = $doc->getElementsByTagName("Utente");

                        foreach ($utenti as $utente) {

                            $idUtente = $utente->getAttribute("id_user");

                            $giochi = $utente->getElementsByTagName("listaGiochi")[0]->getElementsByTagName("idGiocoPosseduto");

                            $possiedeGioco = false;

                            foreach ($giochi as $gioco) {
                                $idGiocoP = $gioco->textContent;

                                if ($idGiocoP == $idGioco) {
                                    $possiedeGioco = true;
                                    break;
                                }
                            }

                            if (!$possiedeGioco) {
                                echo '
                                <div id="Acquisto">
                                    <input type="button" id="buttonAcquista" value="Acquista">
                                </div>';
                            }
                        }
                   

              ?>
               
            </div>
            

            <div id="social">
                
            
                <div id="commenti">
                    <h3>Commenti degli utenti:</h3> 
                    <?php 

                    if($service == 0) echo "<p>Devi essere loggato per poter lasciare un commento .</p>";
                    else if($_SESSION['Grado']<=1) echo "<p>Devi essere di grado 2 o superiore per lasciare un commento.</p>";
                    else{

                            echo " <form action=\"Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco\" method=\"post\" id=\"formCommenti\">
                                    <textarea name=\"commentoUtente\" rows=\"4\" cols=\"65\" placeholder=\"Scrivi il tuo commento qui...\"></textarea>
                                    <br/>
                                    <input type=\"submit\" name=\"invioCommento\" value=\"Invia\"/> 
                                </form>"; 
                                            
                        }
         
    
                    
                        echo "<h4>Commenti:</h4>";

                    $xmlString="";

                            foreach(file("XML/Commenti.xml") as $node){ 
                                $xmlString .= trim($node);
                            }
                        
                            $doc= new DOMDocument();
                            $doc->loadXML($xmlString);
                            $root=$doc->documentElement;
                            $elem=$root->childNodes;
                            foreach($elem as $i){
                                    if($i->getAttribute("id_gioco")==$idGioco){
                                        $commentoId = $i->getElementsByTagName("Commento");
                                        foreach($commentoId as $c){

                                            $idUtenteCommento = $c->getAttribute('id_utente');
                                            $idCommento = $c->getAttribute('id_commento');
                                            $commentoTesto = $c->getElementsByTagName('text')->item(0)->textContent;
                                            $dataCommento = $c->getAttribute('data');
                                            $oraCommento = $c->getAttribute('ore');
                                            $minutoCommento = $c->getAttribute('minuti');
                                            $likeCommento = $c->getAttribute('like'); 
                                            $dislikeCommento = $c->getAttribute('dislike');

                                            $db_name = "Database_Pixel_Hub";
                                            $table_users = "Tabella_Utenti";
                                            $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

                                            if (mysqli_connect_errno()){

                                                printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                                            }
                                            

                                            $queryLogin = "SELECT * FROM $table_users WHERE ID = '$idUtenteCommento'";
                                            $resultQ = mysqli_query($mysqliConnection, $queryLogin);
                                            $num = mysqli_num_rows($resultQ);

                                            if($num == 1){

                                                $row=mysqli_fetch_array($resultQ);
                                                $nomeUtenteCommento = $row['Username'];
                                                echo "<div class=\"commentoUtente\">
                                                        <div><h4>$nomeUtenteCommento</h4></div>
                                                        <div><p>$commentoTesto</p></div>
                                                        <div class=\"likeAndDateContainer\">
                                                            <div class=\"dataCommento\"> <p>Data: $dataCommento - $oraCommento : $minutoCommento </p> </div>
                                                            <div class=\"likeDislike\">
                                                                <div class=\"like\">
                                                                        <p id=\"likeButtonTextCom$idCommento\"> $likeCommento  </p> 
                                                                        <button type=\"button\" id=\"likeButtonCom$idCommento\" ";

                                                if($service != 0){
                                                    if($_SESSION['Grado']>1) echo "onclick=\"LikeGestioneCommenti(".$_SESSION['userId'].", $idCommento, $idGioco, 'like')\"";
                                                    else echo "onclick=\"userAlert(".$_SESSION['Grado'].")\"";
                                                }
                                                else echo "onclick=\"userAlert(0)\"";
                                                echo ">&#128077;
                                                    </button>
                                                    </div>
                                                        <div class=\"dislike\"> <p id=\"dislikeButtonTextCom$idCommento\">  $dislikeCommento   </p> 
                                                            <button id=\"dislikeButtonCom$idCommento\"type=\"button\" ";

                                                        if($service != 0){ 
                                                    
                                                        if( $_SESSION['Grado']>1) echo "onclick=\"LikeGestioneCommenti(".$_SESSION['userId'].", $idCommento, $idGioco, 'dislike')\"";
                                                        else echo "onclick=\"userAlert(".$_SESSION['Grado'].")\"";
                                                         }
                                                    
                                                        else echo "onclick=\"userAlert(0)\"";

                                                        echo">&#128078;</button>
                                                          </div>                                                                                
                                                        </div>
                                                     </div>
                                                    </div>";          
                                                        }

                                        }
                                        
                                    }
                                }
                                       

                    ?>
               </div>
            
               <div id="recensioni">
                     <h3>Recensioni degli utenti:</h3> 

                     <?php 

                    if($service == 0)echo "<p>Devi essere loggato per poter lasciare una recensione .</p>";
                    else if($_SESSION['Grado']<=2) echo "<p>Devi essere di grado 3 per lasciare una recensione.</p>";
                    else{

                            echo " <form action=\"Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco\" method=\"post\" id=\"formRecensioni\">
                                    <textarea name=\"recensioneUtente\" rows=\"4\" cols=\"65\" placeholder=\"Scrivi la tua recensione qui...\"></textarea><br/> 
                                    <input type=\"number\" cols=\"10\"  min=\"0\" max=\"100\" name=\"votoUtente\" placeholder=\" Voto da 0 a 100...\">
                                    
                                    <br/>
                                    <input type=\"submit\" name=\"invioRecensione\" value=\"Invia\"/> 
                                </form>"; 
                                            
                        }
                      echo "<h4>Recensioni:</h4 >";

                        $xmlString="";

                            foreach(file("XML/Recensioni.xml") as $node){ 
                                $xmlString .= trim($node);
                            }
                        
                            $doc= new DOMDocument();
                            $doc->loadXML($xmlString);
                            $root=$doc->documentElement;
                            $elem=$root->childNodes;
                            foreach($elem as $i){
                                    if($i->getAttribute("id_gioco")==$idGioco){
                                        $recensioneId = $i->getElementsByTagName("Recensione");
                                        foreach($recensioneId as $r){
                                            $idRecensione = $r->getAttribute('id_recensione');
                                            $idUtenteRecensione = $r->getAttribute('id_utente');
                                            $recensioneTesto = $r->getElementsByTagName('text')->item(0)->textContent;
                                            $dataRecensione = $r->getAttribute('data');
                                            $oraRecensione = $r->getAttribute('ore');
                                            $minutoRecensione = $r->getAttribute('minuti');
                                            $likeRecensione = $r->getAttribute('like'); 
                                            $dislikeRecensione = $r->getAttribute('dislike');
                                            $db_name = "Database_Pixel_Hub";
                                            $table_users = "Tabella_Utenti";
                                            $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

                                            if (mysqli_connect_errno()){

                                                printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                                            }
                                            

                                            $queryLogin = "SELECT * FROM $table_users WHERE ID = '$idUtenteRecensione'";
                                            $resultQ = mysqli_query($mysqliConnection, $queryLogin);
                                            $num = mysqli_num_rows($resultQ);

                                            if($num == 1){

                                                $row=mysqli_fetch_array($resultQ);
                                                $nomeUtenteRecensione = $row['Username'];
                                                echo "<div class=\"recensioneUtente\">
                                                        <div><h4>$nomeUtenteRecensione</h4></div>
                                                        <div><p>$recensioneTesto</p></div>
                                                        <div class=\"votoRecensione\"><p> Voto: ".$r->getAttribute('voto')."/100 </p></div>
                                                        <div class=\"likeAndDateContainer\">
                                                            <div class=\"dataRecensione\"> <p>Data: $dataRecensione - $oraRecensione : $minutoRecensione </p> </div>
                                                            <div class=\"likeDislike\">
                                                                <div class=\"like\">
                                                                    <p id=\"likeButtonTextRec$idRecensione\"> $likeRecensione  </p> 
                                                                    <button type=\"button\" id=\"likeButtonRec$idRecensione\" ";

                                                if($service != 0){
                                                if($_SESSION['Grado']>2) echo "onclick=\"LikeGestioneRecensioni(".$_SESSION['userId'].", $idRecensione, $idGioco, 'like')\"";
                                                else echo "onclick=\"userAlert(".$_SESSION['Grado'].")\""; 
                                                }
                                                else echo "onclick=\"userAlert(0)\"";
                                                echo ">&#128077;
                                                        </button>
                                                        </div>
                                                        <div class=\"dislike\"> <p id=\"dislikeButtonTextRec$idRecensione\">  $dislikeRecensione   </p> 
                                                        <button id=\"dislikeButtonRec$idRecensione\"type=\"button\" ";
                                                if($service != 0){
                                                        if($_SESSION['Grado']>2) echo "onclick=\"LikeGestioneRecensioni(".$_SESSION['userId'].", $idRecensione, $idGioco, 'dislike')\"";
                                                        else echo "onclick=\"userAlert(".$_SESSION['Grado'].")\"";
                                                    }
                                                    else echo "onclick=\"userAlert(0)\"";
                                                    echo ">&#128078;</button></div>                                                                             
                                                                </div>
                                                             </div>
                                                         </div>";          
                                            }

                                        }
                                        
                                    }
                                }
         
    
                    
            
                    ?>
                </div>
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