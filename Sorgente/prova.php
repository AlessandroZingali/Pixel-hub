<?php 

$service = 0;
$utente = "";

session_start();
if(isset($_SESSION['userId'])){
    
    $utente = $_SESSION['userName'];
    $service = 1;
}

if(isset($_SESSION['NumCol'])){
    echo $_SESSION['NumCol'];
    if(isset($_SESSION['NumCol'])){ 
        $perPagina = 3 * $_SESSION['NumCol'];
        unset($_SESSION['NumCol']);
    }
   /* else{
        $perPagina = 3 * $_COOKIE['NumCol'];
        setcookie('NumCol', '', time() - 3600, '/');
    }*/

        
}
else{
    header("Location: initCatalogo.php");
}
$page = isset($_GET['page']) ? max(0, intval($_GET['page'])) : 0;
$offset = $page * $perPagina;


?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Home</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/catalogo.css?v=3" /> 
        <script type="text/javascript" src="Script/GameTableGestione.js?v=3"></script>
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
                            echo "<script>";
                            echo "sessionStorage.removeItem(\"idUser\");";
                            echo "sessionStorage.removeItem(\"genPref\");";
                            echo "</script>";
                            echo "<li><a href=\"login.php\">Log out </a></li>";
                        }
                        ?>
                        
                        <li><a href="Homepage.php">Home</a></li>
                        <li><a href="carrello.html">Carrello </a></li>
                        <li><a href="catalogo.php">Catalogo </a></li>
                        <!-- <li><a href="Creadatabasepixelhub.php">data</a></li> -->
                        <?php 
                        if($service == 1) echo "<li><a href=\"profilo.php\">Profilo di $utente </a></li>";
                        ?>
                    </ul>
                </div>

                <form action="" id="searchBar">
                    <input type="text" placeholder="Search" name="search"/>
                    <input type="submit" value="Cerca"/>
                </form>
            </div>

                <div class="GameSlider">

                    <div>
                         <p class="titleTable">Ultimi Giochi aggiunti</p>
                    
                        
                           
                            <?php
                                $xmlString="";
                                
                                foreach(file("XML/Giochi.xml") as $node){ 
                                    $xmlString .= trim($node);
                                }
                                
                                $doc= new DOMDocument();
                                $doc->loadXML($xmlString);
                                $root=$doc->documentElement;
                                $elem=$root->childNodes;
                                if($elem->length<15){
                                    $limite=$elem->length;
                                } else {
                                    $limite=15;
                                }
                                $totale = $elem->length;
                                $maxPagine = ceil($totale / $perPagina);
                            ?>



                   <table id="GameTable0">
                            <?php
                            $contatore = 0;
                            
                            for ($i = $offset ; $i < min($offset + 15, $totale); $i++) {

                                if ($contatore % 5 == 0) echo "<tr>";

                                $gioco = $elem->item($i);
                                $idGioco = $gioco->getAttribute("id_gioco");
                                $immagine = $gioco->getElementsByTagName("Immagine")->item(0)->textContent;
                                $prezzo = $gioco->getElementsByTagName("Prezzo")->item(0)->textContent;
                                $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;

                                echo "
                                <td>
                                    <div class='GameCard'>
                                        <img src='$immagine'
                                            title='$titolo'
                                            onclick=\"location.href='Gamepage.php?titoloGioco=$titolo&idGioco=$idGioco'\">
                                        <div class='prezzo'>$prezzo €</div>
                                    </div>
                                </td>";

                                if ($contatore % 5 == 4) echo "</tr>";

                                $contatore++;
                            }
                            ?>
                            </table>

                    <div class="paginazione">
                    <?php if ($page > 0): ?>
                        <a href="prova.php?page=<?= $page - 1 ?>">◀ Pagina precedente</a>
                    <?php endif; ?>

                    <?php if ($page < $maxPagine - 1): ?>
                        <a href="prova.php?page=<?= $page + 1 ?>">Pagina successiva ▶</a>
                    <?php endif; ?>
                </div>
                </div>                 
                                    
                    
                            



    
        <div id="footer">
            <ul>
                <li><a href="">Contact Us</a></li>
                <li><a href="">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>