<?php
/* Questa pagina è l'hub gestionale per gli admin Qui si può trovare la gestione degli sconti per ogni gioco. Si possono gestire le info
degli utenti e i rimborsi ai vari giochi. Si può riposondere anche ai vari ticket. Ed alto.
 Abbiamo diviso ogni sezione in varie card e quasi ogni card ha la sua card apposita di ricerca per giochi o utenti in vase ad un ID. */
require 'serverUtility.php';
require_once 'baseScontiUtente.php'; 

$service = 0;
$utente = "";
$tipologiaSconti = [
    1=>"clienti che hanno speso N crediti finora", 
    2=>"clienti che hanno speso M crediti da una certa data", 
    3=>"clienti che hanno acquistato un gioco nella lista giochi consigliati dall'admin",
    4=>"clienti che hanno una certa reputazione", 
    5=>"clienti che sono con noi da X mesi",
    6=>"clienti che sono con noi da Y anni",
    7=>"il gioco appartiene ad una certa casa di sviluppo",
    8=>"il gioco appartiene ad un certo genere (magari tra quelli più apprezzati dal cliente)",
    9=>"sconto indipendente(esempio saldi invernali o festivi o altro)",
    10=>"sconto personalizzato assegnato direttamente dall'admin in base al grado"
];

session_start();
if($_SESSION['tipoUtente'] == '2'){
    //l'admin puo accedere a questa pagina
} else {
    //se non e admin lo reindirizzo alla homepage
    header("Location: Homepage.php");
    exit();
}
if(isset($_SESSION['userId'])){
    
    $utente = $_SESSION['userName'];
    $service = 1;
}

if($service == 0) header('Location: Homepage.php'); //reindirizzo alla homepage se non c'è una sessione attiva

if(isset($error)){
    echo "<script type='text/javascript'>alert('Campi inseriti scorrettmente');</script>";
}
// Funzione per modificare i parametri degli sconti
if (isset($_POST['modificaMinimiSpesi']) && !empty($_POST['minimiSpesi'])) {
    if(preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['minimiSpesi'])){
        $minimiSpesi = $_POST['minimiSpesi'];
        $doc = getDoc('XML/SettingsSconti.xml');
        $root = $doc->documentElement;
        $root->getElementsByTagName('MinimiSpesi')->item(0)->textContent = $minimiSpesi;
        $doc->save('XML/SettingsSconti.xml');
        header("Location: GestioneAdmin.php");
    }
    else{
        echo "<script type='text/javascript'>alert('Formato minimo speso non valido. Deve essere un numero decimale');</script>";
    }
    
}
if (isset($_POST['modificaValoreSconto']) && !empty($_POST['valoreSconto'])) {
    if(preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['valoreSconto'])){
        $valoreSconto = $_POST['valoreSconto'];
        $doc = getDoc('XML/SettingsSconti.xml');
        $root = $doc->documentElement;
        $root->getElementsByTagName('Valore')->item(0)->textContent = $valoreSconto;
        $doc->save('XML/SettingsSconti.xml');
        header("Location: GestioneAdmin.php");
    }
    else{
        echo "<script type='text/javascript'>alert('Formato valore sconto non valido. Deve essere un numero decimale');</script>";
    }
    
}
if (isset($_POST['modificaDataInizio']) && !empty($_POST['dataInizio'])) {
    $dataOdierna = new DateTime('today');
    $dataDaConfrontare = new DateTime($_POST['dataInizio']);
    if(preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['dataInizio']) && $dataDaConfrontare <= $dataOdierna){   
        $dataInizio = $_POST['dataInizio'];
        $doc = getDoc('XML/SettingsSconti.xml');
        $root = $doc->documentElement;
        $root->getElementsByTagName('DataInizio')->item(0)->textContent = $dataInizio;
        $doc->save('XML/SettingsSconti.xml');
        header("Location: GestioneAdmin.php");
    }
    else{
        $esitoDataInizio = !preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['dataInizio']);
        if($esitoDataInizio){
            echo "<script type='text/javascript'>alert('Formato data di inizio non valido. Deve essere nel formato GG-MM-AAAA');</script>";
        }
        else if($dataDaConfrontare > $dataOdierna){
            echo "<script type='text/javascript'>alert('La data di inizio non può essere futura');</script>";
        }
    }
}

if (isset($_POST['modificaReputazioneMin']) && !empty($_POST['reputazioneMin'])){
    if(preg_match('/^[0-9]+$/', $_POST['reputazioneMin'])){
        $reputazioneMin = $_POST['reputazioneMin'];
        $doc = getDoc('XML/SettingsSconti.xml');
        $root = $doc->documentElement;
        $root->getElementsByTagName('ReputazioneMin')->item(0)->textContent = $reputazioneMin;
        $doc->save('XML/SettingsSconti.xml');
        header("Location: GestioneAdmin.php");
        }
    else{
        echo "<script type='text/javascript'>alert('Formato reputazione minima non valido. Deve essere un numero intero');</script>";
    }
    
}

if (isset($_POST['modificaTempoIscrizione'])&& !empty($_POST['anniMin']&& !empty($_POST['mesiMin']))) {
    $esitoAnniDiIscr = false;
    $esitoMesiDiIscr = false;
    if(preg_match('/^[1-9]+$/', $_POST['anniMin'])){
        if(!empty($_POST['anniMin'])) $esitoAnniDiIscr = true;
    }
    if(preg_match('/^[1-9]+$/', $_POST['mesiMin'])){
        if(!empty($_POST['anniMin'])) $esitoMesiDiIscr = true;
    }
        
    if($esitoAnniDiIscr && $esitoMesiDiIscr){ 
        $anniMin = $_POST['anniMin'];
        $mesiMin = $_POST['mesiMin'];
        $doc = getDoc('XML/SettingsSconti.xml');
        $root = $doc->documentElement;
        $root->getElementsByTagName('AnniMin')->item(0)->textContent = $anniMin;
        $root->getElementsByTagName('MesiMin')->item(0)->textContent = $mesiMin;
        $doc->save('XML/SettingsSconti.xml');
        header("Location: GestioneAdmin.php");
    }
    else{
        if(!$esitoAnniDiIscr) echo "<script type='text/javascript'>alert('Attenzione: inserire un numero intero Maggiore di 0 per gli anni minimi');</script>";
        else if(!$esitoMesiDiIscr) echo "<script type='text/javascript'>alert('Attenzione: inserire un numero intero Maggiore di 0 per i mesi minimi');</script>";
        else if(!$esitoAnniDiIscr && !$esitoMesiDiIscr) echo "<script type='text/javascript'>alert('Attenzione: inserire un numero intero Maggiore di 0 per gli anni e i mesi minimi');</script>";
    }
    
}
if (isset($_POST['modificaCasaSconto']) && !empty($_POST['casaSconto'])) {
    $casaSconto = $_POST['casaSconto'];
    $doc = getDoc('XML/SettingsSconti.xml');
    $root = $doc->documentElement;
    $root->getElementsByTagName('CasaSconto')->item(0)->textContent = $casaSconto;
    $doc->save('XML/SettingsSconti.xml');
    header("Location: GestioneAdmin.php");
    
}
if (isset($_POST['modificaGenereSconto']) && !empty($_POST['genereSconto'])) {
    $genereSconto = $_POST['genereSconto'];
    $doc = getDoc('XML/SettingsSconti.xml');
    $root = $doc->documentElement;
    $root->getElementsByTagName('GenereSconto')->item(0)->textContent = $genereSconto;
    $doc->save('XML/SettingsSconti.xml');
    header("Location: GestioneAdmin.php");
    
}
if(isset($_POST['modificaGiochiAdmin'])){
    if(isset($_POST['giochiAdmin']) && !empty($_POST['giochiAdmin'])){
        $nodeRemoveSelector = [];
        $settingsSelector = getDoc('XML/SettingsSconti.xml');
        $rootSettings = $settingsSelector->documentElement;
        $gameListAdmin = $rootSettings->getElementsByTagName("listaGiochiAdmin")->item(0);
        foreach($gameListAdmin->childNodes as $gioco){
            if(in_array(trim($gioco->textContent), $_POST['giochiAdmin'])){
                array_push($nodeRemoveSelector, $gioco);
            }
        }
        foreach($nodeRemoveSelector as $node){
            $gameListAdmin->removeChild($node);
        }
        $settingsSelector->save('XML/SettingsSconti.xml');
    }

    if(isset($_POST['nuoviGiochiAdmin']) && !empty($_POST['nuoviGiochiAdmin'])){

        $listaGiochiEsitentiInCatalogo = [];
        $erroreGiocoInesistente = false;
        $settingsSelector = getDoc('XML/SettingsSconti.xml');
        $elemGicohiEsistenti = xmlPointer('XML/Giochi.xml');
        $rootSettings = $settingsSelector->documentElement;
        $gameListAdmin = $rootSettings->getElementsByTagName("listaGiochiAdmin")->item(0);
        $giochiEsistenti = [];
        foreach($elemGicohiEsistenti as $idgiocoNode){
            $listaGiochiEsitentiInCatalogo[] = $idgiocoNode->getAttribute('id_gioco');
        }
        foreach($gameListAdmin->getElementsByTagName("Gioco") as $giocoNode){
            $giochiEsistenti[] = trim($giocoNode->textContent);
        }
        $arrayNuoviGiochiAdmin = explode(',', $_POST['nuoviGiochiAdmin']);

        foreach($arrayNuoviGiochiAdmin as $gioco){
            if(!in_array(trim($gioco), $giochiEsistenti) && in_array(trim($gioco), $listaGiochiEsitentiInCatalogo)){
                $newGiocoNode = $settingsSelector->createElement("Gioco", trim($gioco));
                $gameListAdmin->appendChild($newGiocoNode);
            }
            else if(!in_array(trim($gioco), $listaGiochiEsitentiInCatalogo)){
               $erroreGiocoInesistente = true;
            }
        }

        $settingsSelector->save('XML/SettingsSconti.xml');

        if($erroreGiocoInesistente){
            echo "<script type='text/javascript'>alert('Attenzione: alcuni giochi inseriti non sono stati aggiunti alla lista dei giochi consigliati perchè non esistono nel catalogo');</script>";
        }
    }

    header("Location: GestioneAdmin.php");
}
if(isset($_POST['modificaScontoGrado'])){
    if(($_POST['scontoGrado4']< $_POST['scontoGrado5'] && $_POST['scontoGrado5'] < $_POST['scontoGrado6']) && $_POST['scontoGrado6'] <= 70){
        $scontoGrado4 = $_POST['scontoGrado4'];
        $scontoGrado5 = $_POST['scontoGrado5'];
        $scontoGrado6 = $_POST['scontoGrado6'];
        $doc = getDoc('XML/SettingsSconti.xml');
        $root = $doc->documentElement;

        $NodoGradoSconto = $root->getElementsByTagName('GradoSconto')->item(0);
        $tipi = $NodoGradoSconto->getElementsByTagName('TipoSconto');

        foreach($tipi as $tipo){
            $grado = $tipo->getAttribute('Grado');

            if($grado == '4'){
                $tipo->getElementsByTagName('Sconto')->item(0)->textContent = $scontoGrado4;
            }
            elseif($grado == '5'){
                $tipo->getElementsByTagName('Sconto')->item(0)->textContent = $scontoGrado5;
            }
            elseif($grado == '6'){
                $tipo->getElementsByTagName('Sconto')->item(0)->textContent = $scontoGrado6;
            }
        }

        $doc->save('XML/SettingsSconti.xml');
        header("Location: GestioneAdmin.php");
        exit;
    }
    else{
        echo "<script type='text/javascript'>alert('Formato sconto per grado non valido. Assicurarsi che: sconto grado 4 < sconto grado 5 < sconto grado 6 e che la somma dei tre sconti non superi il 70%');</script>";
    }
    
}
// Funzione per assegnare o rimuovere sconti agli utenti
if (isset($_POST['assegnaSconto']) && isset($_POST['id_user']) && !empty($_POST['sconto'])) {
    $id_user = $_POST['id_user'];
    $sconto = $_POST['sconto'];
    $doc = getDoc('XML/ScontiAssegnati.xml');
    $root = $doc->documentElement;
    $elem = $root->childNodes;
    foreach ($elem as $utenteNode) {
        if ($utenteNode->getAttribute('id_user') == $id_user) {
            if ($utenteNode->getElementsByTagName("scontiAssegnati")->length == 0) {
                $scontiAssegnatiNode = $doc->createElement("scontiAssegnati");
                $utenteNode->appendChild($scontiAssegnatiNode);
                }
                else if($utenteNode->getElementsByTagName("scontiAssegnati")->item(0)->getElementsByTagName("Sconto")->length > 0){
                    $scontiEsistenti = $utenteNode->getElementsByTagName("scontiAssegnati")->item(0)->getElementsByTagName("Sconto");
                    foreach($scontiEsistenti as $scontoEsistente){
                        if(trim($scontoEsistente->textContent) == $sconto){
                            header("Location: GestioneAdmin.php");
                            exit();
                        }
                    }
                }


            $newSconto = $doc->createElement("Sconto", htmlspecialchars($sconto));
            $utenteNode->getElementsByTagName("scontiAssegnati")->item(0)->appendChild($newSconto);

            $doc->save('XML/ScontiAssegnati.xml');
            break;
        }
    }
     

    header("Location: GestioneAdmin.php");
    exit();

}
if (isset($_POST['rimuoviSconto']) && isset($_POST['id_user']) && !empty($_POST['sconto'])) {

    $id_user = $_POST['id_user'];
    $sconto = $_POST['sconto'];

    $doc = getDoc('XML/ScontiAssegnati.xml');
    $root = $doc->documentElement;

    $utenti = $root->getElementsByTagName("Utente");

    foreach ($utenti as $utenteNode) {

        if ($utenteNode->getAttribute("id_user") == $id_user) {

            $scontiAssegnati = $utenteNode->getElementsByTagName("scontiAssegnati")->item(0);

            if ($scontiAssegnati) {

                $listaSconti = $scontiAssegnati->getElementsByTagName("Sconto");

                foreach ($listaSconti as $scontoNode) {

                    if (trim($scontoNode->textContent) == $sconto) {
                        $scontiAssegnati->removeChild($scontoNode);
                        break;
                    }
                }
            }

            $doc->save("XML/ScontiAssegnati.xml");
            break;
        }
    }

    header("Location: GestioneAdmin.php");
    exit();
}

