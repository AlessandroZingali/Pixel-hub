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


?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Admin Board</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/Faq.css?v=3" />
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" /> 
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>

      
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
                        <td><textarea width='100px' height='200px'> </textarea> </td>
                        <td><input type='submit' value='Invia'></td>
                        
                        
                        </tr>";

                    }  
                    ?> 
            </table>

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
                        <input type='submit' value='Assegna Sconto'>
                      </form><br><hr><br>";
                    
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