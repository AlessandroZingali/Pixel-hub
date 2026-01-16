<?php
/* Questo file contiene tutte le API, richiamte in AJAX traimte il file LIkeAndDislikeGestione.js per la gestione 
dei file Xml relativi alle Recensioni e alla registrazione dei dislike e like per ogni recensione in ogni gioco. In questi script si gestiscono vari casi, 
per l'inserimento o il deinserimento del like o del dislike*/

require 'serverUtility.php'; // Includo il file di utilità per ricavare gli elemneti dei file XML (tramite DOMDocument)

$route = "0"; // Variabile di controllo del flusso 

// Legge LikeRecensioni, che tiene traccia dei like/dislike nelle varie recensioni

$root = getRoot('XML/LikeRecensioni.xml');

// Scorre tutti i record di like/dislike già presenti
if ($root->hasChildNodes()) {
    $elem = $root->childNodes;
    foreach ($elem as $rr) {
        // Estrae la tupla chiave: Id_Utente + Id_Recensione + Id_Gioco
        $utente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
        $recensione = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
        $gioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;

        // Confronta la tupla con i dati ricevuti via POST
        if (
            $utente == $_POST['idUtente'] &&
            $recensione == $_POST['idRecensione'] &&
            $gioco == $_POST['idGioco']
        ) {
            // Legge i flag per capire se c’è già un like o un dislike
            $flagLike = $rr->getAttribute("flagLike");
            $flagDislike = $rr->getAttribute("flagDislike");

            // Determina il percorso logico in base al tipo di richiesta e allo stato del riferimento alla recensione in LikeGetsione
            if ($flagDislike == "1" && $_POST['tipo'] == "like") $route = "1"; // cambia da dislike a like
            else if ($flagLike == "1" && $_POST['tipo'] == "dislike") $route = "2"; // cambia da like a dislike
            else if ($flagLike == "1" && $_POST['tipo'] == "like") $route = "3"; // rimuove il like esistente
            else if ($flagDislike == "1" && $_POST['tipo'] == "dislike") $route = "4"; // rimuove il dislike esistente
        }
    }
}

// Gestione del caso: richiesta LIKE
if ($_POST['tipo'] == "like") {
    // Caso 0: non aveva ancora messo like/dislike
    if ($route == "0") {
        $doc = getDoc('XML/Recensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        // Cerca la recensione specifica (Id_Gioco + Id_Recensione)
        foreach ($elem as $gioco) {
            foreach ($gioco->childNodes as $recensione) {
                if (
                    $recensione->getAttribute("id_recensione") == $_POST['idRecensione'] &&
                    $gioco->getAttribute("id_gioco") == $_POST['idGioco']
                ) {
                    // Incrementa il like
                    $like = $recensione->getAttribute("like");
                    $like = $like + 1;
                    $recensione->setAttribute("like", $like);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }

        // Aggiunge il record di like in LikeRecensioni.xml
        $doc = getDoc('XML/LikeRecensioni.xml');
        $root = $doc->documentElement;

        // Crea il nuovo nodo di relazione (utente + gioco + recensione)
        $nuovoLike = $doc->createElement("ref_Recensioni");
        $nuovoLike->setAttribute("flagLike", "1");
        $nuovoLike->setAttribute("flagDislike", "0");
        $nuovoLike->appendChild($doc->createElement("Id_Utente", $_POST['idUtente']));
        $nuovoLike->appendChild($doc->createElement("Id_Recensione", $_POST['idRecensione']));
        $nuovoLike->appendChild($doc->createElement("Id_Gioco", $_POST['idGioco']));
        $root->appendChild($nuovoLike);
        $doc->save("XML/LikeRecensioni.xml");

        // Risposta verso AJAX
        echo 'like';
    }

    // Caso 1: aveva dislike, lo cambia in like
    if ($route == "1") {
        $doc = getDoc('XML/Recensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $gioco) {
            foreach ($gioco->childNodes as $recensione) {
                if (
                    $recensione->getAttribute("id_recensione") == $_POST['idRecensione'] &&
                    $gioco->getAttribute("id_gioco") == $_POST['idGioco']
                ) {
                    $like = $recensione->getAttribute("like");
                    $dislike = $recensione->getAttribute("dislike");
                    $dislike = $dislike - 1;
                    $like = $like + 1;
                    $recensione->setAttribute("like", $like);
                    $recensione->setAttribute("dislike", $dislike);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }

        // Aggiorna il record in LikeRecensioni.xml
        $doc = getDoc('XML/LikeRecensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $rr) {
            $idUtente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idRecensione = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
            $idGioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;

            if (
                $idUtente == $_POST['idUtente'] &&
                $idRecensione == $_POST['idRecensione'] &&
                $idGioco == $_POST['idGioco']
            ) {
                $rr->setAttribute("flagLike", "1");
                $rr->setAttribute("flagDislike", "0");
                $doc->save("XML/LikeRecensioni.xml");
            }
        }

        // Risposta verso AJAX
        echo 'cambioDislikeLike';
    }

    // Caso 3: aveva già like, lo rimuove
    if ($route == "3") {

        // Decrementa il contatore like
        $doc = getDoc('XML/Recensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $gioco) {
            foreach ($gioco->childNodes as $recensione) {
                if (
                    $recensione->getAttribute("id_recensione") == $_POST['idRecensione'] &&
                    $gioco->getAttribute("id_gioco") == $_POST['idGioco']
                ) {
                    $like = $recensione->getAttribute("like");
                    $like = $like - 1;
                    $recensione->setAttribute("like", $like);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }


        // Rimuove il record di like dal file LikeRecensioni.xml
        $doc = getDoc('XML/LikeRecensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $rr) {
            $idUtente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idRecensione = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
            $idGioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;

            if (
                $idUtente == $_POST['idUtente'] &&
                $idRecensione == $_POST['idRecensione'] &&
                $idGioco == $_POST['idGioco']
            ) {
                $parent = $rr->parentNode;
                $parent->removeChild($rr);
                $doc->save("XML/LikeRecensioni.xml");
            }
        }

        // Risposta verso AJAX
        echo 'rimozioneLike';
    }
}

// Gestione del caso: richiesta DISLIKE
if ($_POST['tipo'] == "dislike") {
    // Caso 0: non aveva ancora messo like/dislike
    if ($route == "0") {

        // Incrementa il contatore dislike nella recensione
        $doc = getDoc('XML/Recensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $gioco) {
            foreach ($gioco->childNodes as $recensione) {
                if (
                    $recensione->getAttribute("id_recensione") == $_POST['idRecensione'] &&
                    $gioco->getAttribute("id_gioco") == $_POST['idGioco']
                ) {
                    $dislike = $recensione->getAttribute("dislike");
                    $dislike = $dislike + 1;
                    $recensione->setAttribute("dislike", $dislike);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }

        // Aggiunge il record di dislike nel file LikeRecensioni.xml
        $doc = getDoc('XML/LikeRecensioni.xml');
        $root = $doc->documentElement;

        $nuovoLike = $doc->createElement("ref_Recensioni");
        $nuovoLike->setAttribute("flagLike", "0");
        $nuovoLike->setAttribute("flagDislike", "1");
        $nuovoLike->appendChild($doc->createElement("Id_Utente", $_POST['idUtente']));
        $nuovoLike->appendChild($doc->createElement("Id_Recensione", $_POST['idRecensione']));
        $nuovoLike->appendChild($doc->createElement("Id_Gioco", $_POST['idGioco']));
        $root->appendChild($nuovoLike);
        $doc->save("XML/LikeRecensioni.xml");

        // Risposta verso AJAX
        echo 'dislike';
    }
    // Caso 2: aveva like, lo cambia in dislike
    else if ($route == "2") {

        // Aggiorna contatori: +1 dislike, -1 like
        $doc = getDoc('XML/Recensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $gioco) {
            foreach ($gioco->childNodes as $recensione) {
                if (
                    $recensione->getAttribute("id_recensione") == $_POST['idRecensione'] &&
                    $gioco->getAttribute("id_gioco") == $_POST['idGioco']
                ) {
                    $dislike = $recensione->getAttribute("dislike");
                    $like = $recensione->getAttribute("like");
                    $dislike = $dislike + 1;
                    $like = $like - 1;
                    $recensione->setAttribute("like", $like);
                    $recensione->setAttribute("dislike", $dislike);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }

        // Aggiorna il record in LikeRecensioni.xml
        $doc = getDoc('XML/LikeRecensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $rr) {
            $idUtente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idRecensioni = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
            $idGioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;

            if (
                $idUtente == $_POST['idUtente'] &&
                $idRecensioni == $_POST['idRecensione'] &&
                $idGioco == $_POST['idGioco']
            ) {
                $rr->setAttribute("flagLike", "0");
                $rr->setAttribute("flagDislike", "1");
                $doc->save("XML/LikeRecensioni.xml");
            }
        }

        // Risposta verso AJAX
        echo 'cambioLikeDislike';
    }
    // Caso 4: aveva già dislike, lo rimuove
    else if ($route == "4") {

        // Decrementa il contatore dislike
        $doc = getDoc('XML/Recensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $gioco) {
            foreach ($gioco->childNodes as $recensione) {
                if (
                    $recensione->getAttribute("id_recensione") == $_POST['idRecensione'] &&
                    $gioco->getAttribute("id_gioco") == $_POST['idGioco']
                ) {
                    $dislike = $recensione->getAttribute("dislike");
                    $dislike = $dislike - 1;
                    $recensione->setAttribute("dislike", $dislike);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }

        // Rimuove il record di dislike da LikeRecensioni.xml
        $doc = getDoc('XML/LikeRecensioni.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;

        foreach ($elem as $rr) {
            $idUtente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idRecensione = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
            $idGioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;

            if (
                $idUtente == $_POST['idUtente'] &&
                $idRecensione == $_POST['idRecensione'] &&
                $idGioco == $_POST['idGioco']
            ) {
                $parent = $rr->parentNode;
                $parent->removeChild($rr);
                $doc->save("XML/LikeRecensioni.xml");
            }
        }

        // Risposta verso AJAX
        echo 'rimozioneDislike';
    }
}

?>