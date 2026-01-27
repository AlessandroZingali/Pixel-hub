<?php
/* Questo file contiene tutte le API, richiamte in AJAX traimte il file LIkeAndDislikeGestione.js per la gestione 
dei file Xml relativi ai Commenti e alla registrazione dei dislike e like per ogni commento in ogni gioco. In questi script si gestiscono vari casi, 
per l'inserimento o il deinserimento del like o del dislike*/

require 'serverUtility.php'; // Includo il file di utilità per ricavare gli elemneti dei file XML (tramite DOMDocument)

$route="0"; // Flag per decidere quale operazione eseguire: 0=nuovo like/dislike, 1=cambio dislike->like, 2=cambio like->dislike, 3=rimozione like, 4=rimozione dislike
// La route lavora insieme al tipo passato tramite POST (like o dislike), il caso 0 è in comune mentre poi i pari andranno con il dislike e i dispari con il like per gestire le varie operazioni

/* Il file Like Commenti tiene traccia dei like e dislike inseriti. Usa una chiave di 3 elementi, utente che ha messo like/dislike,
l'id gioco e l'id commento che indentificano il commento stesso. Inoltre avremo 2 flag come attributi, per inserire la tipologia di valutazione inserita */

        $root = getRoot('XML/LikeCommenti.xml');
        if($root->hasChildNodes()){
            $elem = $root->childNodes;
            foreach($elem as $rc){
                $utente = $rc->getElementsByTagName("Id_Utente")->item(0)->textContent;
                $commento = $rc->getElementsByTagName("Id_Commento")->item(0)->textContent;
                $gioco = $rc->getElementsByTagName("Id_Gioco")->item(0)->textContent;
                if ($utente == $_POST['idUtente'] && $commento == $_POST['idCommento'] && $gioco == $_POST['idGioco']) {
                    $flagLike = $rc->getAttribute("flagLike");
                    $flagDislike = $rc->getAttribute("flagDislike");
                    if ($flagDislike == "1" && $_POST['tipo'] == "like") $route = "1"; // ha messo like al posto di dislike
                    else if ($flagLike == "1" && $_POST['tipo'] == "dislike")   $route= "2"; // mette dislike al posto di like
                    else if ($flagLike == "1" && $_POST['tipo'] == "like") $route = "3"; // ha già messo like
                    else if ($flagDislike == "1" && $_POST['tipo'] == "dislike") $route= "4"; // ha già messo dislike
                    }
                }   
        }
