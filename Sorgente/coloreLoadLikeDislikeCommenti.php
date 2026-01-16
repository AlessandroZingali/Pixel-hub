<?php
    /* Questa API controlla la corrispondenza tra l'utente nella session, il gioco e i vari commenti, 
    cercando le corrisponenze il Like Commenti, cosi da colorare i pulsanti li e dislike corrispondenti a quelli a cui Lui ha messo mi piace e non mi piace*/

    require 'serverUtility.php'; // Includo il file di utilità per ricavare gli elemneti dei file XML (tramite DOMDocument)
    session_start(); 

    $tupleCom = []; // Array che conterrà i risultati da restituire in JSON

    // Verifica che l'utente sia loggato controllando la variabile di sessione 'tipoUtente'
    if (isset($_SESSION['tipoUtente'])) {
        header('Content-Type: application/json'); // Imposta il tipo di risposta a JSON

        $found = false; // Flag per verificare se sono stati trovati risultati 

        $root = getRoot('XML/LikeCommenti.xml'); // Nodo radice del documento XML

        // Se il documento ha nodi figli
        if ($root->hasChildNodes()) {
            $elem = $root->childNodes;

            // Scorre tutte le riferimenti ai commenti, prende tutte le tuple chiave (Id_Utente+Id_Commento+Id_Gioco)
            foreach ($elem as $rc) {
                $utente = $rc->getElementsByTagName("Id_Utente")->item(0)->textContent;
                $commento = $rc->getElementsByTagName("Id_Commento")->item(0)->textContent;
                $gioco = $rc->getElementsByTagName("Id_Gioco")->item(0)->textContent;

                // Controlla se l'utente e il gioco corrispondono a quelli richiesti
                //Prende l'idGioco dalla richiesta POST fatta in AJAX e riporta per quel utente quel gioco se ha una flag attiva
                if ($utente == $_SESSION['userId'] && $gioco == $_POST['idGioco']) {
                    $flagLike = $rc->getAttribute("flagLike");     // Like
                    $flagDislike = $rc->getAttribute("flagDislike"); // Dislike

                    $found = true; // Imposta il flag a true se viene trovata almeno una corrispondenza

                    // Aggiunge i dati del commento trovato all'array dei risultati
                    $tupleCom[] = [
                        'idCommento' => $commento,
                        'flagLike' => $flagLike,
                        'flagDislike' => $flagDislike
                    ];
                }
            }

            // Restituisce i risultati in formato JSON
            if ($found) echo json_encode($tupleCom);
            else echo ""; // Nessun risultato trovato, nessuna corrispondenza
        } 
        else echo ""; // Nessun nodo nel documento XML
    } 
    else echo ""; // Utente non loggato
?>
