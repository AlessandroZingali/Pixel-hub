<?php

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso


$game = $_GET["Giochi"] ?? "";

$rows = xmlPointer("XML/Giochi.xml");


$output = "";

// Inizializza una stringa vuota per contenere i risultati della ricerca

if (strlen($game) > 0) {
    // Cicla attraverso tutti i giochi nel file XML
    foreach ($rows as $row) {
        $idGioco = $row->getAttribute("id_gioco");
        $titolo = $row->getElementsByTagName("Titolo")[0]->nodeValue;
        if (stripos($titolo, $game) !== false) {
            $output .= "<p class=\"risultatoSearchGiochi\" onclick='insertIdGiochi(this, $idGioco)'>$titolo</p>";
        }
    }
}
//var_dump($rows);
// se non trova nulla mostra semplicemente nella finestra nessun risultato
echo $output === "" ? "Nessun risultato" : $output;
?>