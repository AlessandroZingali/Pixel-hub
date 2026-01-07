<?php 

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
        <title>Pixel Hub - Home</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/Home.css?v=3" /> 
        <script>
            <?php  
            if($service == 1 && isset($_SESSION['generePreferito'])){
                echo "sessionStorage.setItem(\"idUser\", \"".$_SESSION['userId']."\");";
                echo "sessionStorage.setItem(\"genPref\", \"".$_SESSION['generePreferito']."\");";
            }
            ?>
            
        </script>
        <script type="text/javascript" src="Script/GameTableGestione.js?v=3">  </script>
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

            
            
            <div id="TablesBoard"> <!--tag della della tabella maestra  -->

                <?php 
                if (isset($_SESSION['userId']) && isset($_SESSION['generePreferito']))
                {
                    echo "<div class=\"GameSlider\">
                    <div>
                        <input type=\"button\" value=\"<\" id=\"scorrindietro\" onclick=\"sliderTable('GameTable0', 'back')\" />
                    </div> <!-- < -->
                    <div>
                    <p class=\"titleTable\">Hey $utente ! Guarda questi ".$_SESSION['generePreferito']."</p>
                        <table id=\"GameTable0\">
                    
                    
                           ";
                        
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
                                
                                 echo "<tr>";
                                for($j=0; $j < $limite ; $j++){
                                    $gioco=$elem->item($j);
                                    $genereGioco=$gioco->getElementsByTagName("Generi")->item(0)->textContent;

                                   if($genereGioco == $_SESSION['generePreferito']){
                                        $idGioco = $gioco->getAttribute("id_gioco");
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        $prezzoGioco=$gioco->getElementsbyTagName("Prezzo")->item(0)->textContent;
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                             <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco'\" >   
                                             <div class=\"prezzo\"><p> $prezzoGioco €</p></div>
                                            </div> 
                                            </td>";
                                                
                                        }
                                            
                                    }   
                                    echo "</tr>";    
                                    
                            echo " </table>
                       
                    </div><!--tab -->
                    <div>
                        <input type=\"button\" value=\">\" id=\"scorriavanti\" onclick=\"sliderTable('GameTable0', 'forward')\" />
                    </div><!-- > -->
                </div>";
                }
                ?>
                
                <div class="GameSlider">
                    
                    <div>
                        
                        <input type="button" value="<" id="scorrindietro" onclick="sliderTable('GameTable1', 'back')" />
                        
                    </div>
                    <div>
                    <p class="titleTable">I piu' Popolari</p>
                        <table id="GameTable1">
                        
                            
                            
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
                                        
                                        echo "<tr>";
                                            
                                        for($j=0; $j < $limite ; $j++){
                                           
                                            $gioco=$elem->item($j);
                                            $idGioco = $gioco->getAttribute("id_gioco");
                                            $valGiocoGrezzo=$gioco->getElementsByTagName("MediaRecensioniUtenti")->item(0)->textContent;
                                            $valGioco = (float) $valGiocoGrezzo;
                                            if($valGioco>=80){
                                                $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                                $prezzoGioco=$gioco->getElementsbyTagName("Prezzo")->item(0)->textContent;
                                                
                                
                                                $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                                echo "<td>";
                                                echo "<div class= \" GameCard \"> 
                                                      <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco'\" >   
                                                      <div class=\"prezzo\"><p> $prezzoGioco €</p></div>
                                                    </div> 
                                                    </td>";
                                                        
                                                }
                                                    
                                            }   
                                            echo "</tr>";    
                                        
                                ?>   
                        
                        </table>

                    </div>

                    <div>
                        
                        <input type="button" value=">" id="scorriavanti" onclick="sliderTable('GameTable1', 'forward')" />
                    
                    </div>
                </div>

                 <div class="GameSlider">
                    <div>
                        <input type="button" value="<" id="scorrindietro" onclick="sliderTable('GameTable2', 'back')" />
                    </div> <!-- < -->
                    <div>
                         <p class="titleTable">Ultimi Giochi aggiunti</p>
                        <table id="GameTable2">
                        
                           
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
                                
                                echo "<tr>";// 12                           0
                                for($j=$elem->length-1; $j >=0 && $j > ($elem->length-1) - ($limite-1); $j--){
                                    $gioco=$elem->item($j);

                                        $idGioco = $gioco->getAttribute("id_gioco");
                        
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        $prezzoGioco=$gioco->getElementsbyTagName("Prezzo")->item(0)->textContent;
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                             <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco'\" > 
                                             <div class=\"prezzo\"><p>  $prezzoGioco € </p></div>
                                            </div> 
                                            </td>";
                                                
                                    
                                            
                                    }   
                                    echo "</tr>";    
                                    
                            ?> 
                            

                        </table>
                    </div><!--tab -->
                    <div>
                        <input type="button" value=">" id="scorriavanti" onclick="sliderTable('GameTable2', 'forward')" />
                    </div><!-- > -->
                </div>
                
                <div class="GameSlider">
                    <div>
                        <input type="button" value="<" id="scorrindietro" onclick="sliderTable('GameTable3', 'back')" />
                    </div> <!-- < -->
                    <div>
                        <p class="titleTable">Sparatutto</p>
                        <table id="GameTable3">
                    
                    
                        
                            
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
                                
                                echo "<tr>";
                                for($j=0; $j < $limite ; $j++){
                                    $gioco=$elem->item($j);
                                    $genereGioco=$gioco->getElementsByTagName("Generi")->item(0)->textContent;

                                   if($genereGioco=="Sparatutto"){
                                        $idGioco = $gioco->getAttribute("id_gioco");
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        $prezzoGioco=$gioco->getElementsbyTagName("Prezzo")->item(0)->textContent;
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                              <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco=$titoloGioco&idGioco=$idGioco'\" > 
                                               <div class=\"prezzo\"><p> $prezzoGioco €</p></div>
                                            </div> 
                                            </td>";
                                                
                                        }
                                            
                                    }   
                                    echo "</tr>";    
                                    
                            ?> 
                            

                        </table>
                    </div><!--tab -->
                    <div>
                        <input type="button" value=">" id="scorriavanti" onclick="sliderTable('GameTable3', 'forward')" />
                    </div><!-- > -->
                </div>


                

                  <!--  Pulsante per il debugg
                <input type="button" value="Reset Lista" onclick="localStorage.clear();"/> 
                -->
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