if(isset($_POST['modificaValoreForza']) && !empty($_POST['valoreforza'])){
    if(preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['valoreforza'])){
        $valoreForza = $_POST['valoreforza'];
        $doc = getDoc('XML/SettingModCommenti.xml');
        $root = $doc->documentElement;
        $root->getElementsByTagName('Forza')->item(0)->textContent = $valoreForza;
        $doc->save('XML/SettingModCommenti.xml');
        header("Location: GestioneAdmin.php");
    }
    else{
        echo "<script type='text/javascript'>alert('Formato valore forza non valido. Deve essere un numero decimale');</script>";
    }
    
}

if(isset($_POST['modificaValoreRange']) && !empty($_POST['valoreRange'])){
    if(preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['valoreRange'])){
        $valoreRange = $_POST['valoreRange'];
        $doc = getDoc('XML/SettingModCommenti.xml');
        $root = $doc->documentElement;
        $root->getElementsByTagName('RangeMax')->item(0)->textContent = $valoreRange;
        $doc->save('XML/SettingModCommenti.xml');
        header("Location: GestioneAdmin.php");
    }
    else{
        echo "<script type='text/javascript'>alert('Formato valore range non valido. Deve essere un numero decimale');</script>";
    }
    
}

// Funzione per eliminare un gioco dal catalogo
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
    header("Location: GestioneAdmin.php");
}

// Funzione per sospendere o riattivare un gioco
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

    header("Location: GestioneAdmin.php");
}



// Funzione per modificare i dettagli di un gioco
if (isset($_POST['modificaGioco']) && !empty($_POST['id_da_modificare'])) {
    

    if(preg_match('/^([0-9]+(, *[0-9]+)*)?$/', $_POST['id_correlati']) &&
    preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['nuovo_prezzo']) &&
    preg_match('/^[0-9]+$/', $_POST['nuovaMediaAdmin']) &&
    preg_match('/^[0-9]{2}.[0-9]{2}.[0-9]{4}$/', $_POST['nuovaData'])){
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
                
                if(isset($_POST['nuova_descrizione'])) $gioco->getElementsByTagName("Descrizione")->item(0)->textContent = $_POST['nuova_descrizione'];

                if(isset($_POST['nuovaMediaAdmin'])) $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent = $_POST['nuovaMediaAdmin'];

                if(isset($_POST['nuovaData'])) $gioco->getElementsByTagName("DataDiUscita")->item(0)->textContent = $_POST['nuovaData'];

                if(isset($_POST['nuovoGenere'])) $gioco->getElementsByTagName("Genere")->item(0)->textContent = $_POST['nuovoGenere'];
                
                
                

                // Aggiorna i giochi correlati
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
        header("Location: GestioneAdmin.php");
    }
    else
        {
            $esitoCorrelati = !preg_match('/^([0-9]+(, *[0-9]+)*)?$/', $_POST['id_correlati']);
            $esitoPrezzo = !preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['nuovo_prezzo']);
            $esitoMediaAdmin = !preg_match('/^[0-9]+$/', $_POST['nuovaMediaAdmin']);
            $esitoData = !preg_match('/^[0-9]{2}.[0-9]{2}.[0-9]{4}$/', $_POST['nuovaData']);
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

// Funzione per rimuovere giochi correlati
if(isset($_POST['rimuoviCorrelati']) && !empty($_POST['id_correlati_eliminati']) && !empty($_POST['id_da_modificare'])){
    $id_gioco = $_POST['id_da_modificare'];
    $id_correlati_eliminati = $_POST['id_correlati_eliminati'];

    $xml = getDoc('XML/Giochi.xml');
    $root = $xml->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $gioco) {
        if ($gioco->getAttribute('id_gioco') == $id_gioco) {
            $correlatiNode = $gioco->getElementsByTagName("TitoliCorrelati")->item(0);
            foreach ($id_correlati_eliminati as $id_da_rimuovere) {
                $nodiDaRimuovere = [];
                foreach ($correlatiNode->getElementsByTagName("idGiocoCorrelato") as $correlato) {
                    if ($correlato->textContent == $id_da_rimuovere) {
                        array_push($nodiDaRimuovere, $correlato);
                    }
                }
                foreach ($nodiDaRimuovere as $nodo) {
                    $correlatiNode->removeChild($nodo);
                }
            }
        }
    }
    $xml->save('XML/Giochi.xml');
    header("Location: GestioneAdmin.php");
}


//
if(isset($_POST['modificaUtente']) && !empty($_POST['id_user_gestione'])) {
    $dataOdierna = new DateTime('today');
    $dataDaConfrontare = new DateTime($_POST['Data_di_Nascita']);
    

    if(preg_match('/^.*@.*$/',$_POST['Email']) && 
    preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['Password']) &&
    preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['Data_di_Nascita']) && 
    preg_match('/^[0-9]+$/', $_POST['Esperienza']) && 
    preg_match('/^[0-9]+$/', $_POST['Pixels']) && 
    preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['Saldo_attuale']) &&
    $dataDaConfrontare <= $dataOdierna
    ){

        $pointDB = new connectionDB();
        $mysqliConnection = $pointDB->connectDB();
        $id_utente = $_POST['id_user_gestione'];

        

        if (mysqli_connect_errno()) {
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

        if(isset($_POST['sospensione'])){
            $grado = 0;
        } else {
            $grado = $_POST['Grado'];
        }

        if(!isset($_POST['sospensione']) && $_POST['grado_precedente'] == '0'){
            $grado = 1;
        }

        $sql = "SELECT * FROM {$pointDB->getTableUsers()} WHERE ID='$id_utente';";
        if(mysqli_query($mysqliConnection, $sql)){
            $resultQ = mysqli_query($mysqliConnection, $sql);
            $num = mysqli_num_rows($resultQ);
            if($num == 1){
                $row=mysqli_fetch_array($resultQ);
                $tipoUtente = $row['Tipologia_utente'];
            }
        } else {
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

        
        $sql2 = "";

        $sql1 = "
            UPDATE {$pointDB->getTableUsers()}
            SET Email = '".$_POST['Email']."' ,
            Password = '".$_POST['Password']."',
            Username = '".$_POST['Username']."',
            Esperienza = '".$_POST['Esperienza']."',
            Grado = '".$grado."',
            Pixels = '".$_POST['Pixels']."',
            Saldo_attuale = '".$_POST['Saldo_attuale']."',
            Data_di_Nascita = '".$_POST['Data_di_Nascita']."',
            Nome = '".$_POST['Nome']."',
            Cognome = '".$_POST['Cognome']."',
            Tipologia_utente = '".$_POST['Tipologia_utente']."',
            imgProfiloPath = '".$_POST['imgProfiloPath']."'";
            
    

        // Esecuzione query
        if (mysqli_query($mysqliConnection, $sql)) {

        } 
        else{
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }
        $baseScontiUtente = new ScontiUtente($id_utente);
            // I dati sono validi
        
        if($tipoUtente == '1') {
            $sql2 = ", PIVA = '".$_POST['PIVA']."'";
        }
        
        $sql3 = " WHERE ID = '$id_utente';";

        $sql = $sql1 . $sql2 . $sql3;
        if (mysqli_query($mysqliConnection, $sql)) {

        } 
        else{
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }
            

        $xml = getDoc('XML/utenti.xml');
        $root = $xml->documentElement;
        $elem = $root->getElementsByTagName("Utente");
        // var_dump($tipoUtente);
        // var_dump($_POST['Tipologia_utente']);
        if(($tipoUtente == '0' || $tipoUtente == '2') && $_POST['Tipologia_utente'] == '1'){
         
            foreach ($elem as $utente) {

                if ($utente->getAttribute('id_user') == $_POST['id_user_gestione']) {
                    $newToggleAgency = $xml->createElement("ToggleAgency", "false");
                    $newDescrizionePublisher = $xml->createElement("DescrizionePublisher", "");
                    break;
                }

            }

            if(isset($newToggleAgency) && isset($newDescrizionePublisher)){
                $utente->appendChild($newToggleAgency);
                $utente->appendChild($newDescrizionePublisher);
            }
            
            $xml->save('XML/utenti.xml');
            
        }
        
        
        else if($tipoUtente == '1' && ($_POST['Tipologia_utente'] == '0' || $_POST['Tipologia_utente'] == '2')){
           
            foreach ($elem as $utente) {

                if ($utente->getAttribute('id_user') == $_POST['id_user_gestione']) {
                    $nodoAgency = $utente->getElementsByTagName("ToggleAgency");
                    $nodoDescrizionePublisher = $utente->getElementsByTagName("DescrizionePublisher");
                    break;
                }
                
            
            }

            if(isset($nodoAgency) && isset($nodoDescrizionePublisher)){
                if($nodoDescrizionePublisher->length > 0) $utente->removeChild($nodoDescrizionePublisher->item(0));
                if($nodoAgency->length > 0) $utente->removeChild($nodoAgency->item(0));
            }

            $xml->save('XML/utenti.xml');
        
        }
    }
    
    



 
    else {

        $errorepasword = !preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['Password']);
        $erroreemail = !preg_match('/^.*@.*$/',$_POST['Email']);
        $erroreDataNascita = !preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['Data_di_Nascita']);
        $erroreExperienza = !preg_match('/^[0-9]+$/', $_POST['Esperienza']);
        $errorePixels = !preg_match('/^[0-9]+$/', $_POST['Pixels']);
        $erroreSaldo = !preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['Saldo_attuale']);
        if($errorepasword){
            echo "<script type='text/javascript'>alert('Formato password non valido. Deve contenere almeno 8 caratteri, una lettera maiuscola e un carattere speciale tra !@=&');</script>";
        }
        else if($erroreemail){
            echo "<script type='text/javascript'>alert('Formato email non valido. Deve contenere una @');</script>";
        }
        else if($erroreDataNascita){
            echo "<script type='text/javascript'>alert('Formato data di nascita non valido. Deve essere nel formato GG-MM-AAAA');</script>";
        }
        else if($erroreExperienza){
            echo "<script type='text/javascript'>alert('Formato esperienza non valido. Deve essere un numero intero');</script>";
        }
        else if($errorePixels){
            echo "<script type='text/javascript'>alert('Formato pixels non valido. Deve essere un numero intero');</script>";
        }
        else if($erroreSaldo){
            echo "<script type='text/javascript'>alert('Formato saldo non valido. Deve essere un numero decimale');</script>";
        }
        else if($dataDaConfrontare > $dataOdierna){
            echo "<script type='text/javascript'>alert('La data di nascita non può essere futura');</script>";
        }
    }

}

