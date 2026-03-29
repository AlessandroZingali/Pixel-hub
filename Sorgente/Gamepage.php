<!-- Questa è la pagina che mostrera in base all'id in get che gli si passa le informazioni relative al gioco, i commenti e le recensioni degli utenti è possibile inserire se è di un grado suffucente una recensione o un commento e ovviamente acquistare il gioco in se-->

<?php 

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso,
require_once 'baseScontiUtente.php';

$service = 0;
$utente = "";
$titoloGioco = "";
$idGame = 0;
$userSet = false;

//Logica per verificare che siano stati passati in GET il titolo e l'id del gioco dalla pagina precedente
if(isset($_GET['titoloGioco']) && isset($_GET['idGioco'])){
    $titoloGioco = $_GET['titoloGioco'];
    $idGioco = $_GET['idGioco'];
}
else if(!isset($_GET['titoloGioco']) || !isset($_GET['idGioco'])){
 header("Location: Homepage.php");
}

// $titoloGioco = $_GET['titoloGioco'];
// $idGioco = $_GET['idGioco'];
$doc = getDoc("XML/Giochi.xml");
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $i){
            if($i->getAttribute("id_gioco")==$idGioco){
               $disponibilita = $i->getElementsByTagName('Disponibile')->item(0)->textContent;
            }
        }


session_start();
//Verifica se l'utente è loggato (servizio di autenticazione)
if(isset($_SESSION['userId'])){
    $utente = $_SESSION['userName'];
    $scontoManager = new scontiUtente($_SESSION['userId']);
    $service = 1; //La variabile service come in Home Page indica se l'utente è loggato o meno, getsendo 2 tipi di display differenti del sito
}

if($service == 0 && $disponibilita=='0'){
    echo "<script>
    
        alert('Il gioco selezionato non è attualmente disponibile per l\'acquisto. Verrai reindirizzato alla homepage.');
        setTimeout(function() {
        
        }, 1000);
        window.location.href = 'Homepage.php';

    </script>";

}

if(isset($_SESSION['tipoUtente'])){

    if($_SESSION['tipoUtente']==0 && $disponibilita=='0'){
        echo "<script>
    
        alert('Il gioco selezionato non è attualmente disponibile per l\'acquisto. Verrai reindirizzato alla homepage.');
        setTimeout(function() {
        
        }, 1000);
        window.location.href = 'Homepage.php';

    </script>";
    }
    else if(($_SESSION['tipoUtente']==1 && $disponibilita=='0' )) {
        header("Location: GestioneAdmin.php"); 
    }
}

    


?>

