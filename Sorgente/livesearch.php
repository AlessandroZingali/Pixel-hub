<?php
 $xmlString="";
                                
        foreach(file("XML/Giochi.xml") as $node){ 
            $xmlString .= trim($node);
        }
$xml = new DOMDocument();
$xml->loadXML($xmlString);
$xml->formatOutput = true;
$root = $xml->documentElement;
$giochi = $root->childNodes;

$giochi = $xml->getElementsByTagName("Gioco");
$game = $_GET["game"] ?? "";

$output = "";

if (strlen($game) > 0) {
    foreach ($giochi as $gioco) {
        $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
        $id = $gioco->getAttribute("id_gioco");

        if (stripos($titolo, $game) !== false) {
            $output .= "
            <div class='risultato'>
                
        
                <a href='Gamepage.php?titoloGioco=" . urlencode($titolo) . "&idGioco=$id'>
                    $titolo 
                </a>
            
            </div>";
        }
    }
}

echo $output === "" ? "Nessun risultato" : $output;
?>