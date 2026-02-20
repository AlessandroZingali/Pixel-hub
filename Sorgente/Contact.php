<?php
/* Il seguente file serve per creare una form per il contatto diretto con gli amministratori. Verranno registrati all'invio del testo, solamente se l'utente è loggato,
dei nuovi nodi ticket all'interno di un apposito file XML omonimo. Qusto file sarà poi letto in una pagina specifica riservata agli amministratori del sito */

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

//Inizia la sessione
session_start();

//Variabili di stato per capire se l’utente è loggato
$service = 0;
$utente = "";

//Controllo login
if (isset($_SESSION['userId'])) {
    $utente = $_SESSION['userName']; //Nome utente da mostrare
    $service = 1;                   //Utente autenticato
}

//Controlla se il form è stato inviato E l’utente è loggato
if (isset($_POST["invioTicket"]) && $service === 1) {

    // Carica il documento XML
    $doc = getDoc("XML/Ticket.xml");
    $root = $doc->documentElement;

    // Calcolo del nuovo id_ticket (incrementale)
    $newId = 1;
    foreach ($root->getElementsByTagName("ticket") as $t) {
        $id = (int)$t->getAttribute("id_ticket");
        if ($id >= $newId) {
            $newId = $id + 1;
        }
    }

    //Creazione del nodo <ticket>
    $ticket = $doc->createElement("ticket");

    //Attributi del ticket
    $ticket->setAttribute("id_ticket", $newId);
    $ticket->setAttribute("id_utente", $_SESSION["userId"]);
    $ticket->setAttribute("data", date("d/m/Y"));
    $ticket->setAttribute("ore", date("H"));
    $ticket->setAttribute("minuti", date("i"));

    //Nodo testo con il messaggio dell’utente (sanificato)
    $testo = $doc->createElement(
        "text",
        htmlspecialchars($_POST["ticketUtente"], ENT_QUOTES, "UTF-8")
    );

    //Aggiunge il testo al ticket
    $ticket->appendChild($testo);

    //Aggiunge il ticket alla root dell’XML
    $root->appendChild($ticket);

    //Salva il file XML aggiornato
    $doc->save("XML/Ticket.xml");

    //Redirect alla homepage
    header("Location: Homepage.php");
    exit;
}
?>
 
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Catalogo</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/Contact.css?v=3" />
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" /> 
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

         
                <!-- se l'utente ha fatto l'accesso puo mandare nella form una segnalazione agli admin  -->
                <h2>
                    Contattaci:
                </h2>
                <p>Se hai avuto problemi con il sito nell'acquisto di un gioco o magari vuoi darci una dritta su come migliorare facci sapere scrivendolo qui sotto:</p>
                <?php 
                    //Controlla se l'utente è loggato
                    if($service == 0) echo "<p>Devi essere loggato per mandare un ticket.</p>";
                    else{
                        //Mostra il form per l'invio del ticket
                        echo "<form action=\"Contact.php?idUtente={$_SESSION['userId']}\" method=\"post\" id=\"formTicket\"> 

                                <textarea name=\"ticketUtente\" placeholder=\"Scrivi il tuo messaggio qui\"></textarea>
                                
                                <br />/>
                                <input type=\"submit\" name=\"invioTicket\" value=\"Invia la segnalazione\"/> 
                            </form>"; 
                                            
                    }
                ?>
           

        </div>
        <div id="footer">
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy;  Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>