if(isset($_POST['modificaInfoExtraUtente']) && !empty($_POST['id_user_gestione'])){
    $dataOdierna = new DateTime('today');
    $dataDaConfrontare = new DateTime($_POST['DataIscrizione']);
    if(preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['DataIscrizione']) && $dataDaConfrontare <= $dataOdierna){

        $xml = getDoc('XML/utenti.xml');
        $root = $xml->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $utente) {

            if ($utente->getAttribute('id_user') == $_POST['id_user_gestione']) {

                $utente->getElementsByTagName("linkEsterno")->item(0)->textContent = $_POST['linkProfiloSocial'];
                $utente->getElementsByTagName("DataIscrizione")->item(0)->textContent = $_POST['DataIscrizione'];
                $utente->getElementsByTagName("Descrizione")->item(0)->textContent = $_POST['Descrizione'];
                $utente->getElementsByTagName("GenerePreferito")->item(0)->textContent = $_POST['GenerePreferito'];
                $utente->getElementsByTagName("CasaDiSviluppoPreferita")->item(0)->textContent = $_POST['CasaDiSviluppoPreferita'];
                if($_POST['tipologia_utente'] == '1'){
                    if(isset($_POST['toggleAgency'])) {
                        $utente->getElementsByTagName("ToggleAgency")->item(0)->textContent = "true";
                    } else {
                        $utente->getElementsByTagName("ToggleAgency")->item(0)->textContent = "false";
                    }
                    
                    $utente->getElementsByTagName("DescrizionePublisher")->item(0)->textContent = $_POST['DescrizionePublisher'];
                }
                
                break;
            }
        }

        $xml->save('XML/utenti.xml');
        $baseScontiUtente = new ScontiUtente($_POST['id_user_gestione']);
    }
    else{
        $esitoDataIscrizione = !preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['DataIscrizione']);
        if($esitoDataIscrizione){
            echo "<script type='text/javascript'>alert('Formato data di iscrizione non valido. Deve essere nel formato GG-MM-AAAA');</script>";
        }
        else if($dataDaConfrontare > $dataOdierna){
            echo "<script type='text/javascript'>alert('La data di iscrizione non può essere futura');</script>";
        }
    }
    
}

if(isset($_POST['rimuoviGiochiPosseduti']) && !empty($_POST['id_user_gestione'])){

    $xml = getDoc('XML/utenti.xml');
    $root = $xml->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $utente) {

        if ($utente->getAttribute('id_user') == $_POST['id_user_gestione']) {

            $listaGiochi = $utente->getElementsByTagName("listaGiochi")->item(0);

            if ($listaGiochi && isset($_POST['giochi_da_rimuovere'])) {

                $giochiPosseduti = $listaGiochi->getElementsByTagName("idGiocoPosseduto");

                for ($i = $giochiPosseduti->length - 1; $i >= 0; $i--) {

                    $gioco = $giochiPosseduti->item($i);

                    if (in_array(trim($gioco->textContent), $_POST['giochi_da_rimuovere'])) {

                        $listaGiochi->removeChild($gioco);

                    }
                }
            }
        }
    }

    $xml->save('XML/utenti.xml');
    $baseScontiUtente = new ScontiUtente($_POST['id_user_gestione']);
}



if(isset($_POST['aggiungiGiochiPosseduti']) && !empty($_POST['id_user_gestione'])){

    $jumperGiochiPos = false;
    $dataDiOggi = new DateTime('today');
    $dataPerConfronto = new DateTime($_POST['data_acquisto_assegnazione_nuovo_gioco']);
    $idCheckCond = true;
    $catalogoGiochi = [];
    $pointerAlCatalogo = xmlPointer('XML/Giochi.xml');

    foreach($pointerAlCatalogo as $gameInCatalogo){
        $catalogoGiochi = $gameInCatalogo->getAttribute('id_gioco');
    }

    if(!empty($_POST['id_assegnazione_nuovo_gioco']) && preg_match('/^[1-9]+$/', $_POST['id_assegnazione_nuovo_gioco'])){
        if(!(in_array($_POST['id_assegnazione_nuovo_gioco'], $catalogoGiochi))) $idCheckCond = false;

    }
    
    if((empty($_POST['id_assegnazione_nuovo_gioco'])) || 
    (empty($_POST['data_acquisto_assegnazione_nuovo_gioco'])) ||
    (empty($_POST['prezzo_assegnazione_nuovo_gioco']) )){
        $jumperGiochiPos = true;

        echo "<script type='text/javascript'>alert('Attenzione: per aggiungere un nuovo gioco bisogna completare TUTTI I CAMPI!');</script>";
         
    }
    
    if((preg_match('/^[0-9]+$/', $_POST['id_assegnazione_nuovo_gioco']) &&
        preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['data_acquisto_assegnazione_nuovo_gioco']) &&
        preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['prezzo_assegnazione_nuovo_gioco'])) &&
        !$jumperGiochiPos && ($dataPerConfronto <= $dataDiOggi && $idCheckCond)
        ){
        $xml = getDoc('XML/utenti.xml');
        $utenti = $xml->getElementsByTagName("Utente");

        foreach ($utenti as $utente) {

            if ($utente->getAttribute('id_user') == $_POST['id_user_gestione']) {

                $listaGiochi = $utente->getElementsByTagName("listaGiochi")->item(0);

                $giocoId = $_POST['id_assegnazione_nuovo_gioco'];
                $newGioco = $xml->createElement("idGiocoPosseduto", $giocoId);

                if(isset($_POST['data_acquisto_assegnazione_nuovo_gioco']) && !empty($_POST['data_acquisto_assegnazione_nuovo_gioco'])){
                    $newGioco->setAttribute("data_acquisizione", $_POST['data_acquisto_assegnazione_nuovo_gioco']);
                }else{
                    $newGioco->setAttribute("data_acquisizione", date("d-m-Y"));
                }

                if(isset($_POST['prezzo_assegnazione_nuovo_gioco']) && !empty($_POST['prezzo_assegnazione_nuovo_gioco'])){
                    $newGioco->setAttribute("spesa", $_POST['prezzo_assegnazione_nuovo_gioco']);
                }else{

                    $giochi = xmlPointer("XML/Giochi.xml");
                    $prezzoGioco = 0;

                    foreach ($giochi as $gioco) {
                        if ($gioco->getAttribute("id_gioco") == $giocoId) {
                            $prezzoGioco = $gioco->getElementsByTagName("Prezzo")->item(0)->textContent;
                            break;
                        }
                    }

                    $newGioco->setAttribute("spesa", $prezzoGioco);
                }

                $listaGiochi->appendChild($newGioco);

                break;
            }
        }

        $xml->save('XML/utenti.xml');
        $baseScontiUtente = new ScontiUtente($_POST['id_user_gestione']);
    }
    else{
        $esitoIdGioco = !preg_match('/^[1-9]+$/', $_POST['id_assegnazione_nuovo_gioco']);
        $esitoDataAcquisto = !preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['data_acquisto_assegnazione_nuovo_gioco']);
        $esitoPrezzo = !preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['prezzo_assegnazione_nuovo_gioco']);
        if($esitoIdGioco){
            echo "<script type='text/javascript'>alert('Formato ID gioco non valido. Deve essere un numero intero positivo');</script>";
        }
        else if($esitoDataAcquisto){
            echo "<script type='text/javascript'>alert('Formato data di acquisto non valido. Deve essere nel formato GG-MM-AAAA');</script>";
        }
        else if($esitoPrezzo){
            echo "<script type='text/javascript'>alert('Formato prezzo non valido. Deve essere un numero decimale');</script>";
        }
        else if($dataPerConfronto > $dataDiOggi){
            echo "<script type='text/javascript'>alert('La data di acquisto non può essere futura');</script>";
        }
        else if(!$idCheckCond) echo '<script type="text/javascript">alert("Il gioco che si sta cercando di assegnare non esiste nel catalogo");</script>';
    }
}


// Funzione per gestire i rimborsi
if(isset($_POST["richiediRimborso"])){

    $pointDB = new connectionDB();
    $mysqliConnection = $pointDB->connectDB();
    $id_utente = $_POST['id_user_rimborso'];


    if (mysqli_connect_errno()) {
        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }


    $sql ="SELECT * FROM {$pointDB->getTableUsers()} WHERE ID=\"".$_POST['id_user_rimborso']."\";";
    
    $resultQ = mysqli_query($mysqliConnection, $sql);
    $num = mysqli_num_rows($resultQ);
    if($num == 1){
        $row=mysqli_fetch_array($resultQ);
        
        $esperienzaDedotta= $row['Esperienza']-($_POST['pixels_da_togliere']*$_POST['modCommentiPrecedente']);

        $pixelsDedotti = $row['Pixels'] - $_POST['pixels_da_togliere'];
        
        $saldoDaAggiungere = $row['Saldo_attuale'] + $_POST['importo'];

        $gradoAttuale = $row['Grado'];

        switch($gradoAttuale > 0){
            case $gradoAttuale == 1:
                $capEsperienza = 0;
                break;
            case $gradoAttuale == 2:
                $capEsperienza = 500;
                break;
            case $gradoAttuale == 3:
                $capEsperienza = 1000;
                break;
            case $gradoAttuale == 4:
                $capEsperienza = 3000;
                break;
            case $gradoAttuale == 5:
                $capEsperienza = 4000;
                break;
            default:
                $capEsperienza = 0;
                break;
        }

        if($esperienzaDedotta < $capEsperienza && $gradoAttuale > 0){
            $gradoDaAssegnare = $gradoAttuale - 1;
            $sql ="UPDATE {$pointDB->getTableUsers()} SET Esperienza=$esperienzaDedotta, Pixels=$pixelsDedotti, Saldo_attuale=$saldoDaAggiungere, Grado=$gradoDaAssegnare WHERE ID=\"".$_POST['id_user_rimborso']."\";";
        
        }
        else $sql ="UPDATE {$pointDB->getTableUsers()} SET Esperienza=$esperienzaDedotta, Pixels=$pixelsDedotti, Saldo_attuale=$saldoDaAggiungere WHERE ID=\"".$_POST['id_user_rimborso']."\";";
        
        if(mysqli_query($mysqliConnection, $sql)){
            $docUtente = getDoc("XML/utenti.xml");
            $docLog = getDoc("XML/LogTransazioniGiochi.xml");

            $rootUtente = $docUtente->documentElement;
            $elemUtente = $rootUtente->childNodes;

            foreach($elemUtente as $utente){
            if($utente->getAttribute('id_user') == $_POST['id_user_rimborso']){

                $listaGiochi = $utente->getElementsByTagName("listaGiochi")->item(0);

                if($listaGiochi != null){

                    $giochiPosseduti = $listaGiochi->getElementsByTagName("idGiocoPosseduto");

                    foreach($giochiPosseduti as $gioco){

                        if(trim($gioco->textContent) == $_POST['id_gioco_rimborso']){
                            $listaGiochi->removeChild($gioco);
                            
                        }
                    }
                }
                
                }
                

            
            }
            $docUtente->save('XML/utenti.xml');

            $rootLog = $docLog->documentElement;
            $elemLog = $rootLog->childNodes;

            foreach($elemLog as $log){
                if($log->getAttribute('IDTransazione') == $_POST['id_transazione_rimborso'] && $log->getAttribute('IDGiocatore') == $_POST['id_user_rimborso']){
                    $listaGiochi = $log->getElementsByTagName('Gioco');
                    foreach($listaGiochi as $gioco){
                        if($gioco->getElementsByTagName('IDGioco')->item(0)->textContent == $_POST['id_gioco_rimborso']){
                            $gioco->parentNode->removeChild($gioco);
                            break;
                        }
                    }
                    if(!($log->hasChildNodes())){
                        $log->parentNode->removeChild($log);
                    }
                }
            }
            $docLog->save('XML/LogTransazioniGiochi.xml');
            header('Location: GestioneAdmin.php');
        } 
        else {
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }
    }
    
    
}


