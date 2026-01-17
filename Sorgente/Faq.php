<?php

/*Pagina delle Frequently Asked Question domande che possono essere visualizzate anche dai non iscritti al sito. 
Va da se che la pagina prendera il file XML omonimo e lo andrà a scansionare per prendere tutte le domande e risposte inserite
dagli amministatori del sito, per fare chiarezza su diversi punti. Usiamo un file XML per agevolare l'inserimento
di nuove FAQ nel tempo a venire.*/

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

$service = 0;
$utente = "";

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
                    <ul class ="submenu">
                         <?php
                        if($service == 1) echo "<li><a href=\"login.php\">Log out </a></li>";
                        else if($service == 0){
                            if(isset($_SESSION['userId']) && isset($_SESSION['generePreferito'])){
                                echo "<script>";
                                echo "sessionStorage.removeItem(\"idUser\");";
                                echo "sessionStorage.removeItem(\"genPref\");";
                                echo "</script>"; 
                            }
                            echo "<li><a href=\"login.php\">Log in </a></li>";
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

            <h1>F.A.Q.</h1>

            <table id="TabFaq">
                <tr>
                    
                    <th id="ColDom">
                        Domanda
                    </th>          
                   

                    
                    <th id="ColRis">
                        Risposta
                    </th> 
                    
                </tr>
                <!-- Ciclo PHP per l'estrazione delle domande e risposte dal file XML -->
                <?php
                    $elem= xmlPointer('XML/FAQ.xml'); //Richiama la funzione che restituisce il puntatore ai nodi figli della root del file XML
            
                    //Ciclo per l'estrazione delle domande e risposte
                    foreach($elem as $gioco){
                        
                        $domanda = $gioco->getElementsByTagName("Domanda")->item(0)->textContent;
                        $risposta = $gioco->getElementsByTagName("Risposta")->item(0)->textContent;

                        echo " <tr>
                        <td>$domanda</td>
                        <td>$risposta</td>
                        
                        
                        </tr>";

                    }  
                    ?> 
            </table>
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