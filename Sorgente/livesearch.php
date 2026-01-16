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
// funzione che mostra quando si cerca un gioco nella barra di ricerca fa una comparazione tra tutti i giochi in Giochi.xml e mano a mano che si crea la stringa passata in $game
// si aggiorna anche la finestra risultato passata in output che mostra i vari giochi che hanno il titolo simile alla stringa inserita sotto alla barra di ricerca
// questo lavora insieme al Searchgame.js che usando una XMLHttprequest cerca in diretta i giochi nell'elenco e li mostra in risultato sempre in maniera sincrona una volta trovato il titolo che si desidera si puo cliccare sopra e si andra alla gamepage associata
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

// se non trova nulla mostra semplicemente nella finestra nessun risultato

echo $output === "" ? "Nessun risultato" : $output;
?>