<?php
$service = 0;
$utente = "";

    if(isset($_POST["invioRichiesta"])){

        $xmlString="";
                                
        foreach(file("XML/Ticket.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
            foreach($elem as $i){
                if($i->getAttribute("id_ticket")==$idTicket){
                    $ticket = (int)$i->getAttribute("id_ticket");
                    break;
                }
            }
        if($ticket == 0){
            $newId = 1;

            $gioco=$doc->createElement("Gioco");
            $gioco->setAttribute("id_gioco", $idGioco);
            $ticket = $doc->createElement("ticket");

            $testo = $doc->createElement("text", htmlspecialchars($_POST["ticketUtente"]));

            $ticket->setAttribute("id_ticket", $newId);
            $ticket->setAttribute("id_utente", $_SESSION["userId"]);
            $ticket->setAttribute("data", date("d/m/Y"));
            $ticket->setAttribute("ore", date("H"));
            $ticket->setAttribute("minuti", date("i"));



            $ticket->appendChild($testo);
            $ticket->appendChild($ticket);
            $root->appendChild($ticket);
        }
        else{
            
            if($gioco->hasChildNodes()){
                
                $lastticket = $gioco->firstChild;
                $newId = (intval($lastticket->getAttribute("id_ticket")));

                $newId += 1;
                $ticket = $doc->createElement("ticket");

                $testo = $doc->createElement("text", htmlspecialchars($_POST["ticketUtente"]));

                $ticket->setAttribute("id_ticket", $newId);
                $ticket->setAttribute("id_utente", $_SESSION["userId"]);
                $ticket->setAttribute("data", date("d/m/Y"));
                $ticket->setAttribute("ore", date("H"));
                $ticket->setAttribute("minuti", date("i"));

                $ticket->appendChild($testo);
                $gioco->insertBefore($ticket, $lastticket);
           }
            else { 
                $newId = 1;

                $ticket = $doc->createElement("ticket");

                $testo = $doc->createElement("text", htmlspecialchars($_POST["ticketUtente"]));

                $ticket->setAttribute("id_ticket", $newId);
                $ticket->setAttribute("id_utente", $_SESSION["userId"]);
                $ticket->setAttribute("data", date("d/m/Y"));
                $ticket->setAttribute("ore", date("H"));
                $ticket->setAttribute("minuti", date("i"));


                $ticket->appendChild($testo);
                $gioco->appendChild($ticket);

            }
        }
        
        $doc->save("XML/Ticket.xml");
        alert("Richiesta mandata con successo!");
        header("Location: Homepage.php");
    }

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
        <title>Pixel Hub - Catalogo</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/Contact.css?v=3" />
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
                    <button class="botMenu"><img src="Stile/iconamenu.png" alt=""></button>
                    <ul class ="submenu">
                        <?php
                        if($service == 0) echo "<li><a href=\"login.php\">Log in </a></li>";
                        else if($service == 1){

                            echo "<li><a href=\"login.php\">Log out </a></li>";
                        }
                        ?>
                        
                        <li><a href="Homepage.php">Home</a></li>
                        <li><a href="carrello.html">Carrello </a></li>
                        <li><a href="catalogo.php">Catalogo </a></li>
                        <!-- <li><a href="Creadatabasepixelhub.php">data</a></li> -->
                        <?php 
                        if($service == 1) echo "<li><a href=\"Profilo.php\">Profilo di $utente </a></li>";
                        ?>
                    </ul>
                </div>

                    <form id="searchBar" onsubmit="return false;">
                        <input type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>

         

                <h1>
                    Contattaci:
                </h1>
                <p>Se hai avuto problemi con il sito nell'acquisto di un gioco o magari vuoi darci una dritta su come migliorare facci sapere scrivendolo qui sotto:</p>
                                    <?php 

                    if($service == 0) echo "<p>Devi essere loggato per mandare un ticket.</p>";
                    else{
                            echo "<form action=\"Contact.php?idUtente={$_SESSION['userId']}\" method=\"post\" id=\"formTicket\">

                                    <textarea name=\"AlertUtente\" placeholder=\"Scrivi il tuo messaggio qui\"></textarea>
                                    <br/>
                                    <input type=\"submit\" name=\"invioTicket\" value=\"Invia la segnalazione\"/> 
                                </form>"; 
                                            
                        }
                        ?>
           

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