// Funzione per gestire le segnalazioni dei commenti e delle recensioni
if(isset($_POST['formSegnalazioniCommenti'])){
    $docCommenti = getDoc("XML/Commenti.xml");
    $root = $docCommenti->documentElement;
    $elem =$root->childNodes;
    // echo "<script type='text/javascript'>alert('ciaoh');</script>";

    foreach($elem as $idGiocoCom){
        if($idGiocoCom->getAttribute("id_gioco")==$_POST["idGiocoSegnalato"]){
            $Commenti= $idGiocoCom->childNodes;
            foreach($Commenti as $CommentoDaEliminare){
                if($CommentoDaEliminare->getAttribute("id_commento")==$_POST['idCommentoSegnalato']){
                    $CommentoDaEliminare->parentNode->removeChild($CommentoDaEliminare);

                }
            }
        }
        
    }$docCommenti->save("XML/Commenti.xml");
    $docLike = getDoc("XML/LikeCommenti.xml");
    $root = $docLike->documentElement;
    $elem =$root->childNodes;
    foreach($elem as $ref){
        if($ref->getElementsByTagName('Id_Gioco')->item(0)->textContent==$_POST["idGiocoSegnalato"] && $ref->getElementsByTagName('Id_Commento')->item(0)->textContent==$_POST['idCommentoSegnalato']){
            $root->removeChild($ref);
        }
    }
    $docLike->save('XML/LikeCommenti.xml');


}

if(isset($_POST['eliminaSegCom'])){
    $docSegnalazioni = getDoc("XML/Commenti.xml");
    $root = $docSegnalazioni->documentElement;
    $elem =$root->childNodes;

foreach($elem as $idGiocoCom){
        if($idGiocoCom->getAttribute("id_gioco")==$_POST["idGiocoSegnalato"]){
            $Commenti= $idGiocoCom->childNodes;
            foreach($Commenti as $CommentoDaEliminare){
                if($CommentoDaEliminare->getAttribute("id_commento")==$_POST['idCommentoSegnalato']){
                    $CommentoDaEliminare->setAttribute("segnalazioni", "0");
                }
                }
            }
        }
    $docSegnalazioni->save('XML/Commenti.xml');
}

if(isset($_POST['formSegnalazioniRecensioni'])){
        

    $docRecensioni = getDoc("XML/Recensioni.xml");
    $root = $docRecensioni->documentElement;
    $elem =$root->childNodes;

    foreach($elem as $idGiocoRec){
        if($idGiocoRec->getAttribute("id_gioco")==$_POST["idGiocoSegnalato"]){
            $Recensioni= $idGiocoRec->childNodes;
            foreach($Recensioni as $RecensioneDaEliminare){
                if($RecensioneDaEliminare->getAttribute("id_recensione")==$_POST['idRecensioneSegnalato']){
                    $RecensioneDaEliminare->parentNode->removeChild($RecensioneDaEliminare);
                }
            }
        }
       
    } 

    $docRecensioni->save("XML/Recensioni.xml");
    $docLike = getDoc("XML/LikeRecensioni.xml");
    $root = $docLike->documentElement;
    $elem =$root->childNodes;
    foreach($elem as $ref){
        if($ref->getElementsByTagName('Id_Gioco')->item(0)->textContent==$_POST["idGiocoSegnalato"] && $ref->getElementsByTagName('Id_Recensione')->item(0)->textContent==$_POST['idRecensioneSegnalato']){
            $root->removeChild($ref);
        }
    }
    $docLike->save('XML/LikeRecensioni.xml');


}

if(isset($_POST['eliminaSegRec'])){
    $docSegnalazioni = getDoc("XML/Recensioni.xml");
    $root = $docSegnalazioni->documentElement;
    $elem =$root->childNodes;

    foreach($elem as $segnalazione){
        if($segnalazione->getAttribute("id_gioco")==$_POST["idGiocoSegnalato"]){
            $Recensioni= $segnalazione->childNodes;
            foreach($Recensioni as $RecensioneDaEliminare){
                if($RecensioneDaEliminare->getAttribute("id_recensione")==$_POST['idRecensioneSegnalato']){
                    $RecensioneDaEliminare->setAttribute("segnalazioni", "0");
                }
            }
        }
    }
    $docSegnalazioni->save('XML/Recensioni.xml');
}


if(isset($_POST['cercaUtenteScontoBlacklist'])){
    if(isset($erroreRicercaScontoBlacklist)) unset($erroreRicercaScontoBlacklist);
    $id_user = $_POST['id_user_sconti_blacklist'];
    $utentiBlacklist = xmlPointer("XML/utenti.xml");
    $trovato = false;
    foreach ($utentiBlacklist as $utenteNode) {
        if ($utenteNode->getAttribute('id_user') == $id_user) {
            $trovato = true;
            break;
        }
    }
    if (!$trovato) {
        $erroreRicercaScontoBlacklist = "Utente non trovato";
    }
}

if(isset($_POST['impostaBlacklistSconti']) && !empty($_POST['id_user_sconti_blacklist'])){
    
    $doc = getDoc('XML/BlacklistSconti.xml');
    $docScontiAssegnati = getDoc('XML/ScontiAssegnati.xml');
    $root = $doc->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $utenteNode) {
        if ($utenteNode->getAttribute('id_user') == $_POST['id_user_sconti_blacklist']) {
            $nodoUtenteBlacklist = $utenteNode;
        }
    }

    if(isset($nodoUtenteBlacklist)){
        if($nodoUtenteBlacklist->hasChildNodes()){
            
            $attributeID = $nodoUtenteBlacklist->getAttribute('id_user');
            $nodoUtenteBlacklist->parentNode->removeChild($nodoUtenteBlacklist);

            $newNodoUtenteBlacklist = $doc->createElement("Utente");
            $newNodoUtenteBlacklist->setAttribute("id_user", $attributeID);
            foreach($_POST['sconto'] as $s){
            $newSconto = $doc->createElement("idSconto", (int)$s);
            $newNodoUtenteBlacklist->appendChild($newSconto);
        }
            $root->appendChild($newNodoUtenteBlacklist);
        }
        else if(!isset($_POST['sconto'])){
            $nodoUtenteBlacklist->parentNode->removeChild($nodoUtenteBlacklist);
        }
        else{
            $nodoUtenteBlacklist->parentNode->removeChild($nodoUtenteBlacklist);
            $newNodoUtenteBlacklist = $doc->createElement("Utente");
            $newNodoUtenteBlacklist->setAttribute("id_user", $_POST['id_user_sconti_blacklist']);
            foreach($_POST['sconto'] as $s){
                $newSconto = $doc->createElement("idSconto", (int)$s);
                $newNodoUtenteBlacklist->appendChild($newSconto);
            }
            $root->appendChild($newNodoUtenteBlacklist);
        }
    }
    else{
        $newNodoUtenteBlacklist = $doc->createElement("Utente");
        $newNodoUtenteBlacklist->setAttribute("id_user", $_POST['id_user_sconti_blacklist']);
        foreach($_POST['sconto'] as $s){
            $newSconto = $doc->createElement("idSconto", (int)$s);
            $newNodoUtenteBlacklist->appendChild($newSconto);
        }
        $root->appendChild($newNodoUtenteBlacklist);
    }
    $doc->save('XML/BlacklistSconti.xml');


    if(isset($_POST['sconto'])){
        $scontiAssegnatiArray = [];
        $scannerScontiAssegnati = xmlPointer("XML/ScontiAssegnati.xml");
        $rootScontiAssegnati = $docScontiAssegnati->documentElement;
        $elemScontiAssegnati = $rootScontiAssegnati->childNodes;
        foreach($scannerScontiAssegnati as $utente){
            if($utente->getAttribute("id_user") == $_POST['id_user_sconti_blacklist']){
                $sconti = $utente->getElementsByTagName("scontiAssegnati")->item(0);
                foreach($sconti->childNodes as $sconto){
                    $idSconto = $sconto->textContent;
                    $scontiAssegnatiArray[] = trim($idSconto);
                }
            }
        }
        $scontiAssegnatiArray = array_diff($scontiAssegnatiArray, $_POST['sconto']);

        foreach($elemScontiAssegnati as $utente){
            if($utente->getAttribute("id_user") == $_POST['id_user_sconti_blacklist']){
                $nodoUtenteDaModificare = $utente;
                $nodoScontiAssegnati = $utente->getElementsByTagName("scontiAssegnati")->item(0);
                break;
            }
        }
        $nodoScontiAssegnati->parentNode->removeChild($nodoScontiAssegnati);

 
       
        $newListaSconti = $docScontiAssegnati->createElement("scontiAssegnati");
        foreach($scontiAssegnatiArray as $sconto){
            $newSconto = $docScontiAssegnati->createElement("Sconto", $sconto);
            $newListaSconti->appendChild($newSconto);
        }
        $nodoUtenteDaModificare->appendChild($newListaSconti);
        $docScontiAssegnati->save('XML/ScontiAssegnati.xml');
    }

    header("Location: GestioneAdmin.php");
        
}



