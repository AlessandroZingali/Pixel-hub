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
    $xml = new DOMDocument();
    $xml->formatOutput = true;
    $xml->load('XML/ScontiAssegnati.xml');
    $elem = $xml->getElementsByTagName("Utente");
    foreach ($elem as $utenteNode) {
        if ($utenteNode->getAttribute('id_user') == $id_user) {
            $newSconto = $xml->createElement("Sconto", htmlspecialchars($sconto));
            $utenteNode->getElementsByTagName("scontiAssegnati")->item(0)->appendChild($newSconto);

            $xml->save('XML/ScontiAssegnati.xml');
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

    $giochi = xmlPointer("XML/Giochi.xml");


    

    

    foreach ($elem as $gioco) {
        $id = $gioco->getAttribute("id_gioco");
        if ($id == $idGioco) {
            $dispNode = $gioco->getElementsByTagName("Disponibile")->item(0);

        if ($dispNode->nodeValue == "1") {
            $dispNode->nodeValue = "0";
        } else {
            $dispNode->nodeValue = "1";
        }
          
        }

    }
    $doc->save("XML/Giochi.xml");

    header("Location: GestioneAdmin.php");
    exit();
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
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <script type="text/javascript" src="Script/cardGestioneAdminChanger.js"> </script>

      
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
            <div class="adminFunctions" id="card1">
            <h1>Ticket utenti</h1>

            <table id="TabFaq">
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

            <div class="buttons">
                            <div class="settings">
                                <button onclick="swapperInSettings()"><img src="Stile/Icone/iconafreccia.png" alt="settingbutton" ></button>
                            </div>
                            <div class="settings">
                                <button onclick="swapperInModificaGioco()"><img src="Stile/Icone/iconafreccia.png" alt="settingbutton" ></button>
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
                echo "ID utente: $id<br>";

                $sconti = $utente->getElementsByTagName("Sconto");
                echo "Sconti assegnati: ";

                foreach ($sconti as $sconto) {
                    echo $sconto->nodeValue . " ";
                }

                echo "<br><br>";

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

             <!-- Funzione admin:sospensione gioco -->
              <h3> Sospendi gioco dal catalogo </h3>
              <form method="post" action="GestioneAdmin.php">
                <label for="id_gioco">ID Gioco da sospendere:</label>
                <input type="text" id="id_gioco" name="id_gioco_da_sospendere"  required>
                <input type="submit" name="sospendi" value="Sospendi Gioco">
                </form>
                    <div class="buttons">
                        <div class="backarrow">
                            <button onclick="swapperInSettings()"><img src="Stile/Icone/iconafreccia.png" alt="settingbutton" ></button>
                        </div>
                    </div>
            </div>

            <div class="cardSettings hideCard" id="card3">
                <h1>Modifica Gioco</h1>
                <!-- Contenuto per la modifica del gioco -->
                
                <form method="post" action="GestioneAdmin.php">
                    <!-- Aggiungi qui i campi per modificare il gioco -->
                    <label for="id_gioco_modifica">ID Gioco da modificare:</label>
                    <input type="text" id="id_gioco_modifica" name="id_gioco_modifica" required></br>
                    <label for="nuovo_nome">Nuovo Nome:</label>
                    <input type="text" id="nuovo_nome" name="nuovo_nome" required></br>
                    <label for="nuovo_prezzo">Nuovo Prezzo (€):</label>
                    <input type="text" id="nuovo_prezzo" name="nuovo_prezzo" required></br>
                    <label for="nuova_descrizione">Nuova Descrizione:</label>
                    <input type="text" id="nuova_descrizione" name="nuova_descrizione" required></br>
                    <label for="id_correlati">ID Giochi Correlati (separati da virgola):</label>
                    <input type="text" id="id_correlati" name="id_correlati" required></br>
                    <input type="submit" name="modificaGioco" value="Modifica Gioco">
                </form>
                <div class="buttons">
                    <div class="backarrow">
                        <button onclick="swapperInModificaGioco()"><img src="Stile/Icone/iconafreccia.png" alt="modificagiocobutton" ></button>
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