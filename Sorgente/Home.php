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

        <!-- " ?v=1 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/Home.css?v=1" /> 
        <script type="text/javascript" src="Script/GameTableGestione.js?v=1">  </script>

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
                <ul>
                    <?php
                    if($service == 0) echo "<li><a href=\"login.php\">Log in </a></li>";
                    else if($service == 1) echo "<li><a href=\"login.php\">Log out </a></li>";
                    ?>
                    
                    <li><a href="home.php">Home</a></li>
                    <li><a href="carrello.html">Carrello </a></li>
                    <li><a href="catalogo.html">Catalogo </a></li>
                   <!-- <li><a href="Creadatabasepixelhub.php">data</a></li> -->
                    <?php 
                    if($service == 1) echo "<li><a href=\"profilo.php\">Profilo di $utente </a></li>";
                    ?>
                </ul>

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
                        <input type=\"button\" value=\"<\" id=\"scorriavanti\" onclick=\"sliderTable('GameTable0', 'back')\" />
                    </div> <!-- < -->
                    <div>
                        <table id=\"GameTable0\">
                    
                    
                        <th> Hey $utente ! Guarda questi ".$_SESSION['generePreferito']."   </th> ";
                        
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
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                                <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" >
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
                        
                        <input type="button" value="<" id="scorriindietro" onclick="sliderTable('GameTable1', 'back')" />
                        
                    </div>
                    <div>
                    
                        <table id="GameTable1">
                        
                            <th>I piu' Popolari</th>
                            
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
                                            $valGiocoGrezzo=$gioco->getElementsByTagName("MediaRecensioniUtenti")->item(0)->textContent;
                                            $valGioco = (float) $valGiocoGrezzo;
                                            if($valGioco>=80){
                                                $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                
                                                $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                                echo "<td>";
                                                echo "<div class= \" GameCard \"> 
                                                        <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" >
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
                        <input type="button" value="<" id="scorriavanti" onclick="sliderTable('GameTable2', 'back')" />
                    </div> <!-- < -->
                    <div>
                        <table id="GameTable2">
                    
                    
                        
                            <th>Ultimi Giochi aggiunti</th>
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

                              
                        
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                                <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" >
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
                        <input type="button" value="<" id="scorriavanti" onclick="sliderTable('GameTable2', 'back')" />
                    </div> <!-- < -->
                    <div>
                        <table id="GameTable3">
                    
                    
                        
                            <th>Sparatutto</th>
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
                                        $titoloGioco=$gioco->getElementsbyTagName("Titolo")->item(0)->textContent;
                                        $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                        echo "<td>";
                                        echo "<div class= \" GameCard \"> 
                                                <img src=\"$immagine\" alt=\"GameImage\" title=\"$titoloGioco\" class=\"productimage\" >
                                            </div> 
                                            </td>";
                                                
                                        }
                                            
                                    }   
                                    echo "</tr>";    
                                    
                            ?> 
                            

                        </table>
                    </div><!--tab -->
                    <div>
                        <input type="button" value=">" id="scorriavanti" onclick="sliderTable('GameTable2', 'forward')" />
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