?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Admin Board</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/GestioneAdmin.css?v=3" />
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" />
        <?php 
        /*Questo script serve per far cambiare le card presentate a ricaricamento in base ad errori o situazioni particolari*/
            if(isset($_POST['cercaGioco']) && !empty($_POST['id_gioco_modifica'])) echo "<script>sessionStorage.setItem(\"activeChange\", \"ricercaGioco\");</script>";
            else if (isset($_POST['cercaUtente']) && !empty($_POST['id_user_gestione'])) echo "<script>sessionStorage.setItem(\"activeChange\", \"gestioneUtente\");</script>";
            else if (isset($_POST['cercaUtenteRimborso']) && !empty($_POST['id_user_gestione']) && !isset($erroreRicercaRim)) echo "<script>sessionStorage.setItem(\"activeChange\", \"gestioneRimborso\");</script>";
            else if(isset($_POST['cercaUtenteScontoBlacklist']) && !empty($_POST['id_user_sconti_blacklist']) && !isset($erroreRicercaScontoBlacklist)) echo "<script>sessionStorage.setItem(\"activeChange\", \"gestioneScontiBlacklist\");</script>";
            else if(isset($_POST['cercaUtenteScontoBlacklist']) && isset($erroreRicercaScontoBlacklist)) echo "<script>sessionStorage.setItem(\"activeChange\", \"gestioneRicercaScontiBlacklist\");</script>";
            else echo "<script>sessionStorage.setItem(\"activeChange\", \"vuoto\");</script>";

            if(isset($erroreRicercaRim)) echo "<script>document.addEventListener(\"DOMContentLoaded\", function() {
   swapperInSearchUtenteRim()}</script>";
        ?> 
        
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <script type="text/javascript" src="Script/cardGestioneAdminChanger.js"></script>
        <script type="text/javascript" src="Script/scriptMail.js"></script>
        <script type="text/javascript" src="Script/SearchUsers.js?v=3"> </script>
        <script type="text/javascript" src="Script/SearchGameAdmin.js?v=3"> </script>

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

            <!--Card iniziale per la presentazione delle funzioni disponibili per l'admin  -->
            <div class="adminFunctions" id="card0">
                <!-- card 0 di menu -->

                <h1>Menu Funzioni admin</h1>
                
                <div class="buttons">
                    

                    <div class="sconti">
                    <p> - Vai alla pagina gestione sconti per gli utenti
                        <!-- accedi alla card 12 nascondi la card 0 -->
                        <button onclick="swapperInSearchUtentiSco()">  
                            <img src="Stile/Icone/scontoicon.png" alt="sconticonbutton" > 
                        </button>
                    </p>
                    </div>


                    <div class="modifica">
                        <p>- Modifica un gioco presente - >
                        <!-- accedi alla card 4 nascondi la card 0 -->
                        <button onclick="swapperInRicerca()">
                             <img src="Stile/Icone/modificaicon.png" alt="ricercabutton" >
                            </button>
                            </p>
                    </div>
                    <div class="sospendi">
                        <p>- Sospendi/Riattiva un gioco dal catalogo - >
                        <button onclick="swapperInSospensione()"> <img src="Stile/Icone/icona elenco.png" alt="sospendibutton" ></button></p>
                    </div>
                    <div class="tickets">
                        <p>- Gestisci i ticket degli utenti - >
                        <!-- accedi alla card 1 nascondi la card 0 -->
                        <button onclick="swapperInTickets()"> <img src="Stile/Icone/ticketicon.png" alt="ticketbutton" >
                        </button>  
                        </p>
                    </div>
                    <div class="gestioneUtenti">
                        <p>- Gestisci gli utenti iscritti - >
                            <!-- accedi alla card 2 nascondi la card 0 -->
                            <button onclick="swapperSearchUtente()"> <img src="Stile/Icone/utentiicon.png"
                            alt="gestioneutentibutton">
                            </button>
                        </p>
                    </div>
                    <div class="gestioneRimborsi">
                        <p>- Gestisci i rimborsi - >
                            <button onclick="swapperInSearchUtenteRim()"> <img src="Stile/Icone/rimborsiicon.png" alt="rimborsibutton" >
                        </button>  
                        </p>
                    </div>
                    <div class="gestioneSegnalazioni">
                        <p>- Gestisci Le segnalazioni - >
                            <button onclick="swapperInGestioneSegnalazioni()"> <img src="Stile/Icone/segnalaicon.png" alt="Segnalzionibutton" >
                        </button>  
                        </p>
                    </div>
                    <div class="settings">
                        <p> - Modifica le regole per l'assegnazione degli sconti <br>
                        e imposta i parametri del modificatore commenti - >
                            <!-- accedi alla card 11 nascondi la card 0 -->
                            <button onclick="swapperInSettingsSconti()">   <img src="Stile/Icone/settingsicon.png" alt="settingsbutton" > 
                            </button>
                        </p>
                    </div>
                </div>
            </div>
            <!-- Card per la gestione dei Ticket -->
            <div class="cardSettings hideCard" id="card1">

                <!-- Card di gestione ticket  -->
                <h1>Gestione Ticket Utenti</h1>

                

                <table id="TabellaTicket">
                    <tr>
                      
                        <th id="ColTicket">
                            Num.Ticket &
                            Num.Utente
                        </th>          
                    

                        
                        <th id="ColTesto">
                            Testo Ticket
                        </th> 

                        <th id="ColInv">
                            Risposta
                        </th>

                        <th>Invia</th>
                        
                    </tr>
                    <!-- Ciclo PHP per l'estrazione delle domande e risposte dal file XML -->
                     
                    <?php
                        $elem = xmlPointer('XML/Ticket.xml'); //Richiama la funzione che restituisce il puntatore ai nodi figli della root del file XML
                
                        //Ciclo per l'estrazione delle domande e risposte
                        foreach($elem as $tickets){
                        $idTicket = $tickets->getAttribute('id_ticket');
                        $idUtenteDestinatario = $tickets->getAttribute('id_utente');
                        $testo = $tickets->getElementsByTagName("text")->item(0)->textContent;


                            echo " <tr id=\"ticketN$idTicket\">
                            <td>Ticket N.$idTicket</br> Utente ID: $idUtenteDestinatario</td>
                            <td>$testo</td>
                            <td><textarea name='RispostaTicket' id='RispostaTicketN$idTicket'> </textarea> </td>
                            <td><button  type=\"button\" name='Invia Mail' onclick='mailSender($idTicket, $idUtenteDestinatario)'>Invia Mail</button></td>
                            
                            
                            </tr>";

                        }  
                    ?>

                </table>
                 <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInTickets()">  <img src="Stile/Icone/iconafreccia.png" alt="sospendigiocobutton" ></button>
                    </div>
                </div>
            
            
            </div>

            <div class="cardSettings hideCard" id="card2">
                <?php
                if(isset($_POST['id_user_sconti_blacklist']) && !empty($_POST['id_user_sconti_blacklist']) && !isset($erroreRicercaScontoBlacklist)){
                    $scontiBlacklistati = [];
                    $utentiBlacklistati = xmlPointer("XML/BlacklistSconti.xml");
                    foreach($utentiBlacklistati as $utente){
                        foreach($utente->childNodes as $sconto){
                            if($utente->getAttribute('id_user') == $_POST['id_user_sconti_blacklist']){
                                array_push($scontiBlacklistati, $sconto->textContent);
                            }
                        }
                    }
                    $pointDB = new connectionDB();
                    $mysqliConnection = $pointDB->connectDB();

                    if (mysqli_connect_errno()){
                        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                    }
            

                    $queryLogin = "SELECT * FROM {$pointDB->getTableUsers()} WHERE ID = '".$_POST['id_user_sconti_blacklist']."'";
                    $resultQ = mysqli_query($mysqliConnection, $queryLogin);
                    $num = mysqli_num_rows($resultQ);

                // Se il numero di righe restituite dalla query è 1 allora l'utente esiste e puo essere loggato
                if($num == 1){
                    $flag=1;
                    
                    $row=mysqli_fetch_array($resultQ);
                    

                    echo "<h3>Imposta quali sconti l'utente ".$row['Username']." di ID: ".$row['ID']." non puo usare:</h3>";
                    echo "<form method=\"post\" action=\"GestioneAdmin.php\">";
                    echo"<ul>";
                    foreach($tipologiaSconti as $key => $value){
                        if(in_array($key, $scontiBlacklistati)){
                            echo "<li><div class=\"switchAndScontoLabel\"><div class=\"switchLabel\"><div class=\"switch\"><input type=\"checkbox\" id=\"switchSconto$key\" name=\"sconto[]\" value=\"$key\" checked=\"checked\"/>";
                            echo "<label for=\"switchSconto$key\"></label></div><div><p>".$value."</p></div></div></div></li>";
                        }
                        else{
                            echo "<li><div class=\"switchAndScontoLabel\"><div class=\"switchLabel\"><div class=\"switch\"><input type=\"checkbox\" id=\"switchSconto$key\" name=\"sconto[]\" value=\"$key\"/>";
                            echo "<label for=\"switchSconto$key\"></label></div><div><p>".$value."</p></div></div></div></li>";
                        }
                        
                        
                    }
                    echo "</ul>";
                    
                        echo "<input type=\"hidden\" name=\"id_user_sconti_blacklist\" value=\"".$_POST['id_user_sconti_blacklist']."\">";
                    
                    echo "<input type=\"submit\" name=\"impostaBlacklistSconti\" value=\"Imposta Sconti\">";
                    echo "</form>";
                }
                }

                ?>
             <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInSettings()"><img src="Stile/Icone/iconafreccia.png" alt="ricercagiocobutton" ></button>
                    </div>
                </div>


            

            </div>

            <!-- Funzione admin:rimborso  -->
            <div class="cardSettings hideCard" id="card4">

                         
                <?php  
                echo "<h1>Cerca Gioco</h1>";
                echo "<form method=\"post\" action=\"GestioneAdmin.php\">
                    <!-- Aggiungi qui i campi per modificare il gioco -->
                    <label for=\"id_gioco_modifica\">ID Gioco da modificare:</label>
                    <div class=\"ricercaGiochi\">
                    <div><input type='text' id=\"id_gioco_modifica\" name=\"id_gioco_modifica\"/></div>
                    <div id=\"ContainerliveSearchGiochi\">
                        <div><label for='liveSearchInputGiochi'>Cerca gioco per nome: </label></br><input type='text' id='liveSearchInputGiochi' onkeyup='mostraRisultatiGiochi(this.value)'></div>
                        <div class=\"liveSearchGiochi\"></div>
                    </div>
                    </div>
                    <input type=\"submit\" name=\"cercaGioco\" value=\"Ricerca\">
                    
                </form>";
                ?>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInRicerca()"><img src="Stile/Icone/iconafreccia.png" alt="ricercagiocobutton" ></button>
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
                        $trovato = false;

                        foreach($elemGiochi as $gioco){

                            if($gioco->getAttribute('id_gioco') == $_POST['id_gioco_modifica']){

                                $trovato = true;                         

                                $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
                                $prezzo = $gioco->getElementsByTagName("Prezzo")->item(0)->textContent;
                                $publisher = $gioco->getElementsByTagName("Publisher")->item(0)->textContent;
                                $casaSviluppo = $gioco->getElementsByTagName("CasaSviluppo")->item(0)->textContent;
                                $dataUscita = $gioco->getElementsByTagName("DataDiUscita")->item(0)->textContent;
                                $Genere = $gioco->getElementsByTagName("Genere")->item(0)->textContent;
                                $descrizione = $gioco->getElementsByTagName("Descrizione")->item(0)->textContent;
                                $mediaAdmin = $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent;
                              
                                $requisitiMin = $gioco->getElementsByTagName("RequisitiMinimi")->item(0)->textContent;
                                $requisitiRac = $gioco->getElementsByTagName("RequisitiRaccomandati")->item(0)->textContent;

                                $ids = [];

                                $correlatiNode = $gioco->getElementsByTagName("idGiocoCorrelato");
                                foreach ($correlatiNode as $nodo) {
                                    $ids[] = $nodo->nodeValue;
                                }


                                break;

                            }
                        }
                        if(!$trovato){
                            echo"<h1>Non trovato!</h1>";
                        }
                        else{
                            echo "<h2>Modifica i dettagli del gioco: $titolo</h2>";
                        echo "<form method='post' action='GestioneAdmin.php'>
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
                        <label for=\"MediaRecensioniAdmin\" > Nuova media recensioni admin :</label>
                        <input type=\"text\" id=\"MediaRecensioniAdmin\" name=\"nuovaMediaAdmin\" value=\"$mediaAdmin\" ></br>
                    </p>

                    <p>
                        <label for=\"DataUscita\"> Nuovo data di uscita :</label>
                        <input type=\"text\" id=\"DataUscita\" name=\"nuovaData\" value=\"$dataUscita\"></br>
                    </p>

                    <p>
                        <label for=\"nuovo_genere\"> Nuovo genere :</label>
                         <select name=\"nuovoGenere\" id=\"nuovo_genere\">
                                            <option value=\"{$setValoriGenere[0]}\" ";
                                            if($Genere == $setValoriGenere[0]) echo " selected";
                                            echo ">{$setValoriGenere[0]}</option>
                                            <option value=\"{$setValoriGenere[1]}\"";
                                            if($Genere == $setValoriGenere[1]) echo " selected";
                                            echo ">{$setValoriGenere[1]}</option> 
                                            <option value=\"{$setValoriGenere[2]}\" ";
                                            if($Genere == $setValoriGenere[2]) echo " selected";
                                            echo ">{$setValoriGenere[2]}</option>
                                            <option value=\"{$setValoriGenere[3]}\" ";
                                            if($Genere == $setValoriGenere[3]) echo " selected";
                                            echo ">{$setValoriGenere[3]}</option>
                                            <option value=\"{$setValoriGenere[4]}\" ";
                                            if($Genere == $setValoriGenere[4]) echo " selected";
                                            echo ">{$setValoriGenere[4]}</option>
                                            <option value=\"{$setValoriGenere[5]}\" ";
                                            if($Genere == $setValoriGenere[5]) echo " selected";
                                            echo ">{$setValoriGenere[5]}</option>
                                            <option value=\"{$setValoriGenere[6]}\" ";
                                            if($Genere == $setValoriGenere[6]) echo " selected";
                                            echo ">{$setValoriGenere[6]}</option>
                                            <option value=\"{$setValoriGenere[7]}\" ";
                                            if($Genere == $setValoriGenere[7]) echo " selected";
                                            echo ">{$setValoriGenere[7]}</option>
                                            <option value=\"{$setValoriGenere[8]}\" ";
                                            if($Genere == $setValoriGenere[8]) echo " selected";
                                            echo ">{$setValoriGenere[8]}</option>
                                            <option value=\"{$setValoriGenere[9]}\" ";
                                            if($Genere == $setValoriGenere[9]) echo " selected";
                                            echo ">{$setValoriGenere[9]}</option>
                                        </select>  <br />
                    </p>

                    <p> 
                    <label for=\"elimina_gioco\"> Elimina gioco dallo store:</label>
                    <input type=\"button\" id=\"elimina_gioco\" name=\"elimina_gioco\" value=\"Elimina\" onclick=\"eliminaGioco()\"></p>

                    <script>
                        function eliminaGioco() {
                            if (confirm('Sei sicuro di voler eliminare questo gioco? Questa azione è irreversibile.')) {
                                // Se l'utente conferma, invia un form nascosto per eliminare il gioco
                                var form = document.createElement('form');
                                form.method = 'post';
                                form.action = 'gestioneAdmin.php';

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
                    
                    <p>
                        <label for=\"IdCorrelati\">Aggiungi ad ID Giochi Correlati (separati da virgola):</label>
                        <input type=\"text\" id=\"IdCorrelati\" name=\"id_correlati\" value=\"\"></br>
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
                                        echo "<br /> <input type='checkbox' name='id_correlati_eliminati[]' value='".htmlspecialchars($id->textContent)."'> ID: ".htmlspecialchars($id->textContent)." - Titolo: ".htmlspecialchars($titolo)."<br  />";
                                    }
                                }
                            }
                            
                    
                        }
                        echo "<br  /><input type=\"submit\" name=\"rimuoviCorrelati\" value=\"Rimuovi Correlati\"></p>";
                    echo "</form>";
                    

                        
                }
                        }
                
                            
                    ?>
                    
                  <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInModificaGioco()"><img src="Stile/Icone/iconafreccia.png" alt="modificagiocobutton" ></button>
                    </div>
                </div>
                
            </div>
            <div class="cardSettings hideCard" id="card5">
                <!-- Funzione admin:sospensione gioco -->
              <h3> Sospendi gioco dal catalogo </h3>
              <?php
              echo "<form method=\"post\" action=\"GestioneAdmin.php\">
                <label for=\"id_gioco_modifica\">ID Gioco da sospendere:</label>
                <div class=\"ricercaGiochi\">
                    <div><input type='text' id=\"id_gioco_modifica\" name=\"id_gioco_modifica\"/></div>
                    <div id=\"ContainerliveSearchGiochi\">
                        <div><label for='liveSearchInputGiochi'>Cerca gioco per nome: </label></br>
                        <input type='text' id='liveSearchInputGiochi'  onkeyup='mostraRisultatiGiochi(this.value)'></div>
                        <div class=\"liveSearchGiochi\"></div>
                    </div>
                    </div>
                <input type=\"submit\" name=\"sospendi\" value=\"Sospendi Gioco\">
            </form>";
            ?>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInSospensione()"><img src="Stile/Icone/iconafreccia.png" alt="sospendigiochobutton" ></button>
                    </div>
                </div>
            </div>

            <div class="cardSettings hideCard" id="card6">
                <!-- Card gestione utente -->
                <h1>Gestione Utente</h1>

                <?php 
                echo "<form method='post' action='GestioneAdmin.php'>
                    <label for='id_user_gestione'>ID Utente da gestire:</label>
                    <div class=\"ricercaUtente\">
                    <div><input type='text' id='id_user_gestione' name='id_user_gestione'/></div>
                    <div id=\"ContainerliveSearchUtenti\">
                        <div><label for='liveSearchInputUtenti'>Cerca utente per username: </label></br><input type='text' id='liveSearchInputUtenti' onkeyup='mostraRisultatiUtenti(this.value)'></div>
                        <div class=\"liveSearchUtenti\"></div>
                    </div>
                    </div>
                    <input type='submit' name='cercaUtente' value='Cerca Utente'>
                </form>";
                ?>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperSearchUtente()"><img src="Stile/Icone/iconafreccia.png" alt="usergestionbutton" ></button>
                    </div>
                </div>
            </div>
            
            <div class="cardSettings hideCard" id="card7">

            <?php 
            $pointDB = new connectionDB();
            $mysqliConnection = $pointDB->connectDB();

            if (mysqli_connect_errno()){
                printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
            }
            

                $queryLogin = "SELECT * FROM {$pointDB->getTableUsers()} WHERE ID = '".$_POST['id_user_gestione']."'";
                $resultQ = mysqli_query($mysqliConnection, $queryLogin);
                $num = mysqli_num_rows($resultQ);

                // Se il numero di righe restituite dalla query è 1 allora l'utente esiste e puo essere loggato
                if($num == 1){
                    $flag=1;
                    
                    $row=mysqli_fetch_array($resultQ);
                    
                    echo "<h1>Gestione utente: <br /> ".$row['Username']." ID: ".$row['ID']."</h1>";

                    
                    echo "<form method=\"post\" action=\"GestioneAdmin.php\" >
                    
                                
                                <label for=\"Email\" >Modifica Email:</label>
                                <input type=\"text\" id=\"Email\" name=\"Email\" value=\"".$row['Email']."\"><br />

                                <label for=\"Password\" >Modifica Password:</label>
                                <input type=\"text\" id=\"Password\" name=\"Password\" value=\"".$row['Password']."\" ><br />

                                <label for=\"Username\" >Modifica Username:</label>
                                <input type=\"text\" id=\"Username\" name=\"Username\" value=\"".$row['Username']."\" ><br />

                                <label for=\"Esperienza\" >Modifica Esperienza:</label>
                                <input type=\"text\" id=\"Esperienza\" name=\"Esperienza\" value=\"".$row['Esperienza']."\" ><br /> 

                                <label for=\"Grado\" >Modifica Grado:</label>   
                                <select id=\"Grado\" name=\"Grado\">
                                    <option value=\"1\" ".($row['Grado'] == '1' ? 'selected' : '').">Grado 1</option>
                                    <option value=\"2\" ".($row['Grado'] == '2' ? 'selected' : '').">Grado 2</option>
                                    <option value=\"3\" ".($row['Grado'] == '3' ? 'selected' : '').">Grado 3</option>
                                    <option value=\"4\" ".($row['Grado'] == '4' ? 'selected' : '').">Grado 4</option>
                                    <option value=\"5\" ".($row['Grado'] == '5' ? 'selected' : '').">Grado 5</option>
                                    <option value=\"6\" ".($row['Grado'] == '6' ? 'selected' : '').">Grado 6</option>
                                </select><br />
                                
                                <label for=\"Pixels\" >Modifica Pixels:</label>   
                                <input type=\"text\" id=\"Pixels\" name=\"Pixels\" value=\"".$row['Pixels']."\" ><br /> 

                                <label for=\"Nome\" >Modifica Nome:</label>   
                                <input type=\"text\" id=\"Nome\" name=\"Nome\" value=\"".$row['Nome']."\" ><br /> 

                                <label for=\"Cognome\" >Modifica Cognome:</label>   
                                <input type=\"text\" id=\"Cognome\" name=\"Cognome\" value=\"".$row['Cognome']."\" ><br /> 

                                <label for=\"Data_di_Nascita\">Modifica Data di nascita:</label>
                                <input type=\"text\" id=\"Data_di_Nascita\" name=\"Data_di_Nascita\" value=\"".$row['Data_di_Nascita']."\" ><br /> 

                                <label for=\"Saldo_attuale\" >Modifica saldo attuale :</label>   
                                <input type=\"text\" id=\"Saldo_attuale\" name=\"Saldo_attuale\" value=\"".$row['Saldo_attuale']."\" ><br /> 

                                <label for=\"Tipologia_utente\">Modifica Tipo Utente</label>
                                <select id=\"Tipologia_utente\" name=\"Tipologia_utente\">
                                    <option value=\"0\" ".($row['Tipologia_utente'] == '0' ? 'selected' : '').">Utente standard</option>
                                    <option value=\"1\" ".($row['Tipologia_utente'] == '1' ? 'selected' : '').">Publisher</option>
                                    <option value=\"2\" ".($row['Tipologia_utente'] == '2' ? 'selected' : '').">Admin</option>
                                </select><br />

                                <label for=\"ImmagineProfilo\"> Immagine profilo: </label>
                                <select name=\"imgProfiloPath\" id=\"ImmagineProfilo\">";

                                echo "<option value=\"ProfilePic/propicblank.png\">Nessuna</option>";
                                        
                                            $elem = xmlPointer("XML/utenti.xml");

                                            


                                            foreach($elem as $userNode){
                                                if($userNode->getAttribute('id_user') == $id_utente){
                                                    if($userNode->getElementsByTagName('listaPropic')->item(0) != null){
                                                        $pics= $userNode->getElementsByTagName('listaPropic')->item(0)->getElementsByTagName('idPropic');

                                                 
                                                        foreach($pics as $pic){

                                                            $imgs = xmlPointer("XML/ProfilePic.xml");
                                                            foreach($imgs as $img){ 
                                                                if($pic->textContent == $img->getAttribute('id_pic')) 
                                                                    echo "<option value=\"".$img->getElementsByTagName('path')->item(0)->textContent."\">".$img->getElementsByTagName('nome')->item(0)->textContent."</option>";
                                                            }
                                                        }
                                                    }
                                                    
                                                }
                                            }
                                        
                                 echo "</select><br />




                               ";
                               if($row['Tipologia_utente'] == 1){
                                echo "
                                <label for=\"PIVA\" >Modifica PIVA:</label>   
                                <input type=\"text\" id=\"PIVA\" name=\"PIVA\" value=\"".$row['PIVA']."\" ><br /> 
                                    ";
                                 }
                                 echo "
                                <label for=\"checkSopsensione\" >Sospendi utente?</label>  "; 
                                if($row['Grado'] == '0')
                                {
                                    echo "<input type=\"checkbox\" id=\"checkSopsensione\" name=\"sospensione\" checked=\"checked\"><br /> ";
                                }
                                else{
                                    echo "<input type=\"checkbox\" id=\"checkSopsensione\" name=\"sospensione\"><br /> ";
                                } 
                                




                                echo "
                                <input type=\"hidden\" name=\"grado_precedente\" value=\"".$row['Grado']."\">
                                <input type=\"hidden\" name=\"id_user_gestione\" value=\"".$row['ID']."\">
                                <input type=\"submit\" name=\"modificaUtente\" value=\"Applica modifiche\"></br>
                          </form>";
                } else {
                    echo "<h1>Utente non trovato</h1>";
                }

                echo"<h2> Altre informazioni: </h2>";
                $setValoriGenerePreferito = [
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
                $elem = xmlPointer("XML/utenti.xml");
                foreach ($elem as $utenteNode) {

                    if ($utenteNode->getAttribute('id_user') == $row['ID']) {

                        $id_utente = $utenteNode->getAttribute('id_user');
                        $generePreferitoUtente = $utenteNode->getElementsByTagName("GenerePreferito")->item(0)->textContent;

                        echo"<form method='post' action='GestioneAdmin.php'>
                        

                        <label for=\"DataIscrizione\"> Data di iscrizione: </label>
                        <input type=\"text\" id=\"DataIscrizione\" name=\"DataIscrizione\" value=\"".htmlspecialchars($utenteNode->getElementsByTagName("DataIscrizione")->item(0)->textContent)."\"><br />

                        <label for=\"CasaDiSviluppoPreferita\"> Casa di sviluppo preferita: </label>
                        <input type=\"text\" id=\"CasaDiSviluppoPreferita\" name=\"CasaDiSviluppoPreferita\" value=\"".htmlspecialchars($utenteNode->getElementsByTagName("CasaDiSviluppoPreferita")->item(0)->textContent)."\"><br />
                        
                        <label for=\"GenerePreferito\"> Genere preferito: </label>
                         <select name=\"GenerePreferito\" id=\"GenerePreferito\">
                                            <option value=\"{$setValoriGenerePreferito[0]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[0]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[0]}</option>
                                            <option value=\"{$setValoriGenerePreferito[1]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[1]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[1]}</option> 
                                            <option value=\"{$setValoriGenerePreferito[2]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[2]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[2]}</option>
                                            <option value=\"{$setValoriGenerePreferito[3]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[3]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[3]}</option>
                                            <option value=\"{$setValoriGenerePreferito[4]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[4]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[4]}</option>
                                            <option value=\"{$setValoriGenerePreferito[5]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[5]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[5]}</option>
                                            <option value=\"{$setValoriGenerePreferito[6]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[6]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[6]}</option>
                                            <option value=\"{$setValoriGenerePreferito[7]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[7]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[7]}</option>
                                            <option value=\"{$setValoriGenerePreferito[8]}\" ";
                                            if($generePreferitoUtente == $setValoriGenerePreferito[8]) echo " selected";
                                            echo ">{$setValoriGenerePreferito[8]}</option>
                                            <option value=\"{$setValoriGenerePreferito[9]}\">{$setValoriGenerePreferito[9]}</option>
                                        </select>  <br />
                        
                        <label for=\"Descrizione\"> Descrizione: </label>
                        <textarea id=\"Descrizione\" name=\"Descrizione\" >".htmlspecialchars($utenteNode->getElementsByTagName("Descrizione")->item(0)->textContent)."</textarea><br />
                       
                        <label for=\"linkProfiloSocial\"> Link profilo social: </label>"
                        . "<input type=\"text\" id=\"linkProfiloSocial\" name=\"linkProfiloSocial\" value=\"".htmlspecialchars($utenteNode->getElementsByTagName("linkEsterno")->item(0)->textContent)."\"><br />";

                        if($row['Tipologia_utente']  == "1"){

                        echo "<label for=\"DescrizionePublisher\"> Descrizione publisher: </label>
                        <textarea id=\"DescrizionePublisher\" name=\"DescrizionePublisher\" >".htmlspecialchars($utenteNode->getElementsByTagName("DescrizionePublisher")->item(0)->textContent)."</textarea><br />";

                            echo "<label for=\"toggleAgency\"> Utente è un'agenzia di vendita?: </label>";
                        if($utenteNode->getElementsByTagName("ToggleAgency")->item(0)->textContent == "true"){
                            echo "<input type=\"checkbox\"  id=\"toggleAgency\" name=\"toggleAgency\" checked=\"checked\"><br />";
                        }
                        else{
                            echo "<input type=\"checkbox\" id=\"toggleAgency\" name=\"toggleAgency\"><br />";
                        }
                        }

                        


                    echo "
                        <input type=\"hidden\" name=\"id_user_gestione\" value=\"".$row['ID']."\">
                        <input type=\"hidden\" name=\"tipologia_utente\" value=\"".$row['Tipologia_utente']."\">
                        <input type=\"submit\" name=\"modificaInfoExtraUtente\" value=\"Modifica info extra utente\"></br>
                            </form>";
                            break;

                    }
                }


                echo"<h2>Giochi acquistati:</h2>";

            
                echo"<p>Seleziona i giochi da rimuovere dalla lista dei giochi posseduti:</p>";

                $elem = xmlPointer("XML/utenti.xml");
                $elemGiochiCatalogo = xmlPointer("XML/Giochi.xml");
                $giochiInIdPosseduti = [];
                $elencoGiochiPresentazione = [];

                foreach ($elem as $utenteNode) {

                if ($utenteNode->getAttribute('id_user') == $row['ID']) {

                $id_utente = $utenteNode->getAttribute('id_user');

                    $lista = $utenteNode->getElementsByTagName("listaGiochi")->item(0);

                    echo "<form method='post' action='GestioneAdmin.php'>";

                    $giochi = $lista->getElementsByTagName("idGiocoPosseduto");
                    foreach ($giochi as $gioco) {
                        $giochiInIdPosseduti[] = $gioco->textContent;
                    }
                    foreach($elemGiochiCatalogo as $giocoCatalogo){
                        foreach($giochiInIdPosseduti as $idGiocoPossedutoInPosseduti){
                            if($giocoCatalogo->getAttribute('id_gioco') == $idGiocoPossedutoInPosseduti)
                            $elencoGiochiPresentazione[$idGiocoPossedutoInPosseduti]=$giocoCatalogo->getElementsByTagName('Titolo')->item(0)->textContent;
                        }
                    }

                    foreach($elencoGiochiPresentazione as $idGiocoDaPassare => $titoloGiocoDaPassare){
                        echo "<input type='checkbox' name='giochi_da_rimuovere[]' value='".htmlspecialchars($idGiocoDaPassare)."'> ID Gioco: ".htmlspecialchars($idGiocoDaPassare)." - Titolo: ".htmlspecialchars($titoloGiocoDaPassare)."<br>";
                    }

                    echo "<input type='hidden' name='id_user_gestione' value='$id_utente'>";
                    echo "<input type='submit' name='rimuoviGiochiPosseduti' value='Rimuovi giochi posseduti'><br><br>";
                    echo "</form>";

                    }
                }

                echo"<p> Inserisci i campi per aggiungere un gioco:</p>";
                echo "<form method='post' action='GestioneAdmin.php'>
                        <p><label for='id_gioco_posseduto'>Inserisci gli id del gioco da aggiungere:</label>
                        <input type='text' id='id_gioco_posseduto' name='id_assegnazione_nuovo_gioco' ></p>
                       <p> <label for='data_acquisto'>Inserisci la data di acquisto (formato DD-MM-AAAA):</label>
                        <input type='text' id='data_acquisto' name='data_acquisto_assegnazione_nuovo_gioco' ></p>
                        <p><label for='prezzo'>Inserisci il prezzo di acquisto:</label>
                        <input type='text' id='prezzo' name='prezzo_assegnazione_nuovo_gioco' > </p>
                        <input type='hidden' name='id_user_gestione' value='$id_utente'>
                        <input type='submit' name='aggiungiGiochiPosseduti' value='Aggiungi ai giochi posseduti'><br><br>
                      </form>";

                
                ?>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInUserManagement()"><img src="Stile/Icone/iconafreccia.png" alt="usergestionbutton" ></button>
                    </div>
                </div>
            </div>

            <div class="cardSettings hideCard" id="card8">
                <!-- Card gestione utente -->
                <h1>Gestione Rimborsi:Selezione Utente</h1>

                <?php 
                if(isset($erroreRicercaRim)) echo "<h2>".$erroreRicercaRim."</h2>";
                echo "<form method='post' action='GestioneAdmin.php'>
                    <label for='id_user_gestione'>ID Utente da gestire:</label>
                    <div class=\"ricercaUtente\">
                    <div><input type='text' id='id_user_gestione'  name='id_user_gestione'/></div>
                    <div id=\"ContainerliveSearchUtenti\">
                        <div><label for='liveSearchInputUtenti'>Cerca utente per username: </label></br><input type='text' id='liveSearchInputUtenti' onkeyup='mostraRisultatiUtenti(this.value)'></div>
                        <div class=\"liveSearchUtenti\"></div>
                    </div>
                    </div>
                    <input type='submit' name='cercaUtenteRimborso' value='Cerca Utente'>
                </form>";
                ?>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInSearchUtenteRim()"><img src="Stile/Icone/iconafreccia.png" alt="usergestionbutton" ></button>
                    </div>
                </div>
            </div>



            <div class="cardSettings hideCard" id="card9">
                <!-- Card gestione rimborsi -->
                <h1>Lista Rimborsi </h1>
                <?php 
                    if(isset($_POST["id_user_gestione"])){
                        $pointDB = new connectionDB();
                        $mysqliConnection = $pointDB->connectDB();
                        $id_utente = $_POST['id_user_gestione'];
                        // connectDB();

                        if (mysqli_connect_errno()) {
                            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                        }

                        $query="SELECT * FROM {$pointDB->getTableUsers()} WHERE ID=$id_utente";

                        $result = mysqli_query($mysqliConnection, $query);
                        if($result) $num = mysqli_num_rows($result);
                        

                        if ($num == 1) {
                            $trovatoRimborsi=false;
                            $row = mysqli_fetch_array($result);
                            $pixelAttuali = $row['Pixels'];
                            $elem = xmlPointer("XML/LogTransazioniGiochi.xml");
                            foreach($elem as $trans){
                                
                                if($_POST['id_user_gestione'] == $trans->getAttribute('IDGiocatore')){
                                    $trovatoRimborsi=true;
                                    echo "<table id=\"rimborsi\">";
                                    echo "<tr>
                                            <th>ID Transazione</th>
                                            <th>ID Giocatore</th>
                                            <th>Data e Ora</th>
                                            <th>Mod Utente</th>
                                            <th>Pixel Iniziali</th>
                                        </tr>";
                                    $idTransazione = $trans->getAttribute('IDTransazione');
                                    $idUser = $trans->getAttribute('IDGiocatore');
                                    $modCommenti = (float)$trans->getAttribute('ModCommentiUsato');
                                    echo "</br>";
                                    $pixels = $trans->getAttribute('PixelIniziali');
                                    echo "<tr id=\"transazione\">";
                                    echo '<td>'.$idTransazione.'</td>';
                                    echo '<td>'.$idUser.'</td>';
                                    echo '<td> '.$trans->getAttribute('DataOra').'</td>';
                                    echo '<td> '.(float)$modCommenti.' </td>';
                                    echo '<td>'.$pixels.'->'.$pixelAttuali.'</td>';

                                    $giochi = $trans->getElementsByTagName("Gioco");
                                    echo "</tr>
                                            <tr>
                                                <th>ID Gioco</th>
                                                <th id=\"colTitolo\">Titolo</th>
                                                <th>Importo(€)</th>
                                                <th>Pixel Guadagnati</th>
                                                <th>Annullare Acquisto?</th>
                                            </tr>";
                                    
                                    foreach($giochi as $gioco){
                                        echo "<tr id=\"giochiacquistati\">";
                                        echo "<td>".$gioco->getElementsByTagName("IDGioco")->item(0)->textContent."</td>";
                                        echo "<td>".$gioco->getElementsByTagName("Titolo")->item(0)->textContent."</td>";
                                        $importo = (float)$gioco->getElementsByTagName("Importo")->item(0)->textContent;   
                                        echo "<td>".$importo."€</td>";
                                        $pixelGuadagnati = $importo*5;
                                        echo"<td>$pixelGuadagnati</td>";
                                        echo "<td><form method='post' action='GestioneAdmin.php'>
                                                <input type='hidden' name='id_transazione_rimborso' value='".$idTransazione."'>
                                                <input type='hidden' name='id_gioco_rimborso' value='".$gioco->getElementsByTagName("IDGioco")->item(0)->textContent."'>
                                                <input type='hidden' name='id_user_rimborso' value='".$idUser."'>
                                                <input type='hidden' name='pixels_da_togliere' value='".$pixelGuadagnati."'>
                                                <input type='hidden' name='modCommentiPrecedente' value='".$modCommenti."'>
                                                <input type='hidden' name='importo' value='".$importo."'>
                                                <input type='submit' name='richiediRimborso' value='Annulla Acquisto'>
                                                </form></td>";
                                        echo "</tr>";      
                                    }
                                    
                                    echo "</table>";
                                }
                                
                            } 

                            if(!$trovatoRimborsi){
                                echo "<h2>Non sono presenti rimborsi per questo utente</h2>";
                            }

                        } 
                        else if ($num == 0) echo "<h2>Utente non trovato</h2>";


                        


                        
                
                    }
                
                ?>
        
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInGestioneRimborsi()"><img src="Stile/Icone/iconafreccia.png" alt="usergestionbutton" ></button>
                    </div>
                </div>
            </div>
            
            <div class="cardSettings hideCard" id="card10">
                 <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInGestioneSegnalazioni()"><img src="Stile/Icone/iconafreccia.png" alt="sospendigiocobutton" ></button>
                    </div>
                </div>
                
                <div id="segnalazioni"> 
                <div><h2>Segnalazioni commenti</h2></div>
                
                <?php
                $trovataSegnalazione = false;
                $isSegnalato = false;
                    $elem = xmlPointer("XML/Commenti.xml");
                    foreach($elem as $gioco){
                        if($gioco->getElementsByTagName("Commento")->length > 0){
                            $commenti = $gioco->childNodes;
                            foreach($commenti as $commento){
                                if($commento->getAttribute('segnalazioni') != '0'){
                                    $isSegnalato = true;
                                    $trovataSegnalazione = true;
                                }
                            }
                            if($isSegnalato){
                                echo"<p> ID Gioco: ".$gioco->getAttribute('id_gioco')."</p>";
                                $commenti = $gioco->childNodes;
                                echo "<div class=\"commentiSegnalati\">";
                                foreach($commenti as $commento){
                                    if($commento->getAttribute('segnalazioni') != '0'){
                                        echo "<div class=\"elemSegnalazione\"><p> ID Commento: ".$commento->getAttribute('id_commento')."</p></div>";
                                        echo "<div class=\"elemSegnalazione\"><p> ID Utente: ".$commento->getAttribute('id_utente')."</p></div>";
                                        echo "<div class=\"elemSegnalazione\"><p> Testo: ".$commento->getElementsByTagName('text')[0]->textContent."</p></div>";
                                        echo "<div class=\"elemSegnalazione\"><p> N.Segnalazioni: ".$commento->getAttribute('segnalazioni')."</p></div>";
                                        echo "<div class=\"elemSegnalazione\"> 
                                                <form method='post' action='GestioneAdmin.php'>
                                                <input type=\"hidden\" name=\"idCommentoSegnalato\" value=".$commento->getAttribute('id_commento').">
                                                <input type=\"hidden\" name=\"idGiocoSegnalato\" value=".$gioco->getAttribute('id_gioco').">
                                                <input type=\"submit\" name=\"formSegnalazioniCommenti\"value=\"Elimina Commento \" />
                                                <input type=\"submit\" name=\"eliminaSegCom\" value=\"Elimina Segnalazione\" />
                                                </form>
                                                </div>";
                                    }
                                }
                                echo "</div>";
                                $isSegnalato = false;
                            }
                            
                        }
                        
                    }
                                        if(!$trovataSegnalazione){
                                    echo"<h4>Nessuna segnalazione tra i commenti...Grueto!</h4>";
                                }

                ?>


                <div><h2>Segnalazioni Recensioni</h2></div>
                
                <?php
                    $trovataSegnalazione= false;
                    $elem = xmlPointer("XML/Recensioni.xml");
                    foreach($elem as $gioco){
                        $isSegnalato = false;
                        if($gioco->getElementsByTagName("Recensione")->length > 0){
                            $recensioni = $gioco->childNodes;
                            foreach($recensioni as $recensione){
                                if($recensione->getAttribute('segnalazioni') != '0'){
                                    $isSegnalato = true;
                                    $trovataSegnalazione =true;
                                }
                                
                            }
                            if($isSegnalato){
                                echo"<p> ID Gioco: ".$gioco->getAttribute('id_gioco')."</p>";
                                $recensioni = $gioco->childNodes;
                                echo "<div class=\"recensioniSegnalati\">";
                                foreach($recensioni as $recensione){
                                    if($recensione->getAttribute('segnalazioni') != '0'){
                                        echo "<div class=\"elemSegnalazione\"><p> ID Recensione: ".$recensione->getAttribute('id_recensione')."</p></div>";
                                        echo "<div class=\"elemSegnalazione\"><p> ID Utente: ".$recensione->getAttribute('id_utente')."</p></div>";
                                        echo "<div class=\"elemSegnalazione\"><p> Testo: ".$recensione->getElementsByTagName('text')[0]->textContent."</p></div>";
                                        echo "<div class=\"elemSegnalazione\"><p> N.Segnalazioni: ".$recensione->getAttribute('segnalazioni')."</p></div>";
                                        echo "<div class=\"elemSegnalazione\"> 
                                                <form method='post' action='GestioneAdmin.php'>
                                                <input type=\"hidden\" name=\"idRecensioneSegnalato\" value=".$recensione->getAttribute('id_recensione').">
                                                <input type=\"hidden\" name=\"idGiocoSegnalato\" value=".$gioco->getAttribute('id_gioco').">
                                                <input type=\"submit\" name=\"formSegnalazioniRecensioni\" value=\"Elimina Recensione\" />
                                                <input type=\"submit\" name=\"eliminaSegRec\" value=\"Elimina Segnalazione\" />
                                                </form>
                                                </div>";
                                    }
                                }
                                echo "</div>";
                                
                                
                            }
                            
                            
                            
                        }
                        
                    }
                    if(!$trovataSegnalazione){
                                    echo"<h4>Nessuna segnalazione tra le recensioni...Grueto!</h4>";
                                }

                ?>
                
                </div>
            </div>

            <!-- card di setting sconti -->
            <div class="cardSettings hideCard" id="card11">
                <h1> Settings Sconti</h1>

                <?php 

                    $listaGiochiAdmin = [];

                    $setValoriGenerePreferitoSettingsSconti = [
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

                    $doc = new DOMDocument();
                    $doc->load("XML/SettingsSconti.xml");

                    $root = $doc->documentElement;

                    $minimiSpesi = $root->getElementsByTagName("MinimiSpesi")->item(0)->textContent;
                    $valore = $root->getElementsByTagName("Valore")->item(0)->textContent;
                    $dataInizio = $root->getElementsByTagName("DataInizio")->item(0)->textContent;
                    $reputazioneMin = $root->getElementsByTagName("ReputazioneMin")->item(0)->textContent;
                    $mesiMin = $root->getElementsByTagName("MesiMin")->item(0)->textContent;
                    $anniMin = $root->getElementsByTagName("AnniMin")->item(0)->textContent;
                    $casaSconto = $root->getElementsByTagName("CasaSconto")->item(0)->textContent;
                    $genereSconto = $root->getElementsByTagName("GenereSconto")->item(0)->textContent;
                    $giochiDaAdmin = $root->getElementsByTagName("listaGiochiAdmin")->item(0)->getElementsByTagName("Gioco");
                    // var_dump($giochiDaAdmin);
                    foreach($giochiDaAdmin as $giocoAdmin){
                        array_push($listaGiochiAdmin, $giocoAdmin->textContent);
                    }


                    
                    echo "<p>Euro minimi spesi attuali: $minimiSpesi €</p>
                    <form method=\"post\" action=\"GestioneAdmin.php\">
                        <label for=\"minimiSpesi\">Modifica Minimi Spesi:</label>
                        <input type=\"text\" id=\"minimiSpesi\" name=\"minimiSpesi\" >
                        <input type=\"submit\" name=\"modificaMinimiSpesi\" value=\"Modifica\">
                    </form>

                    <p>Euro minimi spesi da una certa data attuali: $valore € </p>
                    <form method=\"post\" action=\"GestioneAdmin.php\">
                        <label for=\"valoreSconto\">Modifica Valore Sconto:</label>
                        <input type=\"text\" id=\"valoreSconto\" name=\"valoreSconto\" >
                        <input type=\"submit\" name=\"modificaValoreSconto\" value=\"Modifica\">
                    </form>

                    <p>Data di inizio per spesa minima attuale: $dataInizio</p>
                    <form method=\"post\" action=\"GestioneAdmin.php\">
                        <label for=\"dataInizio\">Modifica Data Inizio:</label>
                        <input type=\"text\" id=\"dataInizio\" name=\"dataInizio\" >
                        <input type=\"submit\" name=\"modificaDataInizio\" value=\"Modifica\">
                    </form>

                    <p>Reputazione minima attuale: $reputazioneMin</p>
                    <form method=\"post\" action=\"GestioneAdmin.php\">
                        <label for=\"reputazioneMin\">Modifica Reputazione Minima:</label>
                        <input type=\"text\" id=\"reputazioneMin\" name=\"reputazioneMin\" >
                        <input type=\"submit\" name=\"modificaReputazioneMin\" value=\"Modifica\">
                    </form>
                    <p>Tempo minimo d'iscrizione attuale: $anniMin anni, $mesiMin mesi</p>
                    <form method=\"post\" action=\"GestioneAdmin.php\">
                        <label for=\"anniMin\">Modifica Anni Minimi:</label>
                        <input type=\"text\" id=\"anniMin\" name=\"anniMin\" >
                        <br>
                        <label for=\"mesiMin\">Modifica Mesi Minimi:</label>
                        <input type=\"text\" id=\"mesiMin\" name=\"mesiMin\" >
                        <input type=\"submit\" name=\"modificaTempoIscrizione\" value=\"Modifica\">
                    </form>
                    <p>Casa di sviluppo sconto: $casaSconto</p>
                    <form method=\"post\" action=\"GestioneAdmin.php\">
                        <label for=\"casaSconto\">Modifica Casa Sconto:</label>
                        <input type=\"text\" id=\"casaSconto\" name=\"casaSconto\" >
                        <input type=\"submit\" name=\"modificaCasaSconto\" value=\"Modifica\">
                    </form>
                    <p>Genere sconto: $genereSconto</p> 
                    <form method=\"post\" action=\"GestioneAdmin.php\">
                        <label for=\"genereSconto\">Modifica Genere Sconto:</label>
                        <select id=\"genereSconto\" name=\"genereSconto\" >
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[0]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[0]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[0]}</option>
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[1]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[1]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[1]}</option> 
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[2]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[2]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[2]}</option>
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[3]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[3]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[3]}</option>
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[4]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[4]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[4]}</option>
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[5]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[5]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[5]}</option>
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[6]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[6]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[6]}</option>
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[7]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[7]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[7]}</option>
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[8]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[8]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[8]}</option>
                            <option value=\"{$setValoriGenerePreferitoSettingsSconti[9]}\" ";
                            if($genereSconto == $setValoriGenerePreferitoSettingsSconti[9]) echo " selected";
                            echo ">{$setValoriGenerePreferitoSettingsSconti[9]}</option>
                        </select>
                        <input type=\"submit\" name=\"modificaGenereSconto\" value=\"Modifica\">    
                    </form>
                    <p>Lista Giochi degli admin (spunta per eliminare)</p>
                    <form method=\"post\" action=\"GestioneAdmin.php\">";
    
                    foreach($listaGiochiAdmin as $giocoAdmin){
                        echo "<label for=\"giochiAdmin\">ID Giochi Admin: $giocoAdmin</label>
                        <input type=\"checkbox\" id=\"giochiAdmin\" name=\"giochiAdmin[]\" value=\"$giocoAdmin\" ><br />";
                    }
                    echo "<input type=\"text\" id=\"nuoviGiochiAdmin\" name=\"nuoviGiochiAdmin\" placeholder=\"Inserisci nuovi giochi separati da ;\" >";
                        
                    echo "<input type=\"submit\" name=\"modificaGiochiAdmin\" value=\"Modifica\">
                    </form>

                    <p>Impostazioni Sconto grado</p>
                    <form method=\"post\" action=\"GestioneAdmin.php\">
                        <label for=\"scontoGrado4\">Sconto grado 4:</label>
                        <select id=\"scontoGrado4\" name=\"scontoGrado4\" >";
                            for($i=0; $i<=70; $i+=5){
                                echo "<option value=\"$i\">$i%</option>";
                            }
                        echo "</select><br>
                        <label for=\"scontoGrado5\">Sconto grado 5:</label>
                        <select id=\"scontoGrado5\" name=\"scontoGrado5\" >";
                            for($i=0; $i<=70; $i+=5){
                                echo "<option value=\"$i\">$i%</option>";
                            }
                        echo "</select><br>
                         <label for=\"scontoGrado6\">Sconto grado 6:</label>
                        <select id=\"scontoGrado6\" name=\"scontoGrado6\" >";
                            for($i=0; $i<=70; $i+=5){
                                echo "<option value=\"$i\">$i%</option>";
                            }
                        echo "</select><br>";
                        
                        echo "<input type=\"submit\" name=\"modificaScontoGrado\" value=\"Modifica\">
                    </form>
                                
                            
                    

                    <hr><br />

                    <h2> Impostazione i parametri del modificatore commenti</h2>
                     <form action=\"GestioneAdmin.php\" method=\"post\">
                        <label for=\"valoreforza\">Valore forza modificatore commenti:</label>
                        <select id=\"valoreforza\" name=\"valoreforza\" >";
                            for($i=5; $i<=50; $i+=0.5){
                                echo "<option value=\"$i\" selected>$i</option>";
                                
                            }
                        echo" </select>
                        <br>
                        <input type=\"submit\" name=\"modificaValoreForza\" value=\"Modifica\">
                        <label for=\"valorerangemax\">Valore range max modificatore commenti:</label>
                        <select id=\"valoreRange\" name=\"valoreRange\" >";
                            for($i=0.01; $i<=0.5; $i+=0.01){
                                echo "<option value=\"$i\" selected>$i</option>";
                                
                            }
                        echo" </select>
                        <br>
                        <input type=\"submit\" name=\"modificaValoreRange\" value=\"Modifica\">
                    </form>
                    <div class=\"buttons\">
                        <div class=\"backarrow\">
                            <button onclick=\"swapperInSettingsSconti()\"><img src=\"Stile/Icone/iconafreccia.png\" alt=\"ricercagiocobutton\" ></button>
                        </div>
                    </div>

                

                ";
                ?>
                </div>
                
                <div class="cardSettings hideCard" id="card12">
                
                    <div class="buttons">
                            <div class="backarrow">
                                <button onclick="swapperInSearchUtentiSco()"><img src="Stile/Icone/iconafreccia.png" alt="ricercagiocobutton" ></button>
                            </div>
                        </div>
                        
                        <h2>Inserisci l'id dell'utente di cui vuoi impostare gli sconti</h2>
                        <?php 
                         
                
                
                    echo "<form method='post' action='GestioneAdmin.php'>
                    <label for='id_user_gestione'>ID Utente da gestire:</label>
                    <div class=\"ricercaUtente\">
                    <div><input type='text' id='id_user_gestione'  name='id_user_sconti_blacklist'/></div>
                    <div id=\"ContainerliveSearchUtenti\">
                        <div><label for='liveSearchInputUtenti'>Cerca utente per username: </label></br><input type='text' id='liveSearchInputUtenti' onkeyup='mostraRisultatiUtenti(this.value)'></div>
                        <div class=\"liveSearchUtenti\"></div>
                    </div>
                    </div>
                    <input type='submit' name='cercaUtenteScontoBlacklist' value='Cerca Utente'>
                    </form>";
                
                    if(isset($erroreRicercaScontoBlacklist)) echo "<h2>".$erroreRicercaScontoBlacklist."</h2>";
                ?>

                       
                     
                </div>
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