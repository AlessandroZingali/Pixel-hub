<?php 
require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

require_once 'gestioneEsperienzauser.php';
require_once 'baseScontiUtente.php';

$service = 0; //0 = guest, 1 = logged in
$utente = "";//nome utente loggato


session_start();
if(isset($_SESSION['userId'])){
    
    $utente = $_SESSION['userName'];
    $service = 1;
    $commenti=xmlPointer("XML/Commenti.xml");
    $sommatoria = calcoloModCommenti($commenti);
    $mod = $_SESSION['modCommenti']/100;
    $scontoManager = new scontiUtente($_SESSION['userId']);

}


?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Home</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <!--Set dei vari file css -->
        <link rel="stylesheet" type="text/css" href="Stile/Home.css?v=3" /> 
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" /> 
        <script>
            <?php
            //Passo a javascript l'id dell'utente loggato e il suo genere preferito, 
            //per mostrare oppure no la tebella personalizzata per l'utente in base al suo genere preferito.
            //Si può vedere come una fleg molto elaborata per far comunicare php e javascript, in modo da contare nella gestione
            //degli slider una tabella in più se l'utente è loggato.  
            if($service == 1 && isset($_SESSION['generePreferito'])){
                echo "sessionStorage.setItem(\"idUser\", \"".$_SESSION['userId']."\");";
                echo "sessionStorage.setItem(\"genPref\", \"".$_SESSION['generePreferito']."\");";
            }
            ?>
            
        </script>
        <!--Set dei vari file js, per la gestione del comportamento dei vari Slider e per la barra di ricerca dei giochi -->
        <script type="text/javascript" src="Script/GameTableGestione.js?v=3">  </script>
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <script><?php echo "console.log(\"mod Commenti: ".$mod."\");"; ?></script>

    </head>
    <body>    
        <div id="container"><!--contenitore principale, interno al body -->

            <div id="header"><!--header della pagina -->
                <div id="logo"><img src='Loghi/logo pixelhub slim.png' alt="Logo di Pixel Hub" id="logoimg"/></div>
                <h2>Il tuo shop preferito di videogiochi</h2>

            </div>

            <div id="navigation">
                <div class="dropMenu">
                    <button class="botMenu"><img src="Stile/Icone/iconamenu.png" alt=""></button>
                    <ul>
                        <li><a href="Homepage.php">Home</a></li>
                        <li><a href="catalogo.php">Catalogo </a></li>
                        <li><a href="carrello.php">Carrello </a></li>
                        <?php


                            
                            if($service == 0){
                                if(isset($_SESSION['userId']) && isset($_SESSION['generePreferito'])){
                                    echo "<script>";
                                    echo "sessionStorage.removeItem(\"idUser\");";
                                    echo "sessionStorage.removeItem(\"genPref\");";
                                    echo "</script>"; 
                                }
                                echo "<li><a href=\"login.php\">Log in </a></li>";
                            }
                            else if($service == 1){
                                
                                echo "<li><a href=\"login.php\">Log out </a></li>";
                                echo "<li>
                                <a href=\"Profilo.php\">Profilo </a>
                                </li> 
                                <p id=\"saldo\"> Pixels: ".$_SESSION['Pixels']." </br> Saldo attuale: ".$_SESSION['Saldo']." € </p>";
                            
                            
                            if($_SESSION['tipoUtente'] == '1'){
                                echo "<li><a href=\"GestioneAdmin.php\">Gestione</a></li>";
                            }
                            
                                if($_SESSION['tipoUtente'] == "2"){
                                echo "<li><a href=\"gestionePublisher.php\">Gestione</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
                    <form id="searchBar" onsubmit="return false;">
                        <input id="searchBarInput" type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>

            
            
            <div id="TablesBoard"> <!--tag della della tabella maestra, contenente tutti i game slider. Abbiamo scelto di usare le tabelle come sostegno-->

                <?php 
                //Se l'utente è loggato e ha un genere preferito, mostro la tabella personalizzata
               if (isset($_SESSION['userId']) && isset($_SESSION['generePreferito']) && $_SESSION['generePreferito'] != "Nessuno"){

                    //Il gameSlider sarà strutturato in questo modo: Un input button per scorrere indietro, una tabella con i giochi che sarà scorrevole, un input button per scorrere avanti.
                    echo "<div class=\"GameSlider\">
                    <div>
                        <input type=\"button\" value=\"<\" id=\"scorrindietro0\" onclick=\"sliderTable('GameTable0', 'back')\" />
                    </div> <!-- < -->
                    <div>
                    <p class=\"titleTable\">Hey $utente guarda questi ".$_SESSION['generePreferito'].":</p>
                        <table id=\"GameTable0\">
                    
                    
                           ";
                                $elem = xmlPointer("XML/Giochi.xml");
                                //Inserisco un limite di 20 giochi da mostrare nella tabella personalizzata
                                $limite = setLimiteSlider($elem);
                                 echo "<tr>";
                                 //Stampiamo tutti i giochi che corrispondono al genere preferito dell'utente, ricavandono le informazioni dal file XML. 
                                 //Da notare come lo slider è un "camuflage" di una lunghissima riga di celle di tabella, che vengono fatte scorrere orizzontalmente 
                                 //tramite javascript, in modo da mostrare solo alcune celle alla volta.
                                for($j=0; $j < $limite ; $j++){
                                    $gioco=$elem->item($j);
                                    $genereGioco=$gioco->getElementsByTagName("Generi")->item(0)->textContent;

                                   if($genereGioco == $_SESSION['generePreferito']){
                                        $idGioco = $gioco->getAttribute("id_gioco");
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        $prezzoGioco=$gioco->getElementsbyTagName("Prezzo")->item(0)->textContent;
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;

                                        
                                        //Le game card sono le singole celle della tabella che contengono l'immagine del gioco, il prezzo e il link alla pagina del gioco.
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                             <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco'\" > ";
                                             if($service){
                                               
                                        $elem2=xmlPointer("XML/utenti.xml");
                                        foreach ($elem2 as $utente) {

                                            $idUtente = $utente->getAttribute("id_user");
                                            //Verifico se l'utente loggato possiede già il gioco, in modo da mostrare il prezzo o la dicitura "Acquistato!"
                                            if ($idUtente == $_SESSION['userId']) {
                                                $giochi = $utente->getElementsByTagName("listaGiochi")[0]->getElementsByTagName("idGiocoPosseduto");
                                                $possiedeGioco = false;

                                                foreach ($giochi as $g) {
                                                    
                                                    $idGiocoP = $g->textContent;
                                                    
                                                    if ($idGiocoP == $idGioco) {
                                                        $possiedeGioco = true;
                                                        break;
                                                    }
                                                }
                                                if (!$possiedeGioco) {
                                                    $sconti = $scontoManager->percentualeScontoGioco($idGioco);
                                                    
                                                   if(count($sconti) > 0){
                                                        $sommaSconti=array_sum($sconti);
                                                        $prezzoGiocoScontato = $prezzoGioco - ($prezzoGioco * ($sommaSconti/100));
                                                    
                                                        echo "<div class=\"prezzo\"><p>  <s>$prezzoGioco €</s></p></div> ";
                                                        echo "<div class=\"prezzoSconto\"><p>".round($prezzoGiocoScontato, 2)." € </p></div>";

                                                    }
                                                    else{
                                                        echo "<div class=\"prezzoNoSconto\"><p> $prezzoGioco €  </p></div> ";

                                                    }

                                                    
                                                }
                                                else echo "<div class=\"acquistato\"><p>  Acquistato!  </p></div> ";
                                                
                                            }
                                        }

                                             }
                                               
                                            echo"
                                            </div> 
                                            </td>";
                                                
                                        }
                                            
                                    }   
                                    echo "</tr>";    
                                    
                            echo " </table>
                       
                    </div><!--tab -->
                    <div>
                        <input type=\"button\" value=\">\" id=\"scorriavanti0\" onclick=\"sliderTable('GameTable0', 'forward')\" />
                    </div><!-- > -->
                </div>";
                }
                
                ?>
                <!-- Slider dei giochi più popolari, ogni slider avrà un caratteristica -->
                <div class="GameSlider">
                    
                    <div>
                        
                        <input type="button" value="<" id="scorrindietro1" onclick="sliderTable('GameTable1', 'back')" />
                        
                    </div>
                    <div>
                    <p class="titleTable">I piu' Popolari</p>
                        <table id="GameTable1">
                        
                            
                            
                            <?php
                                $elem=xmlPointer("XML/Giochi.xml");
                                
                                $limite = setLimiteSlider($elem);
                                
                                echo "<tr>";
                                    
                                for($j=0; $j < $limite ; $j++){
                                    
                                    $gioco=$elem->item($j);
                                    $idGioco = $gioco->getAttribute("id_gioco");
                                    $valGiocoGrezzo=$gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent;
                                    $valGioco = (float) $valGiocoGrezzo;
                                    //Qui abbiamo impostato un filtro in modo che mostri solo giochi dove la media delle recensioni  
                                    //è sopra un certo valore; in questo caso maggiore o uguale a 80 (ricoridamo che il punteggio va da 0 a 100)
                                    if($valGioco>=80){
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        $prezzoGioco=$gioco->getElementsbyTagName("Prezzo")->item(0)->textContent;
                                        
                        
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                                <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco'\" >";
                                        if($service){                    

                                        $elem2=xmlPointer("XML/utenti.xml");

                                            foreach ($elem2 as $utente) {

                                                $idUtente = $utente->getAttribute("id_user");

                                                if ($idUtente == $_SESSION['userId']) {
                                                    $giochi = $utente->getElementsByTagName("listaGiochi")[0]->getElementsByTagName("idGiocoPosseduto");
                                                    $possiedeGioco = false;

                                                    foreach ($giochi as $g) {
                                                        
                                                        $idGiocoP = $g->textContent;
                                                        
                                                        if ($idGiocoP == $idGioco) {
                                                            $possiedeGioco = true;
                                                            break;
                                                        }
                                                    }
                                                    if (!$possiedeGioco) {
                                                    $sconti = $scontoManager->percentualeScontoGioco($idGioco);
                                                    if(count($sconti) > 0){
                                                        $sommaSconti=array_sum($sconti);
                                                        $prezzoGiocoScontato = $prezzoGioco - ($prezzoGioco * ($sommaSconti/100));
                                                        
                                                        
                                                        echo "<div class=\"prezzo\"><p>  <s>$prezzoGioco €</s> </p></div> ";
                                                        echo "<div class=\"prezzoSconto\"><p>".round($prezzoGiocoScontato, 2)." € </p></div>";
                                                        

                                                    }
                                                    else{
                                                        echo "<div class=\"prezzoNoSconto\"><p> $prezzoGioco €  </p></div> ";

                                                    }

                                                    
                                                }
                                                else echo "<div class=\"acquistato\"><p>  Acquistato!  </p></div> ";
                                                    
                                                }
                                            }
                                        }
                                        echo"  </div> 
                                            </td>";
                                    }  
                                } 
                                echo "</tr>";  
                            ?>    
                        
                        </table>

                    </div>

                    <div>
                        
                        <input type="button" value=">" id="scorriavanti1" onclick="sliderTable('GameTable1', 'forward')" />
                    
                    </div>
                </div>

                 <div class="GameSlider">
                    <div>
                        <input type="button" value="<" id="scorrindietro2" onclick="sliderTable('GameTable2', 'back')" />
                    </div> <!-- Questo gameslider va a mostrare gli ulimi giochi aggiunti al sito, seguendo l'ordine di grandezza degli id
                     (il primo gioco aggiunto in assoluto ha un id uguale a 1 -->
                    <div>
                         <p class="titleTable">Ultimi Giochi aggiunti</p>
                        <table id="GameTable2">
                        
                           
                            <?php
                                $xmlString="";
                                
                                foreach(file("XML/Giochi.xml") as $node){ 
                                    $xmlString .= trim($node);
                                }
                                
                                $doc= new DOMDocument();
                                $doc->loadXML($xmlString);
                                $root=$doc->documentElement;
                                $elem=$root->childNodes;
                                
                                if($elem->length<20){
                                    $limite=$elem->length;
                                } else {
                                    $limite=20;
                                }
                                
                                echo "<tr>";
                                for($j=$elem->length-1; $j >=0 && $j > ($elem->length-1) - ($limite-1); $j--){
                                    $gioco=$elem->item($j);

                                        $idGioco = $gioco->getAttribute("id_gioco");
                        
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        $prezzoGioco=$gioco->getElementsbyTagName("Prezzo")->item(0)->textContent;
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                             <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco'\" >";
                                            if($service){                    
    
                                        $xmlString = "";
                                        foreach (file("XML/utenti.xml") as $node) {
                                            $xmlString .= trim($node);
                                        }

                                        $doc2 = new DOMDocument();
                                        $doc2->loadXML($xmlString);
                                        $root2=$doc2->documentElement;
                                        $elem2=$root2->childNodes;

                                            

                                        foreach ($elem2 as $utente) {

                                            $idUtente = $utente->getAttribute("id_user");

                                            if ($idUtente == $_SESSION['userId']) {
                                                $giochi = $utente->getElementsByTagName("listaGiochi")[0]->getElementsByTagName("idGiocoPosseduto");
                                                $possiedeGioco = false;

                                                foreach ($giochi as $g) {
                                                    
                                                    $idGiocoP = $g->textContent;
                                                    
                                                    if ($idGiocoP == $idGioco) {
                                                        $possiedeGioco = true;
                                                        break;
                                                    }
                                                }
                                                if (!$possiedeGioco) {
                                                    $sconti = $scontoManager->percentualeScontoGioco($idGioco);
                                                    if(count($sconti) > 0){
                                                        $sommaSconti=array_sum($sconti);
                                                        $prezzoGiocoScontato = $prezzoGioco - ($prezzoGioco * ($sommaSconti/100));
                                                   
                                                        echo "<div class=\"prezzo\"><p>  <s>$prezzoGioco €</s> </p></div> ";
                                                             echo "<div class=\"prezzoSconto\"><p>".round($prezzoGiocoScontato, 2)." € </p></div>";

                                                    }
                                                    else{
                                                        echo "<div class=\"prezzoNoSconto\"><p> $prezzoGioco €  </p></div> ";

                                                    }

                                                    
                                                }
                                                else echo "<div class=\"acquistato\"><p>  Acquistato!  </p></div> ";
                                                
                                            }
                                        }
                                        }
                                    echo"  </div> 
                                            </td>";
                                    }   
                                    echo "</tr>";    
                                    
                            ?> 
                            

                        </table>
                    </div>
                    <div>
                        <input type="button" value=">" id="scorriavanti2" onclick="sliderTable('GameTable2', 'forward')" />
                    </div>
                </div>
                
                <div class="GameSlider">
                    <div>
                        <input type="button" value="<" id="scorrindietro3" onclick="sliderTable('GameTable3', 'back')" />
                    </div> <!-- l'ultimo gameslider presenta una raccolta di giochi che hanno in comune un genere
                    in questo caso gli Sparatutto -->
                    <div>
                        <p class="titleTable">Sparatutto</p>
                        <table id="GameTable3">
                    
                    
                        
                            
                            <?php
                                $xmlString="";
                                
                                foreach(file("XML/Giochi.xml") as $node){ 
                                    $xmlString .= trim($node);
                                }
                                
                                $doc= new DOMDocument();
                                $doc->loadXML($xmlString);
                                $root=$doc->documentElement;
                                $elem=$root->childNodes;
                                
                                if($elem->length<15){
                                    $limite=$elem->length;
                                } else {
                                    $limite=15;
                                }
                                
                                echo "<tr>";
                                for($j=0; $j < $limite ; $j++){
                                    $gioco=$elem->item($j);
                                    $genereGioco=$gioco->getElementsByTagName("Generi")->item(0)->textContent;

                                   if($genereGioco=="Sparatutto"){
                                        $idGioco = $gioco->getAttribute("id_gioco");
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        $prezzoGioco=$gioco->getElementsbyTagName("Prezzo")->item(0)->textContent;
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                              <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco'\" >";
                                              if($service){$xmlString = "";
                                        foreach (file("XML/utenti.xml") as $node) {
                                            $xmlString .= trim($node);
                                        }

                                        $doc2 = new DOMDocument();
                                        $doc2->loadXML($xmlString);
                                        $root2=$doc2->documentElement;
                                        $elem2=$root2->childNodes;

                                            

                                        foreach ($elem2 as $utente) {

                                            $idUtente = $utente->getAttribute("id_user");

                                            if ($idUtente == $_SESSION['userId']) {
                                                $giochi = $utente->getElementsByTagName("listaGiochi")[0]->getElementsByTagName("idGiocoPosseduto");
                                                $possiedeGioco = false;

                                                foreach ($giochi as $g) {
                                                    
                                                    $idGiocoP = $g->textContent;
                                                    
                                                    if ($idGiocoP == $idGioco) {
                                                        $possiedeGioco = true;
                                                        break;
                                                    }
                                                }
                                                if (!$possiedeGioco) {
                                                    $sconti = $scontoManager->percentualeScontoGioco($idGioco);
                                                    if(count($sconti) > 0){
                                                        $sommaSconti=array_sum($sconti);
                                                        $prezzoGiocoScontato = $prezzoGioco - ($prezzoGioco * ($sommaSconti/100));
                                                    
                                                        echo "<div class=\"prezzo\"><p>  <s>$prezzoGioco €</s> </p></div> ";
                                                             echo "<div class=\"prezzoSconto\"><p>".round($prezzoGiocoScontato, 2)." € </p></div>";

                                                    }
                                                    else{
                                                        echo "<div class=\"prezzoNoSconto\"><p> $prezzoGioco €  </p></div> ";

                                                    }

                                                    
                                                
                                                }
                                                else echo "<div class=\"acquistato\"><p>  Acquistato!  </p></div> ";
                                                
                                            }
                                        }

                                              }
                                              
                                               echo"
                                            </div> 
                                            </td>";
                                                
                                        }
                                            
                                    }   
                                    echo "</tr>";    
                                    
                            ?> 
                            

                        </table>
                    </div><!--tab -->
                    <div>
                        <input type="button" value=">" id="scorriavanti3" onclick="sliderTable('GameTable3', 'forward')" />
                    </div><!-- > -->
                </div>


                

                  <!--  Pulsante per il debugg se si vuole reimpostare tutti gli slider alla posizione iniziale
                <input type="button" value="Reset Lista" onclick="localStorage.clear();"/> 
                -->
            </div>
            
        </div>
        
        <div id="footer">
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
        
    </body>
</html> 