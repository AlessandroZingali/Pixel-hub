<?php
/* Questa pagina viene usata per dare un Hub gestionale ai vari publisher. Fornisce funzioni per la publicazione di un gioco sul sito, tra cui
l'inserimento sulla piattaforma e l'assegnazione del gioco in una o più delle varie "Pool" di sconti. Inoltre
In questa sezione è fornita agli utenti la possibilita di essere visti come una pagina di un aziende più che come un utente (modalità Agency)
permettendo di caricare il proprio logo*/
require 'serverUtility.php'; 

function scontoTranslate($id) {
    switch ($id) {
        case 1:
            return "N crediti finora";
        case 2:
            return "M crediti da una certa data";
        case 3:
            return "Acquistata una certa offerta(giochi correlati)";
        case 4:
            return "in base alla reputazione";
        case 5:
            return "clienti che sono con noi da X mesi";
        case 6:
            return "clienti che sono con noi da Y anni";
        case 7:
            return "per una certa casa di sviluppo";
        case 8:
            return "genere preferito";
        case 9:
            return "sconto indipendente";
        default:
            return "Sconto Sconosciuto";
    }

}

$service = 0;
$utente = "";
$inBound = true;
$campiVuoti = false;

session_start();
if($_SESSION['tipoUtente'] == '1'){
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

//Funzione per aggiungere un gioco al sito, viene usata la funzione move_uploaded_file per spostare 
// l'immagine caricata nella cartella "ImmaginiGiochi" e poi viene creato un nuovo nodo "Gioco" nel file XML "Giochi.xml" 
// con i dati inseriti dall'utente. Viene anche gestita la generazione di un nuovo ID per il gioco basato sull'ultimo ID presente nel file XML. 
// Se il file viene caricato correttamente, il nuovo gioco viene aggiunto al file XML e salvato. 
// In caso di errori durante il caricamento dell'immagine o dei dati, vengono visualizzati messaggi di errore appropriati.
if(isset($_POST['aggiungiGioco'])){

    $target_dir ="ImmaginiGiochi\\";
    // Percorso finale del file
    $target_file = $target_dir .$_FILES["fileToUpload"]["name"];

    // Controlla se il file è stato effettivamente caricato

    // Inserisce i vari campi per i requisiti
        $os = $_POST["req_osm"];
        $cpu = $_POST["req_cpum"];
        $ram = $_POST["req_ramm"];
        $gpu = $_POST["req_gpum"];
        $dx = $_POST["req_dxm"];
        $net = $_POST["req_netm"];
        $storage = $_POST["req_storagem"];
        

        $_POST['nuovaRequiMin'] = "OS minimo : $os; Processore minimo: $cpu; Ram richiesta: $ram; GPU Minima: $gpu; DirectX: $dx; Banda di rete minima: $net; Spazio Minimo e memoria : $storage;";            
        
        $os = $_POST["req_osr"];
        $cpu = $_POST["req_cpur"];
        $ram = $_POST["req_ramr"];
        $gpu = $_POST["req_gpur"];
        $dx = $_POST["req_dxr"];
        $net = $_POST["req_netr"];
        $storage = $_POST["req_storager"];
        

        $_POST['nuovaRequiRac'] = "OS: $os; Processore Consiato: $cpu; Ram Consigliata: $ram; GPU Consigliata: $gpu; DirectX: $dx; Network: $net; Spazio Raccomandato e t memoria: $storage; ";
        
    if (isset($_FILES["fileToUpload"]["name"]) 
        && isset($_POST['nuovo_nome']) 
        && isset($_POST['nuovo_prezzo'])   
        && isset($_POST['nuova_casa']) 
        && isset($_POST['nuovo_publisher']) 
        && isset($_POST['nuova_descrizione']) 
        && isset($_POST['nuovaRequiMin']) 
        && isset($_POST['nuovaRequiRac']) 
        && isset($_POST['nuova_dataUscita']) 
        && isset($_POST['nuovo_genere']) 
        && isset($_POST['MediaRecensioniAdmin']) 
        && !empty($_FILES["fileToUpload"]["name"]) 
        && !empty($_POST['nuovo_nome']) 
        && !empty($_POST['nuovo_prezzo']) 
        && !empty($_POST['nuova_casa']) 
        && !empty($_POST['nuovo_publisher']) 
        && !empty($_POST['nuova_descrizione']) 
        && !empty($_POST['nuovaRequiMin']) 
        && !empty($_POST['nuovaRequiRac']) 
        && !empty($_POST['nuova_dataUscita']) 
        && !empty($_POST['nuovo_genere'])
        && !empty($_POST['MediaRecensioniAdmin'])) {


        if(preg_match('/^([0-9]+(, *[0-9]+)*)?$/', $_POST['id_correlati']) &&
                preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['nuovo_prezzo']) &&
                preg_match('/^[0-9]+$/', $_POST['MediaRecensioniAdmin']) &&
                preg_match('/^[0-9]{2}.[0-9]{2}.[0-9]{4}$/', $_POST['nuova_dataUscita'])){
             // echo "Il file ". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])). " è stato caricato.";
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
            
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

                    if(isset($_POST['id_correlati'])){
                        if(!empty($_POST['id_correlati'])){
                            
                            $id_correlati = explode(',',$_POST['id_correlati']);
                            
                            foreach ($id_correlati as $id_correlato) {
                                $newCorrelato = $docGiochi->createElement("idGiocoCorrelato", trim($id_correlato));
                                $nuovoNodoTitoliCorrelati->appendChild($newCorrelato);
                            }
                        }
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
                    
                }
            else{
                echo "Si è verificato un errore durante il caricamento dell'immagine.";
            }
        }
        else{
            $esitoCorrelati = !preg_match('/^([0-9]+(, *[0-9]+)*)?$/', $_POST['id_correlati']);
            $esitoPrezzo = !preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['nuovo_prezzo']);
            $esitoMediaAdmin = !preg_match('/^[0-9]+$/', $_POST['MediaRecensioniAdmin']);
            $esitoData = !preg_match('/^[0-9]{2}.[0-9]{2}.[0-9]{4}$/', $_POST['nuova_dataUscita']);
            if($esitoCorrelati){
                echo "<script type='text/javascript'>alert('Formato ID correlati non valido. Deve essere una lista di numeri separati da una virgola');</script>";
            }
            else if($esitoPrezzo){
                echo "<script type='text/javascript'>alert('Formato prezzo non valido. Deve essere un numero decimale');</script>";
            }
            else if($esitoMediaAdmin){
                echo "<script type='text/javascript'>alert('Formato media admin non valido. Deve essere un numero intero');</script>";
            }
            else if($esitoData){
                echo "<script type='text/javascript'>alert('Formato data non valido. Deve essere nel formato GG-MM-AAAA');</script>";
            }
        } 
        
    }
   else {
        echo "Si è verificato un errore durante il caricamento.";
    }



}

// Funzione per modificare un gioco presente sul sito, viene usata la funzione getDoc per caricare il file XML "Giochi.xml" 
// e poi viene cercato il gioco con l'ID specificato dall'utente.
if (isset($_POST['modificaGioco']) && !empty($_POST['id_da_modificare'])) {
    $id_gioco = $_POST['id_da_modificare'];


    $xml = getDoc('XML/Giochi.xml');
    $root = $xml->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $gioco) {
        if ($gioco->getAttribute('id_gioco') == $id_gioco) {
            // var_dump($gioco);
            // echo "<script>console.log($gioco);</script>";

             
            if(isset($_POST['nuovo_nome'])) $gioco->getElementsByTagName("Titolo")->item(0)->textContent = $_POST['nuovo_nome'];
            
            if(isset($_POST['nuovo_prezzo'])) $gioco->getElementsByTagName("Prezzo")->item(0)->textContent = $_POST['nuovo_prezzo'];

            if(isset($_POST['nuovaCasa'])) $gioco->getElementsByTagName("CasaSviluppo")->item(0)->textContent = $_POST['nuovaCasa'];

            if(isset($_POST['nuovoPublisher'])) $gioco->getElementsByTagName("Publisher")->item(0)->textContent = $_POST['nuovoPublisher'];
            
            if(isset($_POST['nuoviReqMin'])) $gioco->getElementsByTagName("RequisitiMinimi")->item(0)->textContent = $_POST['nuoviReqMin'];

            if(isset($_POST['nuoviReqRac'])) $gioco->getElementsByTagName("RequisitiRaccomandati")->item(0)->textContent = $_POST['nuoviReqRac'];
            
            if(isset($_POST['nuova_descrizione'])) $gioco->getElementsByTagName("Descrizione")->item(0)->textContent = $_POST['nuova_descrizione'];

            if(isset($_POST['nuovaMediaAdmin'])) $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent = $_POST['nuovaMediaAdmin'];

            if(isset($_POST['nuovaData'])) $gioco->getElementsByTagName("DataDiUscita")->item(0)->textContent = $_POST['nuovaData'];

            if(isset($_POST['nuovoGenere'])) $gioco->getElementsByTagName("Generi")->item(0)->textContent = $_POST['nuovoGenere'];

            if(isset($_POST['nuovaDisponibilità'])) {
                if($gioco->getElementsByTagName("Disponibile")->item(0)->textContent == 0){
                    $gioco->getElementsByTagName("Disponibile")->item(0)->textContent = 1;
                }
            else if ($gioco->getElementsByTagName("Disponibile")->item(0)->textContent == 1){
            $gioco->getElementsByTagName("Disponibile")->item(0)->textContent = 0;
                }
            }
            
            
            if(isset($_POST['id_correlati'])){
                    if(!empty($_POST['id_correlati'])){
                    $id_correlati = explode(',', $_POST['id_correlati']);
                    $correlatiNode = $gioco->getElementsByTagName("TitoliCorrelati")[0];
                    foreach ($id_correlati as $key => $id_correlato) {
                        foreach ($correlatiNode->childNodes as $correlato){
                            if($correlato->textContent == trim($id_correlato)){
                                unset($id_correlati[$key]);
                            }
                        }
                    }
                    $id_correlati=array_map('trim', $id_correlati);
                    foreach ($id_correlati as $id_correlato) {
                        $newCorrelato = $xml->createElement("idGiocoCorrelato", $id_correlato);
                        $correlatiNode->appendChild($newCorrelato);
                    }
                }
            }

            
            
        }
    }
    $xml->save('XML/Giochi.xml');
    header("Location: gestionePublisher.php");
}

if(isset($_POST['rimuoviCorrelati']) && !empty($_POST['id_correlati_eliminati']) && !empty($_POST['id_da_modificare'])){
    $id_gioco = $_POST['id_da_modificare'];
    $doc = getDoc('XML/Giochi.xml');
    $root = $doc->documentElement;
    $elem = $root->childNodes;
    foreach ($elem as $gioco) {
        if ($gioco->getAttribute('id_gioco') == $id_gioco) {
            $correlatiNode = $gioco->getElementsByTagName("TitoliCorrelati")[0];
            foreach ($_POST['id_correlati_eliminati'] as $id_correlato_eliminato) {
                $nodiDaRimuovere = [];
                foreach($correlatiNode->childNodes as $correlato){
                    if ($correlato->textContent == $id_correlato_eliminato) {
                        array_push($nodiDaRimuovere, $correlato);
                    }
                }
                foreach ($nodiDaRimuovere as $nodo) {
                    $correlatiNode->removeChild($nodo);
                }
            }
        }
    }
    $doc->save('XML/Giochi.xml');
    header("Location: gestionePublisher.php");
}

if(isset($_POST['eliminaGioco']) && !empty($_POST['id_da_modificare'])){
    $id_gioco = $_POST['id_da_modificare'];
    $doc = getDoc('XML/Giochi.xml');
    $root = $doc->documentElement;
    $elem = $root->childNodes;
    
    foreach ($elem as $gioco) {
        if ($gioco->getAttribute('id_gioco') == $id_gioco) {
            $root->removeChild($gioco);
            break;
        }
    }
    $doc->save('XML/Giochi.xml');
    header("Location: gestionePublisher.php");
}


// Funzione per sospendere o riattivare un gioco presente sul sito, viene usata la funzione getDoc per caricare il file XML "Giochi.xml"
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


// Funzione per aggiornare gli sconti di un gioco presente sul sito, viene usata la funzione getDoc per caricare il file XML "Sconti.xml"
//  e poi viene cercato il gioco con l'ID specificato dall'utente.
if(isset($_POST['aggiornaScontiPublisher'])){
    $idGioco = $_POST['id_gioco_sconto'];
    $sommaFinaleSconti = [];
    foreach ($_POST as $chiave => $valore) {
        // Trova le checkbox "sconto" + numero
        if (preg_match('/^sconto(\d+)$/', $chiave, $matches)) {
            $indice = $matches[1]; 
            
            // Prendi il valore del select corrispondente
            $valoreSconto = $_POST["valoreSconto$indice"] ?? null;

            if($valoreSconto>0 && $valoreSconto != null) array_push($sommaFinaleSconti, intval($valoreSconto));
        }
    }
    $ris=intval(array_sum($sommaFinaleSconti));
    if($ris>70) $inBound = false; // Se la somma totale degli sconti è maggiore di 70, considera i dati come non validi

    if($inBound){
        $docSconti = getDoc('XML/Sconti.xml');
        $root = $docSconti->documentElement;
        $elemSconti = $root->childNodes;

        foreach($elemSconti as $sconto){
            $scontoType = $sconto->getAttribute('id_tipoSconto');
            $chiave = "sconto".$scontoType;
            if(isset($_POST[$chiave])){
                $valoreSconto = $_POST["valoreSconto$scontoType"] ?? null;
                $giocoTrovato = false;

                foreach($sconto->childNodes as $gioco){
                    if($gioco->textContent == $idGioco){
                        if($valoreSconto != null && $valoreSconto > 0){
                            $gioco->setAttribute('valoreSconto', $valoreSconto);
                            $giocoTrovato = true;
                            break;
                        }
                    }
                }

                if(!$giocoTrovato){
                    $nuovoGiocoSconto = $docSconti->createElement("Gioco", $idGioco);
                    $nuovoGiocoSconto->setAttribute('valoreSconto', $valoreSconto);;
                    $sconto->appendChild($nuovoGiocoSconto);
                }
            }
            else {
                foreach($sconto->childNodes as $gioco){
                    if($gioco->textContent == $idGioco){
                        $sconto->removeChild($gioco);
                        break;
                    }
                }
            }
        }
        $docSconti->save('XML/Sconti.xml');
    }
    
}


// Funzione per visualizzare gli sconti di un gioco presente sul sito
if ((isset($_POST["gestioneScontiPublisher"])) || !$inBound) {
  
    $listaSconti = [];
    if($inBound){
        if(empty($_POST['id_gioco_publisher'])) $campiVuoti = true;
        else{
            $elemGiochi = xmlPointer("XML/Giochi.xml");

            foreach($elemGiochi as $gioco){
                if($gioco->getAttribute('id_gioco') == $_POST['id_gioco_publisher']) $nomeGioco = $gioco->getElementsByTagName('Titolo')->item(0)->textContent;
            }
            $idGioco = $_POST['id_gioco_publisher'];
        }
    }
    else{
        $idGioco = $_POST['id_gioco_sconto'];
        $elemGiochi = xmlPointer("XML/Giochi.xml");

        foreach($elemGiochi as $gioco){
            if($gioco->getAttribute('id_gioco') == $idGioco) $nomeGioco = $gioco->getElementsByTagName('Titolo')->item(0)->textContent;
        }
    }

    if(!$campiVuoti){
        $elem = xmlPointer('XML/Sconti.xml');
        
        foreach($elem as $sconto){
            $type = $sconto->getAttribute('id_tipoSconto');
            foreach($sconto->childNodes as $gioco){
                $riga =[];
                if($idGioco == $gioco->textContent){
                    $riga = ['tipoSconto' => $type, 'valoreSconto' => $gioco->getAttribute('valoreSconto')];
                    array_push($listaSconti, $riga);
                }
            }
        }
    }

}

// Funzione per attivare o disattivare la modalità agency per un publisher
if (isset($_POST["agencyToggleSubmit"])) {

    $idUtente = $_SESSION["userId"];

    $doc = getDoc("XML/utenti.xml");
    $root = $doc->documentElement;
    $elem = $root->childNodes;

    // Cambimo modalita agency
    foreach ($elem as $userNode) {
        if ($userNode->getAttribute('id_user') == $idUtente) {
            if(!isset($_POST['newModAgency'])) {
                $userNode->getElementsByTagName('ToggleAgency')->item(0)->textContent = 'false';
                $_SESSION['agencyMod'] = false;

            }else {
                $userNode->getElementsByTagName('ToggleAgency')->item(0)->textContent = 'true';
                $_SESSION['agencyMod'] = true;
            }
             
        }
    }

    $doc->save("XML/utenti.xml");
}


// Funzione per caricare il logo di un publisher in modalità agency
if (isset($_POST["agencyToggleSubmit"])){
    $idUtente = $_SESSION["userId"];
    $target_dir ="loghiPub\\";
    var_dump($_FILES["agencyImageUpload"]);
    $nameLogo = $_SESSION['userName']."_logo.png";
    $target_file = $target_dir.$nameLogo;
    $pointDB = new connectionDB();

    // Controlla se il file è stato effettivamente caricato
    if (move_uploaded_file($_FILES["agencyImageUpload"]["tmp_name"], $target_file)) {
        
        $newPath = "loghiPub/$nameLogo";
        $mysqliConnection = $pointDB->connectDB();

        if (mysqli_connect_errno()){
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }
        $sql = "
            UPDATE {$pointDB->getTableUsers()}
            SET imgProfiloPathPub = '$newPath'
            WHERE ID = ".(int)$_SESSION['userId'].";
        ";
        $resultQ = mysqli_query($mysqliConnection, $sql);
        if($resultQ){
            header("Location:gestionePublisher.php");
        }
        else {
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }
    }

    
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
            if(isset($_POST['cercaGioco']) && !empty($_POST['id_gioco_modifica'])) 
                echo "<script>sessionStorage.setItem(\"activeChange\", \"ricercaGioco\");</script>";

            else if (isset($_POST['cercaGiocoDaSosp']) && !empty($_POST['id_gioco_da_sosp'])) 
                echo "<script>sessionStorage.setItem(\"activeChange\", \"Sospendi\");</script>";
            else if (isset($_POST['aggiornaScontiPublisher']) && !$inBound) 
                echo "<script>sessionStorage.setItem(\"activeChange\", \"noBoundSconti\");</script>";
            else if (isset($_POST["gestioneScontiPublisher"]) && (!$campiVuoti))
                echo "<script>sessionStorage.setItem(\"activeChange\", \"gestioneSconti\");</script>";
            else if (isset($_POST["gestioneScontiPublisher"]) && ($campiVuoti))
                echo "<script>sessionStorage.setItem(\"activeChange\", \"campiVuotiSconti\");</script>";
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
                                    <a href=\"Profilo.php\">Profilo </a>
                                    </li> 
                                    <p id=\"saldo\"> Pixels: ".$_SESSION['Pixels']." </br> Saldo attuale: ".$_SESSION['Saldo']." € </p>";
                                
                                
                                if($_SESSION['tipoUtente'] == '2'){
                                    echo "<li><a href=\"GestioneAdmin.php\">Gestione</a></li>";
                                }
                                
                                    if($_SESSION['tipoUtente'] == "1"){
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
                        <!-- accedi alla card 4 nascondi la card 0 -->
                        <button onclick="swapperInGestioneSconti()">  
                            <img src="Stile/Icone/scontoicon.png" alt="sconticonbutton" > 
                        </button>
                    </p>
                    </div>

                    <div class="agency">
                    <p> - Attiva la modalità agency -> 
                        <!-- accedi alla card 4 nascondi la card 0 -->
                        <button onclick="swapperInAgency()">  
                            <img src="Stile/Icone/switchPub.png" alt="switchPubbutton" > 
                        </button>
                    </p>
                    </div>
    
                

                </div>
            </div>


            <div class="cardSettings hideCard" id="card1">

                <h1>Aggiungi un gioco al sito</h1>

                <?php

                $setValoriGenere = [
                            'Nessuno',
                            'Sparatutto',
                            'RPG',
                            'Avventura',
                            'Souls-like',
                            'Strategia',
                            'Rouge-like',
                            'Picchiaduro',
                            'Azione',
                            'Simulazione'
                        ];
 
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
                    
                    
                        
                        <input type=\"hidden\" id=\"Publisher\" name=\"nuovo_publisher\" value=\"{$_SESSION['userName']}\">
                        
                    
                    <p>
                        <label for=\"nuova_descrizione\">Nuova Descrizione:</label>
                        <textarea rows=\"1\" id=\"nuova_descrizione\" name=\"nuova_descrizione\"></textarea>
                        </br>
                    </p>
                    
                    <p>
                        <label for=\"nuovo_genere\"> Nuovo genere :</label>
                        <select id=\"nuovo_genere\" name=\"nuovo_genere\" ></br>
                            <option value=\"{$setValoriGenere[0]}\" ";
                            
                            echo ">{$setValoriGenere[0]}</option>
                            <option value=\"{$setValoriGenere[1]}\"";
                            
                            echo ">{$setValoriGenere[1]}</option> 
                            <option value=\"{$setValoriGenere[2]}\" ";
                            
                            echo ">{$setValoriGenere[2]}</option>
                            <option value=\"{$setValoriGenere[3]}\" ";
                            
                            echo ">{$setValoriGenere[3]}</option>
                            <option value=\"{$setValoriGenere[4]}\" ";
                           
                            echo ">{$setValoriGenere[4]}</option>
                            <option value=\"{$setValoriGenere[5]}\" ";
                            
                            echo ">{$setValoriGenere[5]}</option>
                            <option value=\"{$setValoriGenere[6]}\" ";
                            
                            echo ">{$setValoriGenere[6]}</option>
                            <option value=\"{$setValoriGenere[7]}\" ";
                            
                            echo ">{$setValoriGenere[7]}</option>
                            <option value=\"{$setValoriGenere[8]}\" ";
                            
                            echo ">{$setValoriGenere[8]}</option>
                            <option value=\"{$setValoriGenere[9]}\" ";
                            
                            echo ">{$setValoriGenere[9]}</option>
                        </select>
                    </p>


                    <p>    
                        <label for=\"MediaRecensioniAdmin\"> Nuova media recensioni admin :</label>
                        <input type=\"text\" id=\"MediaRecensioniAdmin\" name=\"MediaRecensioniAdmin\" >
                        </br>
                    </p>

                    <p>Requisiti Minimi</br></br></p>
                    <ul>
                        <li>
                            <label for=\"req_os\"> OS:</label>
                            <input type=\"text\" id=\"req_os\" name=\"req_osm\" placeholder=\"Es: Windows 10 64-bit\">
                        </li></br>
                        <li>
                            <label for=\"req_cpu\">Processor:</label>
                            <input type=\"text\" id=\"req_cpu\" name=\"req_cpum\" placeholder=\"Es: Intel i5-8400 / Ryzen 5 2600\">
                        </li></br>

                        <li>
                            <label for=\"req_ram\">Memory:</label>
                            <input type=\"text\" id=\"req_ram\" name=\"req_ramm\" placeholder=\"Es: 16 GB RAM\">
                        </li></br>

                        <li>
                            <label for=\"req_gpu\">Graphics:</label>
                            <input type=\"text\" id=\"req_gpu\" name=\"req_gpum\" placeholder=\"Es: GTX 1060 / RX 580\">
                        </li></br>

                        <li>
                            <label for=\"req_dx\">DirectX:</label>
                            <input type=\"text\" id=\"req_dx\" name=\"req_dxm\" placeholder=\"Es: Version 12\">
                        </li></br>

                        <li>
                            <label for=\"req_net\">Network:</label>
                            <input type=\"text\" id=\"req_net\" name=\"req_netm\" placeholder=\"Es: Broadband Internet connection\">
                        </li></br>

                        <li>
                            <label for=\"req_storage\">Storage:</label>
                            <input type=\"text\" id=\"req_storage\" name=\"req_storagem\" placeholder=\"Es: 50 GB available space\">
                        </li></br>

                        <li>
                            <label for=\"req_sound\">Sound Card:</label>
                            <input type=\"text\" id=\"req_sound\" name=\"req_soundm\" placeholder=\"Es: DirectX compatible\">
                        </li></br>

                    </ul>
                    
                    <p>Requisiti Raccomandati</p></br>
                    <ul>
                        <li>
                            <label for=\"req_os\"> OS:</label>
                            <input type=\"text\" id=\"req_os\" name=\"req_osr\" placeholder=\"Es: Windows 10 64-bit\">
                        </li></br>

                        <li>
                            <label for=\"req_cpu\">Processor:</label>
                            <input type=\"text\" id=\"req_cpu\" name=\"req_cpur\" placeholder=\"Es: Intel i5-8400 / Ryzen 5 2600\">
                        </li></br>

                        <li>
                            <label for=\"req_ram\">Memory:</label>
                            <input type=\"text\" id=\"req_ram\" name=\"req_ramr\" placeholder=\"Es: 16 GB RAM\">
                        </li></br>

                        <li>
                            <label for=\"req_gpu\">Graphics:</label>
                            <input type=\"text\" id=\"req_gpu\" name=\"req_gpur\" placeholder=\"Es: GTX 1060 / RX 580\">
                        </li></br>

                        <li>
                            <label for=\"req_dx\">DirectX:</label>
                            <input type=\"text\" id=\"req_dx\" name=\"req_dxr\" placeholder=\"Es: Version 12\">
                        </li></br>

                        <li>
                            <label for=\"req_net\">Network:</label>
                            <input type=\"text\" id=\"req_net\" name=\"req_netr\" placeholder=\"Es: Broadband Internet connection\">
                        </li></br>

                        <li>
                            <label for=\"req_storage\">Storage:</label>
                            <input type=\"text\" id=\"req_storage\" name=\"req_storager\" placeholder=\"Es: 50 GB available space\">
                        </li></br>

                        <li>
                            <label for=\"req_sound\">Sound Card:</label>
                            <input type=\"text\" id=\"req_sound\" name=\"req_soundr\" placeholder=\"Es: DirectX compatible\">
                        </li></br>
                    </ul>




                    <p>
                        <label for=\"DataUscita\"> Nuova data di uscita:</label>
                        <input type=\"text\" id=\"DataUscita\" name=\"nuova_dataUscita\" >
                        </br>
                    </p>
                    
                    <div class=\"fileUploadBase\">
                    <div class=\"file-upload-wrapper\">
                            <label for=\"fileToUpload\" class=\"btn-upload\">
                                Carica immagine
                            </label>
                            <input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\" accept=\"image/*\"/>
                        </div>
                    </div>

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
            
            <!-- Div per la card di ricerca giochi -->



                         
                            <?php              
                            $elemGiochi = xmlPointer('XML/Giochi.xml');

                            echo "<h1>Cerca Gioco</h1>";     
                            echo "Hai messo questi giochi nel sito: ";

                            $idPosseduti = array(); // lo dichiari fuori dal foreach

                            foreach($elemGiochi as $gioco){

                                $publisherGiocoScanner = $gioco->getElementsByTagName("Publisher")->item(0)->textContent;

                                if($publisherGiocoScanner == $_SESSION['userName']){
                                    $idPosseduti[] = $gioco->getAttribute('id_gioco');
                                }
                            }

                            if(empty($idPosseduti)){
                                echo "Nessun gioco trovato";
                            }
                            else{

                                echo "<form method=\"post\" action=\"gestionePublisher.php\">
                                        <label for=\"id_gioco_modifica\">ID Gioco da modificare:</label>
                                        <select name=\"id_gioco_modifica\" id=\"id_gioco_modifica\">";

                                foreach($elemGiochi as $gioco){

                                    $publisher = $gioco->getElementsByTagName("Publisher")->item(0)->textContent;

                                    if($publisher == $_SESSION['userName']){

                                        $id = $gioco->getAttribute('id_gioco');
                                        $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;

                                        echo "<option value=\"$id\">$id - $titolo</option>";
                                    }
                                }

                                echo "  </select>
                                        <input type=\"submit\" name=\"cercaGioco\" value=\"Ricerca\">
                                    </form>";
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
                            if(($gioco->getAttribute('id_gioco') == $_POST['id_gioco_modifica'])){
                                $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
                                $prezzo = $gioco->getElementsByTagName("Prezzo")->item(0)->textContent;
                                $publisher = $gioco->getElementsByTagName("Publisher")->item(0)->textContent;
                                $casaSviluppo = $gioco->getElementsByTagName("CasaSviluppo")->item(0)->textContent;
                                $dataUscita = $gioco->getElementsByTagName("DataDiUscita")->item(0)->textContent;
                                $generi = $gioco->getElementsByTagName("Generi")->item(0)->textContent;
                                $descrizione = $gioco->getElementsByTagName("Descrizione")->item(0)->textContent;
                                $mediaAdmin = $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent;
                                $disponibile =$gioco->getElementsByTagName("Disponibile")->item(0)->textContent;
                              
                                $requisitiMin = $gioco->getElementsByTagName("RequisitiMinimi")->item(0)->textContent;
                                $requisitiRac = $gioco->getElementsByTagName("RequisitiRaccomandati")->item(0)->textContent;

                                $ids = [];

                                $correlatiNode = $gioco->getElementsByTagName("idGiocoCorrelato");
                                foreach ($correlatiNode as $nodo) {
                                    $ids[] = $nodo->nodeValue;
                                }

                                

                                 
                                

                            }
                        }
                
                echo "<h2>Modifica i dettagli del gioco: $titolo</h2>";
                echo "<form method='post' action='gestionePublisher.php'>
                    <input type='hidden' name='id_da_modificare' value='".$_POST['id_gioco_modifica']."'>
                    
                    <p>
                        <label for=\"nuovo_nome\"> Nuovo Nome:</label>
                        <input type=\"text\" id=\"nuovo_nome\" name=\"nuovo_nome\" value=\"$titolo\"> </br>
                       
                    </p>
                    <p> 
                        <label for=\"nuovo_prezzo\">Nuovo Prezzo (€):</label>
                        <input type=\"text\" id=\"nuovo_prezzo\" name=\"nuovo_prezzo\" value=\"$prezzo\"></br>
                    </p>

                    
                    <p>
                        <label for=\"CasaSviluppo\"> Nuova casa di sviluppo :</label>
                        <input type=\"text\" id=\"CasaSviluppo\" name=\"nuovaCasa\" value=\"$casaSviluppo\"></br>
                    </p>
                    <p>
                        <label for=\"Publisher\"> Nuovo publisher :</label>
                        <input type=\"text\" id=\"Publisher\" name=\"nuovoPublisher\" value=\"$publisher\"></br>
                    </p>                    
                    <p>
                        <label for=\"nuova_descrizione\">Nuova Descrizione:</label>
                        <textarea id=\"nuova_descrizione\" name=\"nuova_descrizione\">$descrizione</textarea></br>
                    </p>
                    <p>
                        <label for=\"nuovi_requisiti_min\">Nuovi Requisiti minimi:</label>
                        <textarea id=\"nuovi_requisiti_min\" name=\"nuoviReqMin\">$requisitiMin</textarea></br>
                    </p>
                    <p>
                        <label for=\"nuovi_requisiti_rac\">Nuovi Requisiti raccomandati:</label>
                        <textarea id=\"nuovi_requisiti_rac\" name=\"nuoviReqRac\">$requisitiRac</textarea></br>
                    </p>

                    <p>
                        <label for=\"DataUscita\"> Nuovo data di uscita :</label>
                        <input type=\"text\" id=\"DataUscita\" name=\"nuovaData\" value=\"$dataUscita\"></br>
                    </p>

                    <p>
                        <label for=\"nuovo_genere\"> Nuovo genere :</label>
                        <select id=\"nuovo_genere\" name=\"nuovoGenere\">
                            <option value=\"{$setValoriGenere[0]}\" ";
                            if($generi == $setValoriGenere[0]) echo " selected";
                            echo ">{$setValoriGenere[0]}</option>
                            <option value=\"{$setValoriGenere[1]}\"";
                            if($generi == $setValoriGenere[1]) echo " selected";
                            echo ">{$setValoriGenere[1]}</option> 
                            <option value=\"{$setValoriGenere[2]}\" ";
                            if($generi == $setValoriGenere[2]) echo " selected";
                            echo ">{$setValoriGenere[2]}</option>
                            <option value=\"{$setValoriGenere[3]}\" ";
                            if($generi == $setValoriGenere[3]) echo " selected";
                            echo ">{$setValoriGenere[3]}</option>
                            <option value=\"{$setValoriGenere[4]}\" ";
                            if($generi == $setValoriGenere[4]) echo " selected";
                            echo ">{$setValoriGenere[4]}</option>
                            <option value=\"{$setValoriGenere[5]}\" ";
                            if($generi == $setValoriGenere[5]) echo " selected";
                            echo ">{$setValoriGenere[5]}</option>
                            <option value=\"{$setValoriGenere[6]}\" ";
                            if($generi == $setValoriGenere[6]) echo " selected";
                            echo ">{$setValoriGenere[6]}</option>
                            <option value=\"{$setValoriGenere[7]}\" ";
                            if($generi == $setValoriGenere[7]) echo " selected";
                            echo ">{$setValoriGenere[7]}</option>
                            <option value=\"{$setValoriGenere[8]}\" ";
                            if($generi == $setValoriGenere[8]) echo " selected";
                            echo ">{$setValoriGenere[8]}</option>
                            <option value=\"{$setValoriGenere[9]}\" ";
                            if($generi == $setValoriGenere[9]) echo " selected";
                            echo ">{$setValoriGenere[9]}</option>
                        </select>
                        </br>
                    </p>

                    <p>
                        <label for=\"nuovo_media_admin\"> Inserisci media Admin :</label>
                        <input type=\"text\" id=\"nuovo_media_admin\" name=\"nuovaMediaAdmin\" value=\"$mediaAdmin\"></br>
                    </p>

                    <p>
                        <label for=\"disponibile\"> Cambia la disponibilita nel sito :</label>
                        <input type=\"checkbox\" id=\"disponibile\" name=\"nuovaDisponibilità\" value=\"$disponibile\" ";

                        if($disponibile == "1") echo " checked=\"checked\"> Disponibile </input>";
                        else echo "> Non disponibile </input>";
                        
                    echo "</br>
                    </p>

                    <p>
                        <label for=\"IdCorrelati\">Aggiungi ad ID Giochi Correlati (separati da virgola):</label>
                        <input type=\"text\" id=\"IdCorrelati\" name=\"id_correlati\" value=\"\"></br>
                    </p>

                    <p>
                        <label for=\"elimina_gioco\">Elimina gioco dallo store:</label>
                        <input type=\"button\" id=\"elimina_gioco\" name=\"elimina_gioco\" value=\"Elimina\" onclick=\"eliminaGioco()\"></p>

                    <script>
                        function eliminaGioco() {
                            if (confirm('Sei sicuro di voler eliminare questo gioco? Questa azione è irreversibile.')) {
                                // Se l'utente conferma, invia un form nascosto per eliminare il gioco
                                var form = document.createElement('form');
                                form.method = 'post';
                                form.action = 'gestionePublisher.php';

                                var inputId = document.createElement('input');
                                inputId.type = 'hidden';
                                inputId.name = 'id_da_modificare';
                                inputId.value = '".$_POST['id_gioco_modifica']."';
                                form.appendChild(inputId);

                                var inputElimina = document.createElement('input');
                                inputElimina.type = 'hidden';
                                inputElimina.name = 'eliminaGioco';
                                inputElimina.value = 'true';
                                form.appendChild(inputElimina);

                                document.body.appendChild(form);
                                form.submit();
                            }
                        }
                    </script>
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
                                        echo "<br/> <input type='checkbox' name='id_correlati_eliminati[]' value='".$id->textContent."'> ID: ".htmlspecialchars($id->textContent)." - Titolo: ".htmlspecialchars($titolo)."<br />";
                                    }
                                }
                            }
                            
                    
                        }
                        echo "<br/><input type=\"submit\" name=\"rimuoviCorrelati\" value=\"Rimuovi Correlati\"></p>";
                    echo "</form>";
                }
                ?>
  
                  <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInModificaGioco()"><img src="Stile/Icone/iconafreccia.png" alt="modificagiocobutton" ></button>
                    </div>
                </div>


            </div>

            <div class="cardSettings hideCard" id="card4">
                <h2>Ricerca gioco Sconti</h2>
                <!-- Contenuto per la gestione sconti -->
                <div class="buttons">
                        <div class="backarrow">
                            <button onclick="swapperInGestioneSconti()"><img src="Stile/Icone/iconafreccia.png" alt="modificagiocobutton" ></button>
                        </div>
                </div>
                <?php if($campiVuoti) echo "<h3>Almeno uno dei 2 campi deve essere inserito!</h3>"; ?>
                <form method="post" action="gestionePublisher.php">
                        <input type="hidden" name="publisher_name" value="<?php echo $_SESSION['userName']; ?>">
                        <label for="id_gioco_publisher">Inserisci l'ID del gioco di cui vuoi gestire gli sconti:</label>
                        <select name="id_gioco_publisher" id="id_gioco_publisher">
                        <?php  
                            $elemGiochi = xmlPointer("XML/Giochi.xml");
                            foreach($elemGiochi as $gioco){
                                 if($gioco->getElementsByTagName('Publisher')->item(0)->textContent == $_SESSION['userName']) {
                                    $idGiocoOption = $gioco->getAttribute('id_gioco');
                                    echo "<option value=\"$idGiocoOption\">".$gioco->getElementsByTagName('Titolo')->item(0)->textContent."</option>";
                                 }
                            }
                        ?>
                        </select>
                        <input type="submit" name="gestioneScontiPublisher" value="Gestisci gli sconti di questo Gioco">
                    </form>
            </div>

            <div class="cardSettings hideCard" id="card5">
                <div class="buttons">
                        <div class="backarrow">
                            <button onclick="swapperInAggiornamentoSconti()"><img src="Stile/Icone/iconafreccia.png" alt="modificagiocobutton" ></button>
                        </div>
                </div>
                <?php
                    if(isset($_POST['gestioneScontiPublisher']) && (isset($idGioco)) && (!empty($idGioco)) || (isset($_POST['aggiornaScontiPublisher']))){

                        echo "<h2>Gestione Sconti per il gioco: $nomeGioco (ID: $idGioco)</h2>";
                    
                        echo "<div class=\"scontiAttiviPublisher\">";
                        echo "<form method=\"post\" action=\"gestionePublisher.php\"><ul>";
                        for($i = 1; $i < 10; $i++){
                            $eraScontoAttivo = false;
                            if(count($listaSconti) > 0){
                                foreach($listaSconti as $rigaSconto){
                                
                                    if($i == (int)$rigaSconto['tipoSconto']){
                                        $eraScontoAttivo = true;
                                        $valoreSconto = (int)$rigaSconto['valoreSconto'];
                                        break;
                                    }
                                }
                                if($eraScontoAttivo){
                                    echo "<li><div class=\"switchAndScontoLabel\"><div class=\"switchLabel\"><div class=\"switch\"><input type=\"checkbox\" id=\"switchSconto$i\" name=\"sconto$i\" checked=\"checked\"/>";
                                    echo "<label for=\"switchSconto$i\"></label></div><div><p>".scontoTranslate($i)."</p></div></div>";
                                    echo "<div><select id=\"valoreSconto$i\" name=\"valoreSconto$i\">";
                                    for($j = 0; $j < 71; $j++){
                                        if($j == $valoreSconto) echo "<option value=\"$j\" selected>$j</option>";
                                        else echo "<option value=\"$j\">$j</option>";
                                        
                                    }
                                    echo "</select></div></div></li>";
                                }
                                else{
                                        echo "<li><div class=\"switchAndScontoLabel\">
                                                <div class=\"switchLabel\">
                                                    <div class=\"switch\"><input type=\"checkbox\" id=\"switchSconto$i\" name=\"sconto$i\"/>";
                                    echo "<label for=\"switchSconto$i\"></label></div><div><p>".scontoTranslate($i)."</p></div></div>";
                                    echo "<div><select id=\"valoreSconto$i\" name=\"valoreSconto$i\">";
                                    for($j = 0; $j < 71; $j++){
                                        echo "<option value=\"$j\">$j</option>";
                                    }
                                    echo "</select></div></li>";  
                                }
                            }
                            else{
                                echo "<li><div class=\"switchAndScontoLabel\">
                                    <div class=\"switchLabel\"><div class=\"switch\"><div><p>".scontoTranslate($i)."</p></div>
                                        <input type=\"checkbox\" id=\"switchSconto$i\" name=\"sconto$i\"/>";
                                echo "<label for=\"switchSconto$i\"></label></div></div>";
                                echo "<div><select id=\"valoreSconto$i\" name=\"valoreSconto$i\">";
                                for($j = 0; $j < 71; $j++){
                                    echo "<option value=\"$j\">$j</option>";
                                }
                                echo "</select></div></li>";  
                            }
                            
                            
                            
                            }
                        
                        echo "</ul>
                        <input type=\"hidden\" name=\"id_gioco_sconto\" value=\"$idGioco\">
                        <input type=\"submit\" name=\"aggiornaScontiPublisher\" value=\"Aggiorna Sconti\">
                            </form></div>";
                        
                    }
                ?>

            </div>
            <div class="cardSettings hideCard" id="card6">
                <div class="buttons">
                        <div class="backarrow">
                            <button onclick="swapperInAgency()"><img src="Stile/Icone/iconafreccia.png" alt="modificagiocobutton" ></button>
                        </div>
                </div>
            

                <div class="imageAndToggleAgency">
                    <h3>Nel caso si vuole impostare un descrizione del publisher: Profilo->Impostazioni</h3>
                    <form method="post" action="gestionePublisher.php" enctype="multipart/form-data">
                    <div class="switchLabel">
                        <div class="switch">
                            <?php
                                $elem = xmlPointer("XML/utenti.xml");
                                $toggleState = 'false';
                                foreach ($elem as $userNode) {
                                    if ($userNode->getAttribute('id_user') == $_SESSION['userId']) { 
                                        $toggleState = $userNode->getElementsByTagName('ToggleAgency')->item(0)->textContent;
                                    }
                                }
                            
                            ?>
                            <input type="checkbox" name="newModAgency" id="toggleAgency" <?php if($toggleState == 'true') echo "checked=\"checked\"";?>/>
                            <label for="toggleAgency"></label>
                        </div>
                        <div><p>Modalità Agency</p></div>
                    </div>

                    <div class="agencyImageBase">
                         <div class="file-upload-wrapper">
                            <label for="agencyImageUpload" class="btn-upload">
                                Carica logo
                            </label>
                            <input type="file" name="agencyImageUpload" id="agencyImageUpload" accept="image/*"/>
                        </div>
                    </div>
                    <div><input type="submit" id="buttonAgencySubmit" name="agencyToggleSubmit" value="Invia"/></div>
                    </form>
                </div>
            </div>
        </div>
        <div id="footer">
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2026 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>

