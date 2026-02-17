<?php
/* Il seguente file serve per creare una form per il contatto diretto con gli amministratori. Verranno registrati all'invio del testo, solamente se l'utente è loggato,
dei nuovi nodi ticket all'interno di un apposito file XML omonimo. Qusto file sarà poi letto in una pagina specifica riservata agli amministratori del sito */

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

//Inizia la sessione
session_start();
$redirect = false;
$counter = 0;

//Controlla se il form è stato inviato
if (isset($_POST["invioRecovery"])) {
    $controller = xmlPointer("XML/Recovery.xml"); //Puntatore al file XML dei ticket

    foreach ($controller as $t) {
        if ($t->getElementsByTagName('text')[0]->textContent == $_POST["recoveryEmail"]) {
            // Se l'email è già presente, non creare un nuovo ticket
            $counter++;
        }
    }

    if ($counter < 3) {
         // Carica il documento XML
    $doc = getDoc("XML/Recovery.xml");
    $root = $doc->documentElement;

    // Calcolo del nuovo id_ticket (incrementale)
    $newId = 1;
    foreach ($root->getElementsByTagName("Recovery") as $t) {
        $id = (int)$t->getAttribute("id_recovery");
        if ($id >= $newId) {
            $newId = $id + 1;
        }
    }

    //Creazione del nodo <ticket>
    $ticket = $doc->createElement("Recovery");

    //Attributi del ticket
    $ticket->setAttribute("id_recovery", $newId);
    $ticket->setAttribute("data", date("d/m/Y"));
    $ticket->setAttribute("ore", date("H"));
    $ticket->setAttribute("minuti", date("i"));

    //Nodo testo con il messaggio dell’utente (sanificato)
    $testo = $doc->createElement(
        "text",
        htmlspecialchars($_POST["recoveryEmail"], ENT_QUOTES, "UTF-8")
    );

    //Aggiunge il testo al ticket
    $ticket->appendChild($testo);

    //Aggiunge il ticket alla root dell’XML
    $root->appendChild($ticket);

    //Salva il file XML aggiornato
    $doc->save("XML/Recovery.xml");

    $redirect = true;
    
}
else {
    $redirect = false;
    $errore = "Hai già inviato 3 segnalazioni con questa email, attendi la risposta degli amministratori prima di inviarne altre. Potrebbe essere che il tuo account non esista o sia stato eliminato. In caso di problemi contattare il Customer service attraverso la pagina Contact Us";
}
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
        <script type="text/javascript" src="Script/scriptMail.js"></script>

      
    </head>
    <body>  
        <?php  
            if($redirect){
                echo "<form id=\"redirectForm\" action=\"replyEmail.php\" method=\"post\">
                    <input type=\"hidden\" name=\"IDTicket\" value=\"$newId\" />
                    <input type=\"hidden\" name=\"Email\" value=\"".$_POST['recoveryEmail']."\" />
                </form>";
                echo "<script type=\"text/javascript\">
                    document.getElementById('redirectForm').submit()</script>";
            }
        
        ?>
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
                        <li><a href="login.php">Login </a></li>
                    </ul>
                </div>
                    
                    <form id="searchBar" onsubmit="return false;">
                        <input type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>

         
                <!-- se l'utente ha perso la password  puo inserire nel campo in basso la propria email per ricevere un link per resettare la password  -->
                <h2>
                    Hai bisogno di resettare la password? Don't worry, succede a tutti!
                </h2>
                <p>Inserisci l'email qui sotto:</p>
                <?php 
                if (!$redirect && isset($errore)) {
                    echo "<p class=\"errore\">$errore</p>";}
                        echo "<form class=\"recoveryForm\" action=\"Recovery.php\" method=\"post\" id=\"RecoveryEmail\"> 

                               <input type=\"text\" name=\"recoveryEmail\" placeholder=\"Inserisci la tua email\" required/>
                                
                            
                                <input type=\"submit\" name=\"invioRecovery\" value=\"Invia email di recupero\"/> 
                            </form>"; 
                                            
                    
                ?>
           

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
