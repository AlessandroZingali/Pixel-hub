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
    if(isset($_POST['nuovo_nome'])) $nuovo_nome = $_POST['nuovo_nome'];
    if(isset($_POST['nuovo_prezzo']))$nuovo_prezzo = $_POST['nuovo_prezzo'];
    if(isset($_POST['nuova_descrizione']))$nuova_descrizione = $_POST['nuova_descrizione'];
    if(isset($_POST['CasaSviluppo']))$nuovaCasa = $_POST['CasaSviluppo'];
    if(isset($_POST['id_correlati']))$id_correlati = explode(',', $_POST['id_correlati']);

    $xml = getDoc('XML/Giochi.xml');
    $root = $xml->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $gioco) {
        if ($gioco->getAttribute('id_gioco') == $id_gioco) {
            // var_dump($gioco);
            // echo "<script>console.log($gioco);</script>";
            if(isset($nuovo_nome)) $gioco->getElementsByTagName("Titolo")->item(0)->textContent = $nuovo_nome; 
            
             if(isset($nuovo_prezzo)) $gioco->getElementsByTagName("Prezzo")->item(0)->textContent = htmlspecialchars($nuovo_prezzo);
             if(!empty($nuova_descrizione)) $gioco->getElementsByTagName("Descrizione")->item(0)->nodeValue = htmlspecialchars($nuova_descrizione);
             if(!empty($nuovaCasa)) $gioco->getElementsByTagName("CasaSviluppo")->item(0)->nodeValue = htmlspecialchars($nuovaCasa);
             if(!empty($_POST['Publisher'])) $gioco->getElementsByTagName("Publisher")->item(0)->nodeValue = htmlspecialchars($_POST['Publisher']);
             if(!empty($_POST['MediaRecensioniAdmin'])) $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->nodeValue = htmlspecialchars($_POST['MediaRecensioniAdmin']);

             // Aggiorna i giochi correlati
             if(!empty($id_correlati)){ 
                $correlatiNode = $gioco->getElementsByTagName("TitoliCorrelati");
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
            if(isset($_POST['cercaGioco']) && !empty($_POST['id_gioco_modifica'])) echo "<script>sessionStorage.setItem(\"activeChange\", true);</script>";
            else echo "<script>sessionStorage.setItem(\"activeChange\", false);</script>";
        ?> 
        
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <script type="text/javascript" src="Script/cardGestioneAdminChanger.js"></script>
        
        
      
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
                        ?>
                    </ul>
                </div>

                    <form id="searchBar" onsubmit="return false;">
                        <input type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>
            <div class="adminFunctions" id="card0">
                <h1>Menu Funzioni admin</h1>
                <div class="buttons">
                <div class="sconti">
                   <p>Vai alla pagina gestione sconti per gli utenti
                    <button onclick="swapperInSettings()">  <img src="Stile/Icone/scontoicon.png" alt="sconticonbutton" > </button></p>
                </div>
                <div class="modifica">
                    <p>Modifica un gioco presente
                    <button onclick="swapperInRicerca()"> <img src="Stile/Icone/modificaicon.png" alt="ricercabutton" ></button></p>
                </div>
                <div class="sospendi">
                    <p>Sospendi/Riattiva un gioco dal catalogo
                    <button onclick="swapperInSospensione()"> <img src="Stile/Icone/icona elenco.png" alt="sospendibutton" ></button></p>
                </div>
                <div class="tickets">
                    <p>Gestisci i ticket degli utenti
                    <button onclick="swapperInTickets()"> <img src="Stile/Icone/ticketicon.png" alt="ticketbutton" ></button></p>
                </div>
            </div>
            </div>

            <div class="cardSettings hideCard" id="card1">

                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInTickets()"><img src="Stile/Icone/iconafreccia.png" alt="ricercagiocobutton" ></button>
                    </div>
                </div>
                <!-- Card di gestione ticket  -->
                <h1>Gestione Ticket Utenti</h1>


                

                <table id="TabellaTicket">
                    <tr>
                        
                        <th id="ColDom">
                            Domanda
                        </th>          
                    

                        
                        <th id="ColRis">
                            Risposta
                        </th> 

                        <th id="ColInv">
                            Invia
                        </th>
                        
                    </tr>
                    <!-- Ciclo PHP per l'estrazione delle domande e risposte dal file XML -->
                    <?php
                        $elem = xmlPointer('XML/Ticket.xml'); //Richiama la funzione che restituisce il puntatore ai nodi figli della root del file XML
                
                        //Ciclo per l'estrazione delle domande e risposte
                        foreach($elem as $tickets){
                            

                        $testo = $tickets->getElementsByTagName("text")->item(0)->nodeValue;


                            echo " <tr>
                            <td>$testo</td>
                            <td><textarea id='RispostaTicket'> </textarea> </td>
                            <td><input type='submit' value='Invia'></td>
                            
                            
                            </tr>";

                        }  
                        ?> 
                </table>
            
            
            </div>


            <div class="cardSettings hideCard" id="card2">
                
            <h1>Gestione Sconti e Rimborsi</h1>


        
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
                        <label for='sconto'>Assegna nuovo sconto:</label>
                        <input type='text' id='sconto' name='sconto' required>
                        <input type='submit' name='assegnaSconto' value='Assegna Sconto'>
                      </form><br><hr><br>";
                    
            }
            ?>
            


            <!-- Funzione admin:rimborso  -->

            <h2> Gestione rimborsi </h2>
            <form method="post" action="GestioneAdmin.php">
                <label for="id_user_rimborso">ID Utente da rimborsare:</label>
                <input type="text" id="id_user_rimborso" name="id_user_rimborso" required> <br><br>
                <label for="importo_rimborso">Importo da rimborsare (€):</label>
                <input type="text" id="importo_rimborso" name="importo_rimborso" required>
                <input type="submit" name="processaRimborso" value="Processa Rimborso">
            </form>



                    <div class="buttons">
                        <div class="backarrow">
                            <button onclick="swapperInSettings()"><img src="Stile/Icone/iconafreccia.png" alt="settingbutton" ></button>
                        </div>
                    </div>
            </div>
            <div class="cardSettings hideCard" id="card4">

                         
                <?php  
                echo "<h1>Cerca Gioco</h1>";
                echo "<form method=\"post\" action=\"GestioneAdmin.php\">
                    <!-- Aggiungi qui i campi per modificare il gioco -->
                    <label for=\"id_gioco_modifica\">ID Gioco da modificare:</label>
                    <input type=\"text\" id=\"id_gioco_modifica\" name=\"id_gioco_modifica\" required>
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
                                

                            }
                        }
                
                echo "<h2>Modifica i dettagli del gioco: $titolo</h2>";
                echo "<form method='post' action='GestioneAdmin.php'>
                    <input type='hidden' name='id_da_modificare' value='".htmlspecialchars($_POST['id_gioco_modifica'])."'>
                    
                        
                    
                    
                    <label for=\"nuovo_nome\">Titolo attuale:$titolo Nuovo Nome:</label>
                    <input type=\"text\" id=\"nuovo_nome\" name=\"nuovo_nome\"></br>
                    <label for=\"nuovo_prezzo\">Nuovo Prezzo (€):</label>
                    <input type=\"text\" id=\"nuovo_prezzo\" name=\"nuovo_prezzo\"></br>
                    <label for=\"nuova_descrizione\">Nuova Descrizione:</label>
                    <input type=\"text\" id=\"nuova_descrizione\" name=\"nuova_descrizione\" ></br>
                    <label for=\"id_correlati\">Aggiungi ad ID Giochi Correlati (separati da virgola):</label>
                    <input type=\"text\" id=\"id_correlati\" name=\"id_correlati\"></br>
                    <label for=\"CasaSviluppo\" id=\"nuovaCasa\" name=\"nuovaCasa\"> Nuova casa di sviluppo :</label>
                    <input type=\"text\" id=\"CasaSviluppo\" name=\"CasaSviluppo\" ></br>
                    <label for=\"Publisher\" id=\"nuovoPublisher\" name=\"nuovoPublisher\"> Nuovo publisher :</label>
                    <input type=\"text\" id=\"Publisher\" name=\"Publisher\" ></br>
                    <label for=\"MediaRecensioniAdmin\" id=\"nuovaMediaRecensioniAdmin\" name=\"nuovaMediaRecensioniAdmin\"> Nuova media recensioni admin :</label>
                    <input type=\"text\" id=\"MediaRecensioniAdmin\" name=\"MediaRecensioniAdmin\" ></br>
                    <label for=\"nuovo_genere\" id=\"nuovo_genere\" name=\"nuovo_genere\"> Nuovo genere :</label>
                    <input type=\"text\" id=\"nuovo_genere\" name=\"nuovo_genere\" ></br>

                    

                    
                    <input type=\"submit\" name=\"modificaGioco\" value=\"Modifica Gioco\">
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
                        echo "<br><input type=\"submit\" name=\"rimuoviCorrelati\" value=\"Rimuovi Correlati\">";
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
                <input type="text" id="id_gioco" name="id_gioco_da_sospendere"  required>
                <input type="submit" name="sospendi" value="Sospendi Gioco">
            </form>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInSospensione()"><img src="Stile/Icone/iconafreccia.png" alt="sospendigiochobutton" ></button>
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