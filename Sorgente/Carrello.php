<?php
/* Pagina che mostrera il carrello ancora in fase di sviluppo;
L'idea è di un elenco dei prodotti acquistati con il nome  il prezzo di partenza e quello finale dato dopo gli sconti */
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
        <link rel="stylesheet" type="text/css" href="Stile/Carrello.css?v=3" />
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" /> 

      
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

            <h1>Il mio Carrello - Coming soon...</h1>

            <!-- CARRELLO ANCORA IN ALLESTIMENTO... CI VEDIAMO ALLA PARTE 3 -->
       

        <div id="footer">
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>