<?php 
require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso,
//e che imposta il limite dello slider a un massimo di 20 giochi.

$service = 0; //0 = guest, 1 = logged in
$utente = "";//nome utente loggato

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
    </head>
    <body>    
        <div id="container"><!--contenitore principale, interno al body -->

            <div id="header"><!--header della pagina -->
                <div id="logo"><img src='Loghi/logo pixelhub slim.png' alt="Logo di Pixel Hub" id="logoimg"/></div>
                <h2>Il tuo shop preferito di videogiochi</h2>
            </div>

            <div id="navigation">
                <!--Menu a tendina per la navigazione tra le pagine principali del sito, si mostrera in tal maniera solo al restigimento della pagina-->
                <!--e il cambio sarà gestito tramite media query-->
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
                <!--Barra di ricerca dei giochi, mostra in modo dinamico una lista dei giochi in base al nome. Abbiamo gestito il comportamento nel file Script/Searchgame.js -->
                    <form id="searchBar" onsubmit="return false;">
                        <input id="searchBarInput" type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>

            
            
            <div id="TablesBoard"> <!--tag della della tabella maestra, contenente tutti i game slider. Abbiamo scelto di usare le tabelle come sostegno-->
                <?php //Essendo che ogni Slider è uguale ma cambiano solo i filtri di selezione dei giochi, abbiamo deciso di replicare lo stesso codice per ogni slider, cambiando solo i filtri.
                //Di conseguenza il primo slider sarà quello dove mostreremop il funzionamento generale, nel resto abbiamo evidenziato sopratutto filtri e/o differenze.

                //Se l'utente è loggato e ha un genere preferito, mostro la tabella personalizzata
                if (isset($_SESSION['userId']) && isset($_SESSION['generePreferito'])){

                    //Il gameSlider sarà strutturato in questo modo: Un input button per scorrere indietro, una tabella con i giochi che sarà scorrevole, un input button per scorrere avanti.
                    //Come si può notare i pulsanti e la tabella hanno degli id univoci, in modo da poter essere gestiti singolarmente tramite javascript (ex GameTable0, scorrindietro0, scorriavanti0)
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
                                    if (!$possiedeGioco) echo "<div class=\"prezzo\"><p>  $prezzoGioco € </p></div> ";
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
                                    $valGiocoGrezzo=$gioco->getElementsByTagName("MediaRecensioniUtenti")->item(0)->textContent;
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
                                                    if (!$possiedeGioco) echo "<div class=\"prezzo\"><p>  $prezzoGioco € </p></div> ";
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
                <!-- Slider dei giochi più recenti, basato sull'ordine di inserimento nel database (id_gioco) -->
                 <div class="GameSlider">
                    <div>
                        <input type="button" value="<" id="scorrindietro2" onclick="sliderTable('GameTable2', 'back')" />
                    </div>
                    <div>
                         <p class="titleTable">Ultimi Giochi aggiunti</p>
                        <table id="GameTable2">
 
                            <?php
                                $elem=xmlPointer("XML/Giochi.xml");
                                
                                $limite = setLimiteSlider($elem);
                                
                                echo "<tr>";
                                //Stampo i giochi in ordine decrescente di id_gioco, in modo da mostrare i giochi più recenti
                                //Abbiamo usato un for decrescente per scorrere l'elenco dei giochi dal più recente al meno recente
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
                                                if (!$possiedeGioco) echo "<div class=\"prezzo\"><p>  $prezzoGioco € </p></div> ";
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

                <!-- Ultimo gameslider che presenta una raccolta di giochi che hanno in comune un genere in questo caso gli Sparatutto -->
                <div class="GameSlider">
                    <div>
                        <input type="button" value="<" id="scorrindietro3" onclick="sliderTable('GameTable3', 'back')" />
                    </div> 
                    <div>
                        <p class="titleTable">Sparatutto</p>
                        <table id="GameTable3">

                            <?php
                                $elem=xmlPointer("XML/Giochi.xml");
                                
                                $limite = setLimiteSlider($elem);
                                
                                echo "<tr>";
                                for($j=0; $j < $limite ; $j++){
                                    $gioco=$elem->item($j);
                                    $genereGioco=$gioco->getElementsByTagName("Generi")->item(0)->textContent;
                                    
                                    //Filtro per mostrare solo giochi del genere Sparatutto
                                   if($genereGioco=="Sparatutto"){
                                        $idGioco = $gioco->getAttribute("id_gioco");
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
                                                    if (!$possiedeGioco) echo "<div class=\"prezzo\"><p>  $prezzoGioco € </p></div> ";
                                                    else echo "<div class=\"acquistato\"><p>  Acquistato!  </p></div> ";
                                                    
                                                }
                                            }

                                        }
                                              
                                        echo"</div></td>"; 
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
        
        <div id="footer"><!--footer della pagina -->
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
        
    </body>
</html> 