<?php
    /* Questa API controlla la corrispondenza tra l'utente nella session, il gioco e le varie recensioni, 
    cercando le corrisponenze il Like Recensioni, cosi da colorare i pulsanti li e dislike corrispondenti a quelli a cui Lui ha messo mi piace e non mi piace*/

    require 'serverUtility.php'; // Includo il file di utilità per ricavare gli elemneti dei file XML (tramite DOMDocument)
    session_start(); 

    $tupleRec = []; // Array che conterrà i risultati da restituire in JSON

    // Verifica che l'utente sia loggato controllando la variabile di sessione 'tipoUtente'
    if (isset($_SESSION['tipoUtente'])) {
        header('Content-Type: application/json'); // Imposta la risposta in formato JSON

        $found = false; // Flag per capire se sono stati trovati risultati
        
        $root = getRoot('XML/LikeRecensioni.xml'); // Ottiene il nodo radice del documento XML

        // Se il documento ha nodi figli
        if ($root->hasChildNodes()) {
            $elem = $root->childNodes;

            // Scorre tutte le riferimenti alla recensioni, prende tutte le tuple chiave (Id_Utente+Id_Recensione+Id_Gioco)
            foreach ($elem as $rr) {
                $utente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
                $Recensione = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
                $gioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;

                // Controlla se l'utente e il gioco corrispondono a quelli richiesti
                //Prende l'idGioco dalla richiesta POST fatta in AJAX e riporta per quel utente quel gioco se ha una flag attiva
                if ($utente == $_SESSION['userId'] && $gioco == $_POST['idGioco']) { 
                    $flagLike = $rr->getAttribute("flagLike");     // Like
                    $flagDislike = $rr->getAttribute("flagDislike"); // Dislike

                    $found = true; // Imposta il flag a true se trova almeno una corrispondenza

                    // Aggiunge i dati della recensione trovata all'array dei risultati
                    $tupleRec[] = [
                        'idRecensione' => $Recensione,
                        'flagLike' => $flagLike,
                        'flagDislike' => $flagDislike
                    ];
                }
            }

            // Se sono stati trovati risultati, li restituisce in JSON
            if ($found) echo json_encode($tupleRec);
            else echo ""; // Nessun risultato trovato, nessuna corrispondenza
        } 
        else echo ""; // Nessun nodo nel documento XML
        
    } 
    else echo ""; // Utente non loggato
?>