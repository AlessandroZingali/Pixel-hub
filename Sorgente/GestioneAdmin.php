<?php

require 'serverUtility.php'; 

$service = 0;
$utente = "";

session_start();
if($_SESSION['tipoUtente'] == '1'){
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

if (isset($_POST['assegnaSconto']) && isset($_POST['id_user']) && !empty($_POST['sconto'])) {
    $id_user = $_POST['id_user'];
    $sconto = $_POST['sconto'];
    $doc = getDoc('XML/ScontiAssegnati.xml');
    $root = $doc->documentElement;
    $elem = $root->childNodes;
    foreach ($elem as $utenteNode) {
        if ($utenteNode->getAttribute('id_user') == $id_user) {
            $newSconto = $doc->createElement("Sconto", htmlspecialchars($sconto));
            $utenteNode->getElementsByTagName("scontiAssegnati")->item(0)->appendChild($newSconto);

            $doc->save('XML/ScontiAssegnati.xml');
            break;
        }
    }

    header("Location: GestioneAdmin.php");
    exit();
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

    header("Location: GestioneAdmin.php");
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

             
            if(isset($_POST['nuovaMediaAdmin'])) $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent = $_POST['nuovo_nome'];

            
            if(isset($_POST['nuovo_prezzo'])) $gioco->getElementsByTagName("Prezzo")->item(0)->textContent = $_POST['nuovo_prezzo'];


            if(isset($_POST['nuovaCasa'])) $gioco->getElementsByTagName("CasaSviluppo")->item(0)->textContent = $_POST['nuovaCasa'];

            if(isset($_POST['nuovoPublisher'])) $gioco->getElementsByTagName("Publisher")->item(0)->textContent = $_POST['nuovoPublisher'];
            
            if(isset($_POST['nuoviReqMin'])) $gioco->getElementsByTagName("RequisitiMinimi")->item(0)->textContent = $_POST['nuoviReqMin'];
            
            if(isset($_POST['nuova_descrizione'])) $gioco->getElementsByTagName("Descrizione")->item(0)->textContent = $_POST['nuova_descrizione'];

            if(isset($_POST['nuovaMediaAdmin'])) $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent = $_POST['nuovaMediaAdmin'];

            if(isset($_POST['nuovaData'])) $gioco->getElementsByTagName("DataDiUscita")->item(0)->textContent = $_POST['nuovaData'];

            if(isset($_POST['nuovoGenere'])) $gioco->getElementsByTagName("Generi")->item(0)->textContent = $_POST['nuovoGenere'];
            
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
    header("Location: GestioneAdmin.php");
}

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

if(isset($_POST['modificaUtente']) && !empty($_POST['id_user_gestione'])) {

$table_users='Tabella_Utenti';
$id_utente = $_POST['id_user_gestione'];
connectDB();

    if (mysqli_connect_errno()) {
        printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
    }

    $sql = "
        UPDATE $table_users
        SET Email = '".$_POST['Email']."' ,
        Password = '".$_POST['Password']."',
        Username = '".$_POST['Username']."',
        Esperienza = '".$_POST['Esperienza']."',
        Grado = '".$_POST['Grado']."',
        Pixels = '".$_POST['Pixels']."',
        Saldo_attuale = '".$_POST['Saldo_attuale']."',
        Data_di_Nascita = '".$_POST['Data_di_Nascita']."',
        Nome = '".$_POST['Nome']."',
        Cognome = '".$_POST['Cognome']."',
        Tipologia_utente = '".$_POST['Tipologia_utente']."',
        imgProfiloPath = '".$_POST['imgProfiloPath']."',
        PIVA = '".$_POST['PIVA']."'
        WHERE ID = '$id_utente'
    ;";


    // Esecuzione query
    if (mysqli_query(connectDB(), $sql)) {

        // header("Location:GestioneAdmin.php"); 
    } 
    else{
        printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
    }
}

if(isset($_POST['sospensione'])){
    $table_users='Tabella_Utenti';
    $id_utente = $_POST['id_user_gestione'];
    connectDB();

    if (mysqli_connect_errno()) {
        printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
    }


    $sql = "
        UPDATE $table_users
        SET Grado = 0
        WHERE ID = '$id_utente'
    ;";

    // Esecuzione query
    if (mysqli_query(connectDB(), $sql)) {
        echo"<script>console.log('sospensione eseguita')</script>";
        // header("Location:GestioneAdmin.php"); 
    } 
    else{
        printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
    }
}

if(isset($_POST["richiediRimborso"])){

    $table_users='Tabella_Utenti';
    $id_utente = $_POST['id_user_rimborso'];
    $baseDB = connectDB();

    if (mysqli_connect_errno()) {
        printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
    }


    $sql ="SELECT * FROM $table_users WHERE ID=\"".$_POST['id_user_rimborso']."\";";
    
    $resultQ = mysqli_query($baseDB, $sql);
    $num = mysqli_num_rows($resultQ);
    if($num == 1){
        $row=mysqli_fetch_array($resultQ);
        
        $esperienzaDedotta= $row['Esperienza']-($_POST['pixels_da_togliere']*$_POST['modCommentiPrecedente']);

        $pixelsDedotti = $row['Pixels'] - $_POST['pixels_da_togliere'];
        
        $saldoDaAggiungere = $row['Saldo_attuale'] + $_POST['importo'];
        
        $sql ="UPDATE $table_users SET Esperienza=$esperienzaDedotta, Pixels=$pixelsDedotti, Saldo_attuale=$saldoDaAggiungere WHERE ID=\"".$_POST['id_user_rimborso']."\";";
        if(mysqli_query($baseDB, $sql)){
        $docUtente = getDoc("XML/utenti.xml");
        $docLog = getDoc("XML/LogTransazioniGiochi.xml");

        $rootUtente = $docUtente->documentElement;
        $elemUtente = $rootUtente->childNodes;

        foreach($elemUtente as $utente){
            $listaGiochi = $utente->getElementsByTagName("listaGiochi");


            foreach($listaGiochi as $gioco){
                if($gioco->textContent == $_POST['id_gioco_rimborso']){
                    $gioco->parentNode->removeChild($gioco);
                    break;
                }
            }

            $docUtente->save('XML/utenti.xml');
        }

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
        } else {
            printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
        }
    }
    
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
            if(isset($_POST['cercaGioco']) && !empty($_POST['id_gioco_modifica'])) echo "<script>sessionStorage.setItem(\"activeChange\", \"ricercaGioco\");</script>";
            else if (isset($_POST['cercaUtente']) && !empty($_POST['id_user_gestione'])) echo "<script>sessionStorage.setItem(\"activeChange\", \"gestioneUtente\");</script>";
            else if (isset($_POST['cercaUtenteRimborso']) && !empty($_POST['id_user_gestione'])) echo "<script>sessionStorage.setItem(\"activeChange\", \"gestioneRimborso\");</script>";
            else echo "<script>sessionStorage.setItem(\"activeChange\", \"vuoto\");</script>";
        ?> 
        
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <script type="text/javascript" src="Script/cardGestioneAdminChanger.js"></script>
        <script type="text/javascript" src="Script/scriptMail.js"></script>

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
                        ?>
                    </ul>
                </div>

                    <form id="searchBar" onsubmit="return false;">
                        <input type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>
            <div class="adminFunctions" id="card0">
                <!-- card 0 di menu -->

                <h1>Menu Funzioni admin</h1>
                
                <div class="buttons">
                    <div class="sconti">
                    <p> - Vai alla pagina gestione sconti per gli utenti
                        <!-- accedi alla card 2 nascondi la card 0 -->
                        <button onclick="swapperInSettings()">  
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
                </div>
            </div>

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
                        <button onclick="swapperInTickets()">  <img src="Stile/Icone/iconafreccia.png" alt="sospendigiochobutton" ></button>
                    </div>
                </div>
            
            
            </div>


            <div class="cardSettings hideCard" id="card2">
                
            


        
            <h2> Gestione sconti utente</h2>
            <?php
            $elemSconti = xmlPointer('XML/ScontiAssegnati.xml'); //Richiama la funzione che restituisce il puntatore ai nodi figli della root del file XML
            $utenti = $elemSconti;

            foreach ($utenti as $utente) {
                $id = $utente->getAttribute("id_user");
                echo "<p>ID utente: $id</p><br>";

                $sconti = $utente->getElementsByTagName("Sconto");
                echo "<p>Sconti assegnati: ";

                foreach ($sconti as $sconto) {
                    echo $sconto->nodeValue . " ";
                }

                echo "</p><br><br>";

                echo "<form method='post' action='GestioneAdmin.php'>
                        <input type='hidden' name='id_user' value='$id'>
                        <label for='sconto_$id'>Assegna nuovo sconto:</label>
                        <input type='text' id='sconto_$id' name='sconto' >
                        <input type='submit' name='assegnaSconto' value='Assegna Sconto'>
                      </form><br><hr><br>";
                    
            }
            ?>
             <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInSettings()"><img src="Stile/Icone/iconafreccia.png" alt="ricercagiocobutton" ></button>
                    </div>
                </div>


            <!-- Funzione admin:rimborso  -->

            </div>
            <div class="cardSettings hideCard" id="card4">

                         
                <?php  
                echo "<h1>Cerca Gioco</h1>";
                echo "<form method=\"post\" action=\"GestioneAdmin.php\">
                    <!-- Aggiungi qui i campi per modificare il gioco -->
                    <label for=\"id_gioco_modifica\">ID Gioco da modificare:</label>
                    <input type=\"text\" id=\"id_gioco_modifica\" name=\"id_gioco_modifica\" >
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
                
            </div>
            <div class="cardSettings hideCard" id="card5">
                <!-- Funzione admin:sospensione gioco -->
              <h3> Sospendi gioco dal catalogo </h3>
              <form method="post" action="GestioneAdmin.php">
                <label for="id_gioco">ID Gioco da sospendere:</label>
                <input type="text" id="id_gioco" name="id_gioco_da_sospendere"  >
                <input type="submit" name="sospendi" value="Sospendi Gioco">
            </form>
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
                    <input type='text' id='id_user_gestione' name='id_user_gestione' >
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
            $table_users = "Tabella_Utenti";

            if (mysqli_connect_errno()){
                printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
            }
            

                $queryLogin = "SELECT * FROM $table_users WHERE ID = '".$_POST['id_user_gestione']."'";
                $resultQ = mysqli_query(connectDB(), $queryLogin);
                $num = mysqli_num_rows($resultQ);

                // Se il numero di righe restituite dalla query è 1 allora l'utente esiste e puo essere loggato
                if($num == 1){
                    $flag=1;
                    
                    $row=mysqli_fetch_array($resultQ);
                    echo "<h1>Gestione utente: ".$row['Username']." ID: ".$row['ID']."</h1>";

                    
                    echo "<form method=\"post\" action=\"GestioneAdmin.php\" >
                    
                                
                                <label for=\"Email\" >Modifica Email:</label>
                                <input type=\"text\" id=\"Email\" name=\"Email\" value=\"".$row['Email']."\"><br><br>

                                <label for=\"Password\" >Modifica Password:</label>
                                <input type=\"text\" id=\"Password\" name=\"Password\" value=\"".$row['Password']."\" ><br><br>

                                <label for=\"Username\" >Modifica Username:</label>
                                <input type=\"text\" id=\"Username\" name=\"Username\" value=\"".$row['Username']."\" ><br><br>

                                <label for=\"Esperienza\" >Modifica Esperienza:</label>
                                <input type=\"text\" id=\"Esperienza\" name=\"Esperienza\" value=\"".$row['Esperienza']."\" ><br><br>

                                <label for=\"Grado\" >Modifica Grado:</label>   
                                <input type=\"text\" id=\"Grado\" name=\"Grado\" value=\"".$row['Grado']."\" ><br><br>

                                <label for=\"Pixels\" >Modifica Pixels:</label>   
                                <input type=\"text\" id=\"Pixels\" name=\"Pixels\" value=\"".$row['Pixels']."\" ><br><br>

                                <label for=\"Nome\" >Modifica Nome:</label>   
                                <input type=\"text\" id=\"Nome\" name=\"Nome\" value=\"".$row['Nome']."\" ><br><br>

                                <label for=\"Cognome\" >Modifica Cognome:</label>   
                                <input type=\"text\" id=\"Cognome\" name=\"Cognome\" value=\"".$row['Cognome']."\" ><br><br>

                                <label for=\"Data_di_Nascita\">Modifica Data di nascita:</label>
                                <input type=\"text\" id=\"Data_di_Nascita\" name=\"Data_di_Nascita\" value=\"".$row['Data_di_Nascita']."\" ><br><br>

                                <label for=\"Saldo_attuale\" >Modifica saldo attuale :</label>   
                                <input type=\"text\" id=\"Saldo_attuale\" name=\"Saldo_attuale\" value=\"".$row['Saldo_attuale']."\" ><br><br>

                                <label for=\"Tipologia_utente\" >Modifica Tipo Utente (0=normale, 1=admin, 2=Publisher):</label>
                                <input type=\"text\" id=\"Tipologia_utente\" name=\"Tipologia_utente\" value=\"".$row['Tipologia_utente']."\" ><br><br>

                                <label for=\"imgProfiloPath\" >Modifica il percorso dell immagine del profilo :</label>   
                                <input type=\"text\" id=\"imgProfiloPath\" name=\"imgProfiloPath\" value=\"".$row['imgProfiloPath']."\" ><br><br>

                                <label for=\"PIVA\" >Modifica PIVA:</label>   
                                <input type=\"text\" id=\"PIVA\" name=\"PIVA\" value=\"".$row['PIVA']."\" ><br><br>

                                <label for=\"checkSopsensione\" >Sospendi utente?</label>   
                                <input type=\"checkbox\" id=\"checkSopsensione\" name=\"sospensione\" ><br><br>



                                <input type=\"hidden\" name=\"id_user_gestione\" value=\"".$row['ID']."\">
                                <input type=\"submit\" name=\"modificaUtente\" value=\"Applica modifiche\"></br>
                          </form>";
                } else {
                    echo "<h1>Utente non trovato</h1>";
                }
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
                echo "<form method='post' action='GestioneAdmin.php'>
                    <label for='id_user_gestione'>ID Utente da gestire:</label>
                    <input type='text' id='id_user_gestione' name='id_user_gestione' >
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
                 
                    $table_users='Tabella_Utenti';
                    $id_utente = $_POST['id_user_gestione'];
                    connectDB();

                    if (mysqli_connect_errno()) {
                        printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
                    }

                    $query="SELECT * FROM $table_users WHERE ID=$id_utente";

                    $result = mysqli_query(connectDB(), $query);

                    if ($result) {
                        $row = mysqli_fetch_array($result);
                        $pixelAttuali = $row['Pixels'];
                    } else {
                        echo "<h2>Utente non trovato</h2>";
                    }


                $elem = xmlPointer("XML/LogTransazioniGiochi.xml");
                foreach($elem as $trans){
                    if($_POST['id_user_gestione'] == $trans->getAttribute('IDGiocatore')){
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
                
                ?>
        
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInGestioneRimborsi()"><img src="Stile/Icone/iconafreccia.png" alt="usergestionbutton" ></button>
                    </div>
                </div>
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