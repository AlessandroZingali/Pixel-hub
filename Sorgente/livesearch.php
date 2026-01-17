<?php
/*Funzione  di ricerca di un gioco attraverso l'apposita barra di ricerca. Lo script fa una comparazione tra tutti i giochi in Giochi.xml mostrando in modo dinamico i risultati trovati.
La lista sottostante alla barra di ricerca si aggiorna in tempo reale, in contemporanea alla digitazione del titolo desiderato, passat o come parametro $game via GET.
Questo lavorera insieme allo script lato client Searchgame.js, che usando le tecnologie AJAX, cerca in diretta i giochi nell'elenco e li mostra in risultato sempre in maniera sincrona 
una volta trovato il titolo che si desidera si puo cliccare sopra e si andra alla gamepage associata*/

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

$root = getRoot('XML/Giochi.xml'); //Ottiene il nodo root del file XML contenente i giochi

$giochi = $root->getElementsByTagName("Gioco");
$game = $_GET["game"] ?? "";

$output = "";

// Inizializza una stringa vuota per contenere i risultati della ricerca

if (strlen($game) > 0) {
    // Cicla attraverso tutti i giochi nel file XML
    foreach ($giochi as $gioco) {
        $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
        $id = $gioco->getAttribute("id_gioco");
        // Confronta il titolo del gioco con la stringa di ricerca
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