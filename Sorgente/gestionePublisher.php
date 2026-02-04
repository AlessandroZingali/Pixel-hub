<?php

require 'serverUtility.php'; 

$service = 0;
$utente = "";

session_start();
if($_SESSION['tipoUtente'] == '2'){
    //solo al publisher puo accedere a questa pagina
} else {
    //se non e admin lo reindirizzo alla homepage
    header("Location: Homepage.php");
    exit();
}
if(isset($_SESSION['userId'])){
    
    $utente = $_SESSION['userName'];
    $service = 1;
}


if(isset($_POST['aggiungiGioco'])){

    $target_dir ="ImmaginiGiochi\\";
    // Percorso finale del file
    $target_file = $target_dir .$_FILES["fileToUpload"]["name"];

    // Controlla se il file è stato effettivamente caricato
    
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // echo "Il file ". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])). " è stato caricato.";
        
        $docGiochi= getDoc('XML/Giochi.xml');
        $root = $docGiochi->documentElement;
        $elemGiochi = $root->childNodes;
            
        if ($elemGiochi->length > 0) {
            $ultimoGioco = $elemGiochi->item($elemGiochi->length - 1);
            $ultimoId = $ultimoGioco->getAttribute("id_gioco");
            $nuovoIdGioco = $ultimoId + 1;
        }
        else if (!($root->hasChildNodes())) $nuovoIdGioco = 1;
        

        $nuovoGioco = $docGiochi->createElement("Gioco");
        $nuovoGioco->setAttribute('id_gioco', $nuovoIdGioco);

        $nuovoNodoTitolo = $docGiochi->createElement("Titolo",$_POST['nuovo_nome']);
        $nuovoNodoPrezzo = $docGiochi->createElement("Prezzo",$_POST['nuovo_prezzo']);
        $nuovoNodoCasaDiSviluppo = $docGiochi->createElement("CasaSviluppo",$_POST['nuova_casa']);
        $nuovoNodoPublisher = $docGiochi->createElement("Publisher",$_POST['nuovo_publisher']);
        $nuovoNodoDescrizione = $docGiochi->createElement("Descrizione",$_POST['nuova_descrizione']);
        $nuovoNodoRequisitiMinimi = $docGiochi->createElement("RequisitiMinimi",$_POST['nuovaRequiMin']);
        $nuovoNodoRequisitiRaccomandati=$docGiochi->createElement("RequisitiRaccomandati",$_POST['nuovaRequiRac']);
        $nuovoNodoDataUscita=$docGiochi->createElement("DataDiUscita",$_POST['nuova_dataUscita']);
        $nuovoNodoImmagine=$docGiochi->createElement("Immagine", $target_file);
        $nuovoNodoGenere=$docGiochi->createElement("Generi",$_POST['nuovo_genere']);
        $nuovoNodoDisponibilie=$docGiochi->createElement("Disponibile",1);
        $nuovoNodoMediaAdmin=$docGiochi->createElement("MediaRecensioniAdmin",$_POST['MediaRecensioniAdmin']);
        $nuovoNodoMediaUtenti=$docGiochi->createElement("MediaRecensioniUtenti",0);
        $nuovoNodoTitoliCorrelati=$docGiochi->createElement("TitoliCorrelati");
        $id_correlati = explode(',',$_POST['id_correlati']);

        foreach ($id_correlati as $id_correlato) {
            $newCorrelato = $docGiochi->createElement("idGiocoCorrelato", trim($id_correlato));
            $nuovoNodoTitoliCorrelati->appendChild($newCorrelato);
        }
        
        $nuovoGioco->appendChild($nuovoNodoTitolo);
        $nuovoGioco->appendChild($nuovoNodoPrezzo);
        $nuovoGioco->appendChild($nuovoNodoCasaDiSviluppo);
        $nuovoGioco->appendChild($nuovoNodoPublisher);
        $nuovoGioco->appendChild($nuovoNodoDescrizione);
        $nuovoGioco->appendChild($nuovoNodoRequisitiMinimi);
        $nuovoGioco->appendChild($nuovoNodoRequisitiRaccomandati);
        $nuovoGioco->appendChild($nuovoNodoDataUscita);
        $nuovoGioco->appendChild($nuovoNodoImmagine);
        $nuovoGioco->appendChild($nuovoNodoGenere); 
        $nuovoGioco->appendChild($nuovoNodoDisponibilie);
        $nuovoGioco->appendChild($nuovoNodoMediaAdmin);
        $nuovoGioco->appendChild($nuovoNodoMediaUtenti);
        $nuovoGioco->appendChild($nuovoNodoTitoliCorrelati);
        
        $root->appendChild($nuovoGioco);
        
        $docGiochi->save('XML/Giochi.xml');

    } else {
        echo "Si è verificato un errore durante il caricamento.";
    }



}

if (isset($_POST['modificaGioco']) && !empty($_POST['id_da_modificare'])) {
    $id_gioco = $_POST['id_da_modificare'];


    $xml = getDoc('XML/Giochi.xml');
    $root = $xml->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $gioco) {
        if ($gioco->getAttribute('id_gioco') == $id_gioco) {
            // var_dump($gioco);
            // echo "<script>console.log($gioco);</script>";

             
            if(isset($_POST['nuovaMediaAdmin'])) $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->nodeValue = htmlspecialchars($_POST['nuovo_nome']);

            
            if(isset($_POST['nuovo_prezzo'])) $gioco->getElementsByTagName("Prezzo")->item(0)->nodeValue = htmlspecialchars($_POST['nuovo_prezzo']);


            if(isset($_POST['nuovaCasa'])) $gioco->getElementsByTagName("CasaSviluppo")->item(0)->nodeValue = htmlspecialchars($_POST['nuovaCasa']);

            if(isset($_POST['nuovoPublisher'])) $gioco->getElementsByTagName("Publisher")->item(0)->nodeValue = htmlspecialchars($_POST['nuovoPublisher']);
            
            if(isset($_POST['nuoviReqMin'])) $gioco->getElementsByTagName("RequisitiMinimi")->item(0)->nodeValue = htmlspecialchars($_POST['nuoviReqMin']);
            
            if(isset($_POST['nuova_descrizione'])) $gioco->getElementsByTagName("Descrizione")->item(0)->nodeValue = htmlspecialchars($_POST['nuova_descrizione']);

            if(isset($_POST['nuovaMediaAdmin'])) $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->nodeValue = htmlspecialchars($_POST['nuovaMediaAdmin']);

            if(isset($_POST['nuovaData'])) $gioco->getElementsByTagName("DataDiUscita")->item(0)->nodeValue = htmlspecialchars($_POST['nuovaData']);

            if(isset($_POST['nuovoGenere'])) $gioco->getElementsByTagName("Generi")->item(0)->nodeValue = htmlspecialchars($_POST['nuovoGenere']);
            
            if(isset($_POST['id_correlati']))$id_correlati = explode(',', $_POST['id_correlati']);

             // Aggiorna i giochi correlati
             if(!empty($id_correlati)){ 
                $correlatiNode = $gioco->getElementsByTagName("TitoliCorrelati")[0];
             while ($correlatiNode->firstChild) {
                 $correlatiNode->removeChild($correlatiNode->firstChild);
             }
             foreach ($id_correlati as $id_correlato) {
                 $newCorrelato = $xml->createElement("idGiocoCorrelato", trim(htmlspecialchars($id_correlato)));
                 $correlatiNode->appendChild($newCorrelato);
             }
             }

            
            
        }
    }
    $xml->save('XML/Giochi.xml');
    header("Location: gestionePublisher.php");
}

if (isset($_POST["sospendi"]) && !empty($_POST["id_gioco_da_sospendere"])) {

    $idGioco = $_POST["id_gioco_da_sospendere"];
    $doc = getDoc("XML/Giochi.xml");
    $root = $doc->documentElement;
    $elem = $root->childNodes;


    foreach ($elem as $gioco) {
        $id = $gioco->getAttribute("id_gioco");
        if ($id == $idGioco) {
            $dispNode = $gioco->getElementsByTagName("Disponibile")->item(0);

        if ($dispNode->textContent == "1") {
            $dispNode->textContent = "0";
        } else {
            $dispNode->textContent = "1";
        }
          
        }

    }
    $doc->save("XML/Giochi.xml");

    header("Location: GestionePublisher.php");
}
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Publisher Board</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/GestionePublisher.css" />
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" />

        <?php 
            if(isset($_POST['cercaGioco']) && !empty($_POST['id_gioco_modifica'])) echo "<script>sessionStorage.setItem(\"activeChange\", \"ricercaGioco\");</script>";
            else if (isset($_POST['cercaGiocoDaSosp']) && !empty($_POST['id_gioco_da_sosp'])) echo "<script>sessionStorage.setItem(\"activeChange\", \"Sospendi\");</script>";
            else if (isset($_POST['cercaUtenteRimborso']) && !empty($_POST['id_user_gestione'])) echo "<script>sessionStorage.setItem(\"activeChange\", \"gestioneRimborso\");</script>";
            else echo "<script>sessionStorage.setItem(\"activeChange\", \"vuoto\");</script>";
        ?> 
        
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <script type="text/javascript" src="Script/cardGestionePublisherChanger.js"></script>
        
        
      
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
                                <a href=\"Profilo.php\">Profilo di $utente</a>
                                </li> 
                                <p id=\"saldo\"> Pixels: ".$_SESSION['Pixels']." </br> Saldo attuale: ".$_SESSION['Saldo']." € </p>";
                            }
                            if($_SESSION['tipoUtente'] == '1'){
                                echo "<li><a href=\"GestioneAdmin.php\">A</a></li>";
                            }
                            if(isset($_SESSION['tipoUtente'])){
                                if($_SESSION['tipoUtente'] == "2")
                                echo "<li><a href=\"gestionePublisher.php\">A</a></li>";
                            }
                        ?>
                    </ul>
                </div>

                    <form id="searchBar" onsubmit="return false;">
                        <input type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>

             <div class="publisherFunctions" id="card0">
                <!-- card 0 di menu -->

                <h1>Menu Funzioni Publisher</h1>
                
                <div class="buttons">


                <div class="aggiungi">
                    <p> - Aggiungi un gioco - >
                        <!-- accedi alla card 2 nascondi la card 0 -->
                        <button onclick="swapperInAggiungiGioco()">  
                            <img src="Stile/Icone/aggiungiicon.png" alt="aggiungibutton" > 
                        </button>
                    </p>
                    </div> 
                     <div class="modifica">
                        <p>- Modifica un gioco presente - >
                        <!-- accedi alla card 3 nascondi la card 0 -->
                        <button onclick="swapperInCercaGioco()">
                             <img src="Stile/Icone/modificaicon.png" alt="ricercabutton" >
                            </button>
                            </p>
                    </div>
                    <div class="sconti">
                    <p> - Vai alla pagina gestione sconti dei miei giochi
                        <!-- accedi alla card 2 nascondi la card 0 -->
                        <button onclick="swapperInCercaGioco()">  
                            <img src="Stile/Icone/scontoicon.png" alt="sconticonbutton" > 
                        </button>
                    </p>
                    </div>
                  
                    <div class="sospendi">
                        <!-- accedi alla card 4 nascondi la 0 -->
                        <p>- Sospendi/Riattiva un gioco dal catalogo - >
                        <button onclick="swapperInSospensione()"> <img src="Stile/Icone/icona elenco.png" alt="sospendibutton" ></button></p>
                    </div>
                

                </div>
            </div>

            <div class="cardSettings hideCard" id="card1">

                <h1>Aggiungi un gioco al sito</h1>

                <?php
                    





                        
                    
                

                echo "<form method='post' action='gestionePublisher.php' enctype=\"multipart/form-data\">
                    
                    <p>
                        <label for=\"nuovo_nome\">Nuovo Titolo:</label>
                        <input type=\"text\" id=\"nuovo_nome\" name=\"nuovo_nome\">
                        </br>
                    </p>

                    <p> 
                        <label for=\"nuovo_prezzo\">Nuovo Prezzo (€):</label>
                        <input type=\"text\" id=\"nuovo_prezzo\" name=\"nuovo_prezzo\">
                        </br>
                    </p>
                    
                    <p>
                        <label for=\"CasaSviluppo\"> Nuova casa di sviluppo :</label>
                        <input type=\"text\" id=\"CasaSviluppo\" name=\"nuova_casa\">
                        </br>
                    </p>
                    
                    <p>
                        <label for=\"Publisher\"> Nuovo publisher :</label>
                        <input type=\"text\" id=\"Publisher\" name=\"nuovo_publisher\" >
                        </br>
                    </p>
                    
                    <p>
                        <label for=\"nuova_descrizione\">Nuova Descrizione:</label>
                        <textarea id=\"nuova_descrizione\" name=\"nuova_descrizione\"></textarea></br>
                    </p>
                    
                    <p>
                        <label for=\"nuovo_genere\"> Nuovo genere :</label>
                        <input type=\"text\" id=\"nuovo_genere\" name=\"nuovo_genere\" ></br>
                    </p>


                    <p>    
                        <label for=\"MediaRecensioniAdmin\"> Nuova media recensioni admin :</label>
                        <input type=\"text\" id=\"MediaRecensioniAdmin\" name=\"MediaRecensioniAdmin\" >
                        </br>
                    </p>
                    
                    <p>
                        <label for=\"nuova_requMin\">Nuovi requisiti raccomandati:</label>
                        <textarea id=\"nuova_requMin\" name=\"nuovaRequiMin\"></textarea>
                        </br>
                    </p>

                    <p>
                        <label for=\"nuova_requRac\">Nuovi requisiti raccomandati:</label>
                        <textarea id=\"nuova_requRac\" name=\"nuovaRequiRac\"></textarea>
                        </br>
                    </p>

                    <p>
                        <label for=\"DataUscita\"> Nuova data di uscita:</label>
                        <input type=\"text\" id=\"DataUscita\" name=\"nuova_dataUscita\" >
                        </br>
                    </p>
                    
                    
                    <p>
                        Seleziona immagine:
                        <input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">
                    </p>

                    <p>
                        <label for=\"id_correlati\">Aggiungi ad ID Giochi Correlati (separati da virgola):</label>
                        <input type=\"text\" id=\"id_correlati\" name=\"id_correlati\"></br>
                    </p>

                    <input type=\"submit\" name=\"aggiungiGioco\" value=\"Aggiungi gioco allo store\"></br>";
                    
                    
                    echo "</form>";
                    

                    ?>

                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInAggiungiGioco()"><img src="Stile/Icone/iconafreccia.png" alt="tornaindietrobutton" ></button>
                    </div>
                </div>
            
            </div>

            <div class="cardSettings hideCard" id="card2">



                         
                <?php              
                $elemGiochi = xmlPointer('XML/Giochi.xml');
                      
                        
                echo"Hai messo questi giochi nel sito: ";
                foreach($elemGiochi as $gioco){
                    $idGioco = $gioco->getAttribute('id_gioco');
                    $idPosseduti = array();

                    $publisherGiocoScanner = $gioco->getElementsByTagName("Publisher")->item(0)->textContent;
                    if($publisherGiocoScanner == $_SESSION['userName']){
                        $idPosseduti[] = $idGioco;
                        if(empty($idPosseduti)){
                            echo"Nessun gioco trovato";
                        }
                        else{echo "ID: $idGioco";
                        echo "<h1>Cerca Gioco</h1>";
                        echo "<form method=\"post\" action=\"gestionePublisher.php\">
                        <label for=\"id_gioco_modifica\">ID Gioco da modificare:</label>
                        <select name=\"id_gioco_modifica\" id=\"id_gioco_modifica\">";
                                
                        foreach($idPosseduti as $id){ 
                            echo "<option value=\"$id\">Gioco $id</option>";
                        }

                        echo "  </select>

                                <input type=\"submit\" name=\"cercaGioco\" value=\"Ricerca\">
                            </form>";

                        }
                        
                    }
                }
                
                ?>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInCercaGioco()"><img src="Stile/Icone/iconafreccia.png" alt="ricercagiocobutton" ></button>
                    </div>
                </div>
            </div>

            <div class="cardSettings hideCard" id="card3">



                <h1>Modifica Gioco</h1>
                <!-- Contenuto per la modifica del gioco -->
                
                
                
                <?php
                if (isset($_POST['id_gioco_modifica']) && !empty($_POST['id_gioco_modifica'])) {
                    $elemGiochi = xmlPointer('XML/Giochi.xml');
                        $elemPointer = $elemGiochi;
                        
                        
                        foreach($elemGiochi as $gioco){
                            if($gioco->getAttribute('id_gioco') == $_POST['id_gioco_modifica']){
                                $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
                                $prezzo = $gioco->getElementsByTagName("Prezzo")->item(0)->textContent;
                                $publisher = $gioco->getElementsByTagName("Publisher")->item(0)->textContent;
                                $casaSviluppo = $gioco->getElementsByTagName("CasaSviluppo")->item(0)->textContent;
                                $dataUscita = $gioco->getElementsByTagName("DataDiUscita")->item(0)->textContent;
                                $generi = $gioco->getElementsByTagName("Generi")->item(0)->textContent;
                                $descrizione = $gioco->getElementsByTagName("Descrizione")->item(0)->textContent;
                                $mediaAdmin = $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent;
                              
                                $requisitiMin = $gioco->getElementsByTagName("RequisitiMinimi")->item(0)->textContent;
                                $requisitiRac = $gioco->getElementsByTagName("RequisitiRaccomandati")->item(0)->textContent;

                                $ids = [];

                                $correlatiNode = $gioco->getElementsByTagName("idGiocoCorrelato");
                                foreach ($correlatiNode as $nodo) {
                                    $ids[] = $nodo->nodeValue;
                                }

                                $correlati = implode(", ", $ids);

                                 
                                

                            }
                        }
                
                echo "<h2>Modifica i dettagli del gioco: $titolo</h2>";
                echo "<form method='post' action='gestionePublisher.php'>
                    <input type='hidden' name='id_da_modificare' value='".htmlspecialchars($_POST['id_gioco_modifica'])."'>
                    
                        
                    
       
                    <p>
                        <label for=\"nuovo_nome\"> Nuovo Nome:</label>
                        <input type=\"text\" id=\"nuovo_nome\" name=\"nuovo_nome\" value=\"$titolo\"> </br>
                       
                    </p>
                    <p> 
                        <label for=\"nuovo_prezzo\">Nuovo Prezzo (€):</label>
                        <input type=\"text\" id=\"nuovo_prezzo\" name=\"nuovo_prezzo\" value=\"$prezzo\" ></br>
                        
                    </p>

                    
                    <p>
                        <label for=\"CasaSviluppo\"> Nuova casa di sviluppo :</label>
                        <input type=\"text\" id=\"CasaSviluppo\" name=\"nuovaCasa\" value=\"$casaSviluppo\" ></br>
                    </p>
                    <p>
                        <label for=\"Publisher\"> Nuovo publisher :</label>
                        <input type=\"text\" id=\"Publisher\" name=\"nuovoPublisher\" value=\"$publisher\" ></br>
                    </p>                    
                    <p>
                        <label for=\"nuova_descrizione\">Nuova Descrizione:</label>
                        <textarea id=\"nuova_descrizione\" name=\"nuova_descrizione\"  >$descrizione</textarea></br>
                    </p>
                    <p>
                        <label for=\"nuovi_requisiti_min\">Nuovi Requisiti minimi:</label>
                        <textarea id=\"nuovi_requisiti_min\" name=\"nuoviReqMin\"  > $requisitiMin </textarea></br>
                    </p>
                    <p>
                        <label for=\"nuovi_requisiti_rac\">Nuovi Requisiti minimi:</label>
                        <textarea id=\"nuovi_requisiti_rac\" name=\"nuoviReqRac\"  > $requisitiRac </textarea></br>
                    </p>

                    <p>
                        <label for=\"DataUscita\"> Nuovo data di uscita :</label>
                        <input type=\"text\" id=\"DataUscita\" name=\"nuovaData\" value=\"$dataUscita\"></br>
                    </p>

                    <p>
                        <label for=\"nuovo_genere\"> Nuovo genere :</label>
                        <input type=\"text\" id=\"nuovo_genere\" name=\"nuovoGenere\" value=\"$generi\"></br>
                    </p>
                    
                    <p>
                        <label for=\"IdCorrelati\">Aggiungi ad ID Giochi Correlati (separati da virgola):</label>
                        <input type=\"text\" id=\"IdCorrelati\" name=\"id_correlati\" value=\"$correlati\"></br>
                    </p>

                    

                    
                    <input type=\"submit\" name=\"modificaGioco\" value=\"Modifica Gioco\"></br>
                    <p>
                    <label for=\"id_correlati_eliminati\">Rimuovi da ID Giochi Correlati:</label> ";
                    
                        $elemGiochi = xmlPointer('XML/Giochi.xml');
                        $elemPointer = $elemGiochi;
                        
                        
                        foreach($elemGiochi as $gioco){
                            if($gioco->getAttribute('id_gioco') == $_POST['id_gioco_modifica']){
                                $ref = $gioco->getElementsByTagName("TitoliCorrelati");
                                foreach($ref as $correlati){
                                    $id_correlato = $correlati->getElementsByTagName("idGiocoCorrelato");
                                    foreach($id_correlato as $id){
                                        $elemGiochiRicerca = $elemPointer;
                                        foreach($elemGiochiRicerca as $giocoRicerca){
                                            if($giocoRicerca->getAttribute('id_gioco') == $id->textContent){
                                                $titolo = $giocoRicerca->getElementsByTagName("Titolo")->item(0)->textContent;
                                            }
                                        }
                                        echo "<br> <input type='checkbox' name='id_correlati_eliminati[]' value='".htmlspecialchars($id->textContent)."'> ID: ".htmlspecialchars($id->textContent)." - Titolo: ".htmlspecialchars($titolo)."<br>";
                                    }
                                }
                            }
                            
                    
                        }
                        echo "<br><input type=\"submit\" name=\"rimuoviCorrelati\" value=\"Rimuovi Correlati\"></p>";
                    echo "</form>";
                    

                        
                }
                            
                    ?>
                    
                  <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInModificaGioco()"><img src="Stile/Icone/iconafreccia.png" alt="modificagiocobutton" ></button>
                    </div>
                </div>

                <div class="cardSettings hideCard" id="card4">

                <h1>Seleziona giochi da sospendere</h1>



                         
                <?php              
                $elemGiochi = xmlPointer('XML/Giochi.xml');
            
                        
                echo"Hai messo questi giochi nel sito: ";
                foreach($elemGiochi as $gioco){
                    $idGioco = $gioco->getAttribute('id_gioco');
                    $idPosseduti = array();

                    $publisherGiocoScanner = $gioco->getElementsByTagName("Publisher")->item(0)->textContent;
                    if($publisherGiocoScanner == $_SESSION['userName']){
                        $idPosseduti[] = $idGioco;
                        if(empty($idPosseduti)){
                            echo"Nessun gioco trovato";
                        }
                        else{echo "ID: $idGioco";
                        echo "<h1>Cerca Gioco</h1>";
                        echo "<form method=\"post\" action=\"gestionePublisher.php\">
                        <label for=\"id_gioco_modifica\">ID Gioco da modificare:</label>
                        <select name=\"id_gioco_da_sosp\" id=\"id_gioco_modifica\">";
                                
                        foreach($idPosseduti as $id){ 
                            echo "<option value=\"$id\">Gioco $id</option>";
                        }

                        echo "  </select>

                                <input type=\"submit\" name=\"cercaGiocoDaSosp\" value=\"Sospendi\">
                            </form>";

                        }
                        
                    }
                }
                
                ?>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInCercaGioco()"><img src="Stile/Icone/iconafreccia.png" alt="ricercagiocobutton" ></button>
                    </div>
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