<?xml version="1.0" encoding="UTF-8"?>
<?php //Inizio della logica per le API della pagina gioco, ovvero l'invio di commenti e recensioni

    if(isset($_POST["invioCommento"])&& !empty($_POST["commentoUtente"])){ //Gestione Commenti
        $doc = getDoc("XML/Commenti.xml");
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        //Ricordiamo che per la gestione dei commenti, e anche delle recensioni, ogni commento avra un suo id univoco SOLO in relazione al gioco
        //a cui appartiene, quindi ogni gioco avra commenti con id che partono da 1 e cosi via. Di conseguenza se vogliamo il commento con id X,
        //dobbiamo specificare anche a quale gioco appartiene, referezinadone l'apposito id gioco.
        foreach($elem as $i){//Cerchiamo l'id del gioco, a cui recensioni e commenti apparterranno
            if($i->getAttribute("id_gioco")==$idGioco){
                $idGame = (int)$i->getAttribute("id_gioco");
                $gioco = $i;
                break;
            }
        }
        //Se il gioco non ha commenti, e ce ne è arrivato uno, creiamo un nuovo nodo apposito. Essendo che gli id partiranno da 1, se non c'è nessuno nodo dei commenti
        //con quel id gioco, la variabile $gioco rimarrà 0.
        if($idGame == 0){
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
            $commento->setAttribute("segnalazioni", 0);

            $commento->appendChild($testo);
            $gioco->appendChild($commento);
            $root->appendChild($gioco);
        }
        else{//Se il gioco ha gia commenti, aggiungiamo il nuovo commento
            
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
                $commento->setAttribute("segnalazioni", 0);
                $commento->appendChild($testo);
                $gioco->insertBefore($commento, $lastCommento);
           }
            else { //Questa ripetizione sembra dubbia, ma è funzionale. Può capitare che il gioco abbia un nodo ma non abbia commenti al suo interno. Cosi gestiamo il caso
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
                $commento->setAttribute("segnalazioni", 0);

                $commento->appendChild($testo);
                $gioco->appendChild($commento);

            }
        }
        
        $doc->save("XML/Commenti.xml");
        header("Location: Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco");
        $idGame = 0;
    }

    if(isset($_POST["invioRecensione"])&& !empty($_POST["recensioneUtente"]) && !empty($_POST["votoUtente"])){//Gestione Recensioni

        $doc = getDoc("XML/Recensioni.xml");
        $root = $doc->documentElement;
        $elem = $root->childNodes;
            foreach($elem as $i){
                if($i->getAttribute("id_gioco")==$idGioco){
                    $idGame = (int)$i->getAttribute("id_gioco");
                    $gioco = $i;
                    break;
                }
            }
        if($idGame == 0){//Se il gioco non ha recensioni, e ce ne è arrivata una, creiamo un nuovo nodo apposito
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
            $recensione->setAttribute("segnalazioni", 0);
            $recensione->setAttribute("voto", $_POST["votoUtente"]);
            

            $sommaVotiUtenti = 0;
            $sommaVotiUtenti += (int)$_POST["votoUtente"];
            $numRecensioniUtenti = 1;
            $mediaVotoUtenti = $sommaVotiUtenti / $numRecensioniUtenti;

            //Aggiorniamo la media delle recensioni utenti nel file Giochi.xml
                $docGiochi = getDoc("XML/Giochi.xml");
                $rootGiochi = $docGiochi->documentElement;
                $elemGiochi = $rootGiochi->childNodes;
                foreach($elemGiochi as $giocoElem){
                    if($giocoElem->getAttribute("id_gioco")==$idGioco){
                        $giocoElem->getElementsByTagName("MediaRecensioniUtenti")->item(0)->textContent = $mediaVotoUtenti;
                        $docGiochi->save("XML/Giochi.xml");
                    }
                }
         

            $recensione->appendChild($testo);
            $gioco->appendChild($recensione);
            $root->appendChild($gioco);
        }
        else{
            if ($gioco->hasChildNodes()) {

                // prendi l’ULTIMA recensione vera
                $recensioni = $gioco->getElementsByTagName("Recensione");
                $lastRecensione = $recensioni->item($recensioni->length - 1);

                $newId = ((int)$lastRecensione->getAttribute("id_recensione")) + 1;

                $recensione = $doc->createElement("Recensione");
                $testo = $doc->createElement(
                    "text",
                    htmlspecialchars($_POST["recensioneUtente"], ENT_QUOTES, "UTF-8")
                );

                $recensione->setAttribute("id_recensione", $newId);
                $recensione->setAttribute("id_utente", $_SESSION["userId"]);
                $recensione->setAttribute("data", date("d/m/Y"));
                $recensione->setAttribute("ore", date("H"));
                $recensione->setAttribute("minuti", date("i"));
                $recensione->setAttribute("like", 0);
                $recensione->setAttribute("dislike", 0);
                $recensione->setAttribute("segnalazioni", 0);
                $recensione->setAttribute("voto", (int)$_POST["votoUtente"]);

                //Calcoliamo la nuova media delle recensioni utenti
                $sommaVoti = 0;
                $numRecensioni = 0;

                foreach ($recensioni as $rec) {
                    $sommaVoti += (int)$rec->getAttribute("voto");
                    $numRecensioni++;
                }

                // aggiungi il voto nuovo
                $sommaVoti += (int)$_POST["votoUtente"];
                $numRecensioni++;

                $mediaVotoUtenti = $sommaVoti / $numRecensioni;

                // aggiorna Giochi.xml
                $docGiochi = getDoc("XML/Giochi.xml");
                foreach ($docGiochi->getElementsByTagName("Gioco") as $giocoElem) {
                    if ((int)$giocoElem->getAttribute("id_gioco") === (int)$idGioco) {
                        $giocoElem->getElementsByTagName("MediaRecensioniUtenti")
                                ->item(0)
                                ->textContent = round($mediaVotoUtenti, 2);
                        break;
                    }
                }
                $docGiochi->save("XML/Giochi.xml");

                $recensione->appendChild($testo);
                $gioco->appendChild($recensione);
            }

            else{ //Questa ripetizione sembra dubbia, ma è funzionale. Può capitare che il gioco abbia un nodo ma non abbia recensioni al suo interno. Cosi gestiamo il caso
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
                $recensione->setAttribute("segnalazioni", 0);
                $recensione->setAttribute("voto", $_POST["votoUtente"]);

            $sommaVotiUtenti = 0;
            $sommaVotiUtenti += (int)$_POST["votoUtente"];
            $numRecensioniUtenti = 1;
            $mediaVotoUtenti = $sommaVotiUtenti / $numRecensioniUtenti;

            //Aggiorniamo la media delle recensioni utenti nel file Giochi.xml
                $docGiochi = getDoc("XML/Giochi.xml");
                $rootGiochi = $docGiochi->documentElement;
                $elemGiochi = $rootGiochi->childNodes;
                foreach($elemGiochi as $giocoElem){
                    if($giocoElem->getAttribute("id_gioco")==$idGioco){
                        $giocoElem->getElementsByTagName("MediaRecensioniUtenti")->item(0)->textContent = $mediaVotoUtenti;
                        $docGiochi->save("XML/Giochi.xml");
                    }
                }
         

                $recensione->appendChild($testo);
                $gioco->appendChild($recensione);
            }
        }

        
        $doc->save("XML/Recensioni.xml");
        header("Location: Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco");
        $idGame = 0;
        
    }

    if(isset($_POST["Acquisto"])){

        $giochi = xmlPointer("XML/Giochi.xml");
        foreach($giochi as $i){
            if($i->getAttribute("id_gioco")==$idGioco){
               $PrezzoGioco = $i->getElementsByTagName('Prezzo')->item(0)->textContent;
            }
        }

        $doc = getDoc("XML/Carrelli.xml");
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        if(!($root->hasChildNodes())){

            $newIdCar=1;

            $carrello = $doc->createElement("carrello");

            $carrello->setAttribute("id_user", $_SESSION["userId"]);
            $carrello->setAttribute("id_cart",$newIdCar);

            $giocoInCart= $doc->createElement("gioco");
            $giocoInCart->setAttribute("id_gioco", $idGioco);
            $titolo = $doc->createElement("Titolo", $titoloGioco);
            $prezzo = $doc->createElement("prezzo", $PrezzoGioco);

            $giocoInCart->appendChild($titolo);
            $giocoInCart->appendChild($prezzo);


            $carrello->appendChild($giocoInCart);
            $root->appendChild($carrello);
            $doc->save("XML/Carrelli.xml");
        }
        else{
            foreach($elem as $cart){
                if($cart->getAttribute("id_user")==$_SESSION["userId"]){
                    $userSet=true;
                }
            }

            $doc = getDoc("XML/Carrelli.xml");
            $root = $doc->documentElement;
            $elem = $root->childNodes;
            if(!$userSet){
                
                $numCarrelli=$elem->length;

                $lastCarrello =  $elem->item($numCarrelli-1);
                $newCartId = ((int)$lastCarrello->getAttribute("id_cart"))+1;
                $carrello = $doc->createElement("carrello");

                $carrello->setAttribute("id_user", $_SESSION["userId"]);
                $carrello->setAttribute("id_cart",$newCartId);
                
                
                $giocoInCart= $doc->createElement("gioco");
                $giocoInCart->setAttribute("id_gioco",$idGioco);
                $titolo = $doc->createElement("Titolo", $titoloGioco);
                $prezzo = $doc->createElement("prezzo", $PrezzoGioco);

                $giocoInCart->appendChild($titolo);
                $giocoInCart->appendChild($prezzo);


                $carrello->appendChild($giocoInCart);
                $root->appendChild($carrello);
                $doc->save("XML/Carrelli.xml");
            }
            else if($userSet){
                $giocoInCart= $doc->createElement("gioco");
                $giocoInCart->setAttribute("id_gioco",$idGioco);
                $titolo = $doc->createElement("Titolo", $titoloGioco);
                $prezzo = $doc->createElement("prezzo", $PrezzoGioco);

                $giocoInCart->appendChild($titolo);
                $giocoInCart->appendChild($prezzo);

                foreach($elem as $cart){
                if($cart->getAttribute("id_user")==$_SESSION["userId"]){
                    $carrelloUtente = $cart;
                }
            }

                $carrelloUtente->appendChild($giocoInCart);
                $doc->save("XML/Carrelli.xml");
            }
        }
        
        
        header("Location: Carrello.php?iduser=" . $_SESSION["userId"]);

    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <?php //Gestiamo il titolo della pagina in maniera dinamica in base al gioco selezionato
        echo "<title> Game Page -".$_GET['titoloGioco']."</title> " ;
      
        
        ?>
        <link rel="stylesheet" type="text/css" href="Stile/Gamepage.css?v=3" /> 
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" /> 
        <script src="Script/likeAndDislikeGestione.js?v=3" defer="true"></script>
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <script type="text/javascript" src="Script/SegnalazioniGestione.js?v=3"></script>
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
                    <button class="botMenu"><img src="Stile/Icone/iconamenu.png" alt=""></button>
                    <ul>                       
                        <li><a href="Homepage.php">Home</a></li>
                        <li><a href="catalogo.php">Catalogo </a></li>
                        <li><a href="carrello.php">Carrello </a></li>
                        <?php
                            //Gestiamo la visualizzazione del link di login o logout in base allo stato di $service, il quale ricordiamo è la flag di stato dell'utente (guest o loggato).
                            // Come si può vedere se il service non è attivo (guest) eliminiamo anche le informazioni salvate in sessionStorage riguardo l'utente.
                            //ATTENZIONE: la parte di script è solo per sicurezza, le voci della session lato Client sono eliminate in ogni caso alla disconnessione dell'utente nella pagina di login.php
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
                                echo "<li><a href=\"Profilo.php\">Profilo</a></li> 
                                <p id=\"saldo\"> Pixels: ".$_SESSION['Pixels']." </br> Saldo attuale: ".$_SESSION['Saldo']." € </p>";
                            
                                if($_SESSION['tipoUtente'] == '2')
                                echo "<li><a href=\"GestioneAdmin.php\">Gestione</a></li>";
                            
                            
                                if($_SESSION['tipoUtente'] == "1")
                                echo "<li><a href=\"gestionePublisher.php\">Gestione</a></li>";
                            }
                        ?>
                    </ul>
                </div> <!--Barra di ricerca dei giochi, mostra in modo dinamico una lista dei giochi in base al nome. Abbiamo gestito il comportamento nel file Script/Searchgame.js -->
                
                        <form id="searchBar" onsubmit="return false;">
                            <input id="searchBarInput" type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                            <div id="livesearch"></div>
                        </form>
            
            </div>
<!-- La presentazione del gioco si stuttura su una serie di div che ne compone l'ossatura, possiamo vedere un riga iniziale formata da presentazione gioco, che ne mostra le caratteristiche,
 suddivisa a sua volta in 4 aree, per immagine, informazioni base, giochi correlati e specifiche tecniche -->
            <div id="presentazioneGioco">
                
                <div id="immagineGioco">
                    <?php 
                    //Carichiamo l'immagine del gioco in modo dinamico in base all'id del gioco selezionato 
                        $elem = xmlPointer("XML/Giochi.xml");

                        foreach($elem as $i){
                            if($i->getAttribute("id_gioco")==$idGioco) $imagePath=$i->getElementsByTagName("Immagine")->item(0)->textContent;
                        }

                        echo "<img src=\"$imagePath\" alt=\"GameImage\" title=\"$titoloGioco\"></img>"

                    ?>
                    
                </div>
                <!-- Recuperiamo e mostriamo le informazioni base del gioco, come il titolo, il prezzo, la data di uscita ecc... -->
                <div id="statGioco">
                    <div>
                        <?php 
                        $elem = xmlPointer("XML/Giochi.xml");

                        foreach($elem as $i){
                            if($i->getAttribute("id_gioco")==$idGioco){
                               $PrezzoGioco = $i->getElementsByTagName('Prezzo')->item(0)->textContent;
                               $DataUscitaGioco = $i->getElementsByTagName('DataDiUscita')->item(0)->textContent;
                               $GenereGioco = $i->getElementsByTagName('Generi')->item(0)->textContent;
                               $PublisherGioco = $i->getElementsByTagName('Publisher')->item(0)->textContent;
                               $CasaSviluppoGioco = $i->getElementsByTagName('CasaSviluppo')->item(0)->textContent;
                               $DescrizioneGioco = $i->getElementsByTagName('Descrizione')->item(0)->textContent;
                                $VotoAdmin = $i->getElementsByTagName('MediaRecensioniAdmin')->item(0)->textContent;
                                $VotoUser = round($i->getElementsByTagName('MediaRecensioniUtenti')->item(0)->textContent);

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
                                    <td>Prezzo</td>";
                                        if($service==1){
                                            $sconti = $scontoManager->percentualeScontoGioco($idGioco);
                                            if(count($sconti) > 0){
                                            $sommaSconti=array_sum($sconti);
                                            $prezzoGiocoScontato = $PrezzoGioco - ($PrezzoGioco * ($sommaSconti/100));
                                            echo "<td> <p> <s>$PrezzoGioco</s> €  - > ".round($prezzoGiocoScontato, 2)." €</p></td>";
                                            }
                                             else{
                                                echo "<td> $PrezzoGioco € </td> ";
                                            }
                                        }
                                        else if($service==0){
                                            echo "<td> $PrezzoGioco € </td> ";
                                        }  
                                    
                                    
                                       

                                echo "</tr>
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
                                <tr>
                                    <td>Voto degli utenti</td>
                                    <td>";
                                    if ($VotoUser != '0') echo "$VotoUser/100";
                                    else echo "Non ancora valutato";
                                    echo "</td>
                                </tr>
                                <tr>
                                    <td>Voto degli admin</td>
                                    
                                    <td>";
                                    if ($VotoAdmin != '0') echo "$VotoAdmin/100";
                                    else echo "Non ancora valutato";
                                    echo "</td>
                                </tr>
                              </table>

                              <br>

                               <table>
                              <th>Sono stati applicati i seguenti sconti:</th>";

                              foreach($sconti as $sconto){
                                echo"
                            <tr><td>Sconto</td> <td > $sconto % </td> <td>Tipologia sconto:</td> <td> </td> </tr>";
                            }
                              echo"</table>";
                     ?>
                    </div>

                    <!-- Diamo anche la possibilta di accedere a diversi giochi correlati a quest'ultimo, basati sul genere, affinita, o stesso publisher o casa di sviluppo -->
                    <div id="consigliati">

                            <h3>Potrebbero piacerti anche:</h3>

                        <?php
                        $giochi = xmlPointer("XML/Giochi.xml");
                        
                        $idCorrelati = [];
                     //Cerchiamo i giochi correlati a quello attuale, salvandone gli id in un array
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
                        //Calcoliamo la lunghezza dell'array e cicliamo per mostrarne i titoli, cercando le informazioni di titolo e id in XML/Giochi.xml
                        //da passare poi come parametri GET alla pagina Gamepage.php
                        $len = count($idCorrelati);
                          
                        for ($k=0; $k<$len; $k++) {
                            $giochi = xmlPointer("XML/Giochi.xml");

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
                    <div>
                        <?php

                         //Se l'utente non è loggato, e di conseguenza possiedeGioco non è impostato, mostriamo un messaggio che lo invita a farlo per poter acquistare il gioco    
                        if($service==0 && !(isset($possiedeGioco))){
                                echo '
                                <div id="Acquisto">
                                    <p>Devi essere loggato </br>per poter acquistare il gioco.</p>
                                </div>';

                            }

                                                //Ovviamente il pulsante di acquisto sarà visibile solo se l'utente è loggato e non possiede già il gioco
                        if($service){            
                            
    
                            $inCart = false;
                            $elemC = xmlPointer("XML/Carrelli.xml");

                            foreach($elemC as $cart){
                                if($cart->getAttribute("id_user")==$_SESSION["userId"]){
                                    $giochiInCart = $cart->getElementsByTagName("gioco");
                                    
                                    

                                    foreach($giochiInCart as $giocoInCart){
                                        $titoloGiocoInCart = $giocoInCart->getElementsByTagName("Titolo")->item(0)->textContent;
                                        //echo $titoloGiocoInCart;
                                        if($titoloGiocoInCart == $titoloGioco){
                                            $inCart = true;
                                            break;
                                        }
                                    }
                                }

                            }

                    
                            $elem = xmlPointer("XML/utenti.xml");

                            //Verifichiamo se l'utente loggato possiede già il gioco
                            foreach ($elem as $utente) {

                                $idUtente = $utente->getAttribute("id_user");

                                if ($idUtente == $_SESSION['userId']) {

                                    $giochi = $utente->getElementsByTagName("listaGiochi")[0]->getElementsByTagName("idGiocoPosseduto");
                                    $possiedeGioco = false;

                                    foreach ($giochi as $gioco) {
                                        $idGiocoP = $gioco->textContent;

                                        if ($idGiocoP == $idGioco) {
                                            $possiedeGioco = true;
                                            break;
                                        }
                                    }

                                    //echo $possiedeGioco ? "Possiedi il gioco" : "Non possiedi il gioco";
                                    //echo $inCart ? " - Presente nel carrello" : " - Non presente nel carrello";
                                
                                    if (!$possiedeGioco && !$inCart) {
                                        echo '
                                        <form method="post" action="Gamepage.php?titoloGioco='.$titoloGioco.'&idGioco='.$idGioco.'">
                                        <div id="Acquisto">
                                            <input type="submit" id="buttonAcquista" value="Acquisto" name="Acquisto"/>
                                        </div></form>';
                                    }
                                    else if(!$possiedeGioco && $inCart){
                                        echo '
                                        <div id="Acquisto">
                                            <p>Presente nel carrello.</p>
                                        </div>';
                                    }
                                    else{
                                        echo '
                                        <div id="Acquisto">
                                            <p>Presente nella libreria.</p>
                                        </div>';
                                    }
                                }    
                            }
                        }
                        

                        ?>
                    </div>
                    

                </div>

                <!-- Recuperiamo le infomazioni sul le specifiche tecniche del gioco -->
                <div id="specGioco">
                    <?php 

                    $elem = xmlPointer("XML/Giochi.xml");
                    foreach($elem as $i){
                            if($i->getAttribute("id_gioco")==$idGioco){
                                $interoSpecMin = $i->getElementsByTagName('RequisitiMinimi')->item(0)->textContent;
                                $interoSpecRac = $i->getElementsByTagName('RequisitiRaccomandati')->item(0)->textContent;
                            }
                        }
                    // Le specifiche nel file XML sono divise fa un carattere ';'. Quindie andiamo a splittare le specifiche tecniche in base al quel simobolo, per poi mostrarle in tabella
                    $partiSpecMin = array_map('trim', explode(';', $interoSpecMin));
                    $partiSpecRac = array_map('trim', explode(';', $interoSpecRac));
                    
                    //Dividiamo le specifiche in due tabelle, una per le specifiche minime e una per quelle raccomandate
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
            
            <!-- Sezione per la descrizione e l'acquisto del gioco -->
            <div id="descAndBuy">
              <div id="descGioco">
                    <?php 
                        echo "<h3>Descrizione:</h3> <p>$DescrizioneGioco</p> </div>";


                       

              ?>
               
            </div>
            
            <!-- In social raggruppiamo i commenti e le recensioni -->
            <div id="social">
                <div id="commenti">
                
                    <!-- Prima i commmenti che varranno gestiti nella lettura attraverso sempre xmlPointer (DOM) e nella gestione dei like e dei dislike da javascript
                    con l'ausilio di AJAX e di relative API: aggiornamneto Like commenti e colore load like/dislike. La prima gestisce i nodi commenti nel relativo file XML/Commenti.xml, 
                    la seconda gestisce l'inserimento, il disinserimento e la relativa colorazione alla pressione di uno dei pulsanti-->
                    <h3>Commenti degli utenti:</h3> 
                    <?php 
                        // Gestione form commenti, ovviamente il form sarà visibile solo se l'utente è loggato e di grado 2 o superiore
                        if($service == 0) echo "<p>Devi essere loggato per poter lasciare un commento .</p>";
                        else if($_SESSION['Grado']<=1) echo "<p>Devi essere di grado 2 o superiore per lasciare un commento.</p>";
                        else{

                                echo " <form action=\"Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco\" method=\"post\" id=\"formCommenti\">
                                        <textarea name=\"commentoUtente\" rows=\"4\" cols=\"65\" placeholder=\"Scrivi il tuo commento qui...\"></textarea>
                                        <br />
                                        <input type=\"submit\" name=\"invioCommento\" value=\"Invia\"/> 
                                    </form>"; 
                                                
                        }
                    //   Mostriamo i commenti esistenti per il gioco attuale

                        echo "<h4>Commenti:</h4>";
                        $elem = xmlPointer("XML/Commenti.xml");
                        $vuoto=true;
                            foreach($elem as $i){
                            
                            // Scorriamo i nodi commento, prendendo solo quelli in cui l'attributo id gioco metcha con l'id gioco attuale
                            if($i->getAttribute("id_gioco")==$idGioco){
                               
                                
                                $commentoId = $i->getElementsByTagName("Commento"); //Prendiamo il primo nodo Commento
                                $vuoto=false;

                            

                                //Scarichiamo le informazioni di ogni commento dal primo nodo commento all'ultimo, presente nel nodo Gioco
                                foreach($commentoId as $c){

                                    $idUtenteCommento = $c->getAttribute('id_utente');
                                    $idCommento = $c->getAttribute('id_commento');
                                    $commentoTesto = $c->getElementsByTagName('text')->item(0)->textContent;
                                    $dataCommento = $c->getAttribute('data');
                                    $oraCommento = $c->getAttribute('ore');
                                    $minutoCommento = $c->getAttribute('minuti');
                                    $likeCommento = $c->getAttribute('like'); 
                                    $dislikeCommento = $c->getAttribute('dislike');
                                   
                                    //Effettuiamo una query al database per recuperare lo username dell'utente che ha scritto il commento
                                    $pointDB = new connectionDB();
                                    $mysqliConnection = $pointDB->connectDB();
                                    

                                    if (mysqli_connect_errno()){

                                        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                                    }
                              

                                    $queryLogin = "SELECT * FROM {$pointDB->getTableUsers()} WHERE ID = '$idUtenteCommento'";
                                    $resultQ = mysqli_query($mysqliConnection, $queryLogin);
                                    $num = mysqli_num_rows($resultQ);

                                    if($num == 1){

                                        $row=mysqli_fetch_array($resultQ);
                                        $nomeUtenteCommento = $row['Username'];
                                        echo "<div class=\"commentoUtente\">
                                                <div><a href=\"ProfiloEsterno.php?idUtenteExt=$idUtenteCommento\"><h4>$nomeUtenteCommento</h4></a></div>
                                                <div><p>$commentoTesto</p></div>
                                                <div class=\"likeAndDateContainer\">
                                                    <div class=\"dataCommento\"> <p>Data: $dataCommento - $oraCommento : $minutoCommento </p> </div>
                                                    <div class=\"likeDislike\">
                                                        <div class=\"like\">
                                                                <p id=\"likeButtonTextCom$idCommento\"> $likeCommento  </p> 
                                                                <button type=\"button\" id=\"likeButtonCom$idCommento\" ";
                                    //    Gestiamo i bottoni di like e dislike, in base allo stato di login dell'utente e al suo grado
                                        if($service != 0){ //Se il servizio di autenticazione è attivo
                                            if($_SESSION['Grado']>1) echo "onclick=\"LikeGestioneCommenti(".$_SESSION['userId'].", $idCommento, $idGioco, 'like')\""; //Metti questo onclick se l'utente è attivo
                                            else echo "onclick=\"userAlert(".$_SESSION['Grado'].")\""; //Usa quest'altro onclick se l'utente non ha il grado necessario
                                        }
                                        else echo "onclick=\"userAlert(0)\""; //Mentre se il servizio non è attivo, usa questo onclick
                                            //Metti il simbolo relativo, chiudi il button e il div
                                        echo ">&#128077;
                                            </button>
                                            </div>
                                                <div class=\"dislike\"> <p id=\"dislikeButtonTextCom$idCommento\">  $dislikeCommento   </p> 
                                                    <button id=\"dislikeButtonCom$idCommento\"type=\"button\" ";
                                              //Qui viene gestito dislike nello stesso modo di come è stato gestito il like
                                                if($service != 0){ 
                                            
                                                if( $_SESSION['Grado']>1) echo "onclick=\"LikeGestioneCommenti(".$_SESSION['userId'].", $idCommento, $idGioco, 'dislike')\"";
                                                else echo "onclick=\"userAlert(".$_SESSION['Grado'].")\"";
                                                    }
                                            
                                                else echo "onclick=\"userAlert(0)\"";
                                                
                                               
                                                echo">&#128078;</button>
                                                    </div>"; 
                                                    if(isset ($_SESSION['userId'])){ //Gestione del pulsante di segnalazione, visibile solo se l'utente è loggato
                                                   echo "<div class=\"buttonSegnalazioni\"><button  onclick=\"segnala($idCommento, $idGioco, 'com')\">!</button></div>";
                                                    }                                                                                
                                                echo "</div>
                                                </div>
                                            </div>";          
                                    }

                                }
                                
                            }
                        }
                        if($vuoto==true) echo"<h2>Nessun commento....</h2>";
                                       

                    ?>
               </div>
                <!-- La stesse cose fatte in commenti la facciamo in recensioni -->
               <div id="recensioni">
                     <h3>Recensioni degli utenti:</h3> 

                     <?php 
                        //Gestione form recensioni, ovviamente il form sarà visibile solo se l'utente è loggato e di grado 3 o superiore
                    if($service == 0)echo "<p>Devi essere loggato per poter lasciare una recensione .</p>";
                    else if($_SESSION['Grado']<=2) echo "<p>Devi essere di grado 3 per lasciare una recensione.</p>";
                    else{

                            echo " <form action=\"Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco\" method=\"post\" id=\"formRecensioni\">
                                    <textarea name=\"recensioneUtente\" rows=\"4\" cols=\"65\" placeholder=\"Scrivi la tua recensione qui...\"></textarea> <br /> 
                                    <input type=\"number\" cols=\"10\"  min=\"0\" max=\"100\" name=\"votoUtente\" placeholder=\" Voto da 0 a 100...\">
                                    
                                    <br />
                                    <input type=\"submit\" name=\"invioRecensione\" value=\"Invia\"/> 
                                </form>"; 
                                            
                        }
                      echo "<h4>Recensioni:</h4>";

                        $elem = xmlPointer("XML/Recensioni.xml");
                        $vuoto =true;

                        //Nel file xml di recensioni per ogni nodo gioco scorriamo la lista delle recensioni e ne scarichiamo le informazioni
                            foreach($elem as $i){
                                    if($i->getAttribute("id_gioco")==$idGioco){
                                        $vuoto=false;
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

                                            //Effettuiamo una query al database per recuperare lo username dell'utente che ha scritto la recensione
                                            $pointDB = new connectionDB();
                                            $mysqliConnection = $pointDB->connectDB();

                                            if (mysqli_connect_errno()){

                                                printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
                                            }
                                            

                                            $queryLogin = "SELECT * FROM {$pointDB->getTableUsers()} WHERE ID = '$idUtenteRecensione'";
                                            $resultQ = mysqli_query($mysqliConnection, $queryLogin);
                                            $num = mysqli_num_rows($resultQ);

                                            if($num == 1){
                                            // Stampiamo la recensione con le sue informazioni
                                                $row=mysqli_fetch_array($resultQ);
                                                $nomeUtenteRecensione = $row['Username'];
                                                echo "<div class=\"recensioneUtente\">
                                                        <div><a href=\"ProfiloEsterno.php?idUtenteExt=$idUtenteRecensione\"><h4>$nomeUtenteRecensione</h4></a></div>
                                                        <div><p>$recensioneTesto</p></div>
                                                        <div class=\"votoRecensione\"><p> Voto: ".$r->getAttribute('voto')."/100 </p></div>
                                                        <div class=\"likeAndDateContainer\">
                                                            <div class=\"dataRecensione\"> <p>Data: $dataRecensione - $oraRecensione : $minutoRecensione </p> </div>
                                                            <div class=\"likeDislike\">
                                                                <div class=\"like\">
                                                                    <p id=\"likeButtonTextRec$idRecensione\"> $likeRecensione  </p> 
                                                                    <button type=\"button\" id=\"likeButtonRec$idRecensione\" ";
                                                // Gestiamo i bottoni di like e dislike, in base allo stato di login dell'utente e al suo grado
                                                if($service != 0){ //Se il servizio di autenticazione è attivo
                                                if($_SESSION['Grado']>2) echo "onclick=\"LikeGestioneRecensioni(".$_SESSION['userId'].", $idRecensione, $idGioco, 'like')\""; //Metti questo onclick se l'utente è attivo
                                                else echo "onclick=\"userAlert(".$_SESSION['Grado'].")\"";  //Usa quest'altro onclick se l'utente non ha il grado necessario
                                                }
                                                else echo "onclick=\"userAlert(0)\"";//Mentre se il servizio non è attivo, usa questo onclick
                                                //Metti il simbolo relativo, chiudi il button e il div
                                                echo ">&#128077;
                                                        </button>
                                                        </div>
                                                        <div class=\"dislike\"> <p id=\"dislikeButtonTextRec$idRecensione\">  $dislikeRecensione   </p> 
                                                        <button id=\"dislikeButtonRec$idRecensione\"type=\"button\" ";
                                                //Qui viene gestito dislike nello stesso modo di come è stato gestito il like
                                                if($service != 0){
                                                        if($_SESSION['Grado']>2) echo "onclick=\"LikeGestioneRecensioni(".$_SESSION['userId'].", $idRecensione, $idGioco, 'dislike')\"";
                                                        else echo "onclick=\"userAlert(".$_SESSION['Grado'].")\"";
                                                    }
                                                    else echo "onclick=\"userAlert(0)\"";
                                                    echo ">&#128078;</button></div>
                                                                    <div class=\"buttonSegnalazioni\"><button  onclick=\"segnala($idRecensione, $idGioco, 'rec')\">!</button></div>                                                                              
                                                                </div>
                                                             </div>
                                                         </div>";          
                                            }

                                        }
                                        
                                    }
                                }
                                 if($vuoto==true) echo"<div><h2>Nessuna recensione....</h2></div>";
         
    
                    
            
                    ?>
                </div>
            </div>
        </div>
        <!-- Footer dell pagina -->
        <div id="footer"> 
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2026 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>

