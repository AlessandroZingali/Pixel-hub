<?php
$xml = new DOMDocument();
$xml->load("XML/Giochi.xml");

$giochi = $xml->getElementsByTagName("Gioco");
$q = $_GET["q"] ?? "";

$output = "";

if (strlen($q) > 0) {
    foreach ($giochi as $gioco) {
        $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
        $id = $gioco->getAttribute("id_gioco");

        if (stripos($titolo, $q) !== false) {
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