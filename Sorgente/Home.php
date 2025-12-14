<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Home</title>
        <link rel="stylesheet" type="text/css" href="Stile/Home.css?v=1" />
                <script type="text/javascript" src="Script/GameTableGestione.js?v=1">  </script>

        <script type="type/javascript" src="Script/tabella.js?v=1"></script>
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
                    <li><a href="login.html">Log in </a></li>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="carrello.html">Carrello </a></li>
                    <li><a href="catalogo.html">Catalogo </a></li>
                </ul>

                <form action="" id="searchBar">
                    <input type="text" placeholder="Search" name="search"/>
                    <input type="submit" value="Cerca"/>
                </form>
            </div>
            
            <div id="TablesBoard">
                <table id="GameTable0">
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
                
                                $immagine=$gioco->getElementsbyTagName("Immagine")->item(0)->textContent;
                                echo "<td>";
                                echo "<div class= \" GameCard \"> 
                                        <img src=\"$immagine\" alt=\"GameImage\"  class=\"productimage\" >
                                    </div> 
                                    </td>";
                                        
                                }
                                    
                            }   
                            echo "</tr>";    
                    ?>
                </table>

                                <input type="button" value="Scorri indietro" id="scorriindietro" onclick="sliderTableBack('GameTable0')" />
                    <input type="button" value="Scorri avanti" id="scorriavanti" onclick="sliderTableForward('GameTable0')" />
    
                   
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