// Gestione like
if ($_POST['tipo'] == "like" && $_SESSION['UserId']!=$commento) {
    if ($route == "0"){ // incremento like del commento in Commenti.xml se non ha ancora messo like o dislike
    
        $doc = getDoc('XML/Commenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $commento){
                if ($commento->getAttribute("id_commento") == $_POST['idCommento'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $like = $commento->getAttribute("like");
                    $like = $like + 1;
                    $commento->setAttribute("like", $like);
                    $doc->save("XML/Commenti.xml");
                }
            }
        }
        // Aggiunta riferimento in LikeCommenti.xml
        $doc = getDoc('XML/LikeCommenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        $nuovoLike = $doc->createElement("ref_Commento");
        $nuovoLike->setAttribute("flagLike", "1");
        $nuovoLike->setAttribute("flagDislike", "0");
        $nuovoLike->appendChild($doc->createElement("Id_Utente", $_POST['idUtente']));
        $nuovoLike->appendChild($doc->createElement("Id_Commento", $_POST['idCommento']));
        $nuovoLike->appendChild($doc->createElement("Id_Gioco", $_POST['idGioco']));
        $root->appendChild($nuovoLike);
        $doc->save("XML/LikeCommenti.xml");
        echo 'like'; // Risposta al client tramite echo per AJAX
    
    }
    
    if ($route == "1"){ // Cambio da dislike a like
  
        //Aggiorno Commenti.xml, sostituisco nei counter un dislike con un like            
        $doc = getDoc('XML/Commenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $commento){
                if ($commento->getAttribute("id_commento") == $_POST['idCommento'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $like = $commento->getAttribute("like");
                    $dislike = $commento->getAttribute("dislike");
                    $dislike = $dislike - 1;
                    $like = $like + 1;
                    $commento->setAttribute("like", $like);
                    $commento->setAttribute("dislike", $dislike);
                    $doc->save("XML/Commenti.xml");
                }
            }
        }

        // Aggiorno LikeCommenti.xml, cambio i flag da dislike a like
        $doc = getDoc('XML/LikeCommenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $rc){
            $idUtente = $rc->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idCommento = $rc->getElementsByTagName("Id_Commento")->item(0)->textContent;
            $idGioco = $rc->getElementsByTagName("Id_Gioco")->item(0)->textContent;
            if ($idUtente == $_POST['idUtente'] && $idCommento == $_POST['idCommento'] && $idGioco == $_POST['idGioco']) {
                $rc->setAttribute("flagLike", "1");
                $rc->setAttribute("flagDislike", "0");
                $doc->save("XML/LikeCommenti.xml");
            }
        }
        echo 'cambioDislikeLike'; // Risposta al client tramite echo per AJAX
    }

    if ($route == "3"){ // Rimozione like
        // Decremento like del commento in Commenti.xml
        $doc = getDoc('XML/Commenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $commento){
                if ($commento->getAttribute("id_commento") == $_POST['idCommento'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $like = $commento->getAttribute("like");
                    $like = $like - 1;
                    $commento->setAttribute("like", $like);
                    $doc->save("XML/Commenti.xml");
                }
            }
        }
        // Rimozione riferimento in LikeCommenti.xml
        $doc = getDoc('XML/LikeCommenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $rc){
            $idUtente = $rc->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idCommento = $rc->getElementsByTagName("Id_Commento")->item(0)->textContent;
            $idGioco = $rc->getElementsByTagName("Id_Gioco")->item(0)->textContent;
            if ($idUtente == $_POST['idUtente'] && $idCommento == $_POST['idCommento'] && $idGioco == $_POST['idGioco']) {
                $parent = $rc->parentNode;
                $parent->removeChild($rc);
                $doc->save("XML/LikeCommenti.xml");
        }
    }

    echo 'rimozioneLike';
    }
}

// Gestione dislike
if ($_POST['tipo'] == "dislike" && $_SESSION['UserId']!=$commento) {
    
    if ($route == "0"){
        // incremento dislike del commento in Commenti.xml se non ha ancora messo like o dislike
        $doc = getDoc('XML/Commenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $commento){
                if ($commento->getAttribute("id_commento") == $_POST['idCommento'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $dislike = $commento->getAttribute("dislike");
                    $dislike = $dislike + 1;
                    $commento->setAttribute("dislike", $dislike);
                    $doc->save("XML/Commenti.xml");
                }
            }
        }
        //Aggiunta riferimento in LikeCommenti.xml
        $doc = getDoc('XML/LikeCommenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        $nuovoLike = $doc->createElement("ref_Commento");
        $nuovoLike->setAttribute("flagLike", "0");
        $nuovoLike->setAttribute("flagDislike", "1");
        $nuovoLike->appendChild($doc->createElement("Id_Utente", $_POST['idUtente']));
        $nuovoLike->appendChild($doc->createElement("Id_Commento", $_POST['idCommento']));
        $nuovoLike->appendChild($doc->createElement("Id_Gioco", $_POST['idGioco']));
        $root->appendChild($nuovoLike);
        $doc->save("XML/LikeCommenti.xml");

        echo 'dislike'; // Risposta al client tramite echo per AJAX
    }
    else if ($route == "2"){ // cambio da like a dislike
        // Aggiorno Commenti.xml, sostituisco nei counter un like con un dislike
        $doc = getDoc('XML/Commenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $commento){
                if ($commento->getAttribute("id_commento") == $_POST['idCommento'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $dislike = $commento->getAttribute("dislike");
                    $dislike = $dislike + 1;
                    $like = $commento->getAttribute("like");
                    $like = $like - 1;
                    $commento->setAttribute("like", $like);
                    $commento->setAttribute("dislike", $dislike);
                    $doc->save("XML/Commenti.xml");
                }
            }
        }
        // Aggiorno LikeCommenti.xml, cambio i flag da like a dislike
        $doc = getDoc('XML/LikeCommenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $rc){
            $idUtente = $rc->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idCommento = $rc->getElementsByTagName("Id_Commento")->item(0)->textContent;
            $idGioco = $rc->getElementsByTagName("Id_Gioco")->item(0)->textContent;
            if ($idUtente == $_POST['idUtente'] && $idCommento == $_POST['idCommento'] && $idGioco == $_POST['idGioco']) {
                $rc->setAttribute("flagLike", "0");
                $rc->setAttribute("flagDislike", "1");
                $doc->save("XML/LikeCommenti.xml");
            }
        }
        echo 'cambioLikeDislike'; // Risposta al client tramite echo per AJAX
    }

    else if($route == "4"){ // Rimozione dislike
        // Decremento dislike del commento in Commenti.xml
        $doc = getDoc('XML/Commenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $commento){
                if ($commento->getAttribute("id_commento") == $_POST['idCommento'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $dislike = $commento->getAttribute("dislike");
                    $dislike = $dislike - 1;
                    $commento->setAttribute("dislike", $dislike);
                    $doc->save("XML/Commenti.xml");
                }
            }
        }
        
        // Rimozione riferimento in LikeCommenti.xml
        $doc = getDoc('XML/LikeCommenti.xml');
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $rc){
            $idUtente = $rc->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idCommento = $rc->getElementsByTagName("Id_Commento")->item(0)->textContent;
            $idGioco = $rc->getElementsByTagName("Id_Gioco")->item(0)->textContent;
            if ($idUtente == $_POST['idUtente'] && $idCommento == $_POST['idCommento'] && $idGioco == $_POST['idGioco']) {
                $parent = $rc->parentNode;
                $parent->removeChild($rc);
                $doc->save("XML/LikeCommenti.xml");
            }
        }
        echo 'rimozioneDislike'; // Risposta al client tramite echo per AJAX
    }
    
}

?>