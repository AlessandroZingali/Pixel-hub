<?php
$xmlString="";
$route="0";

foreach(file("XML/LikeRecensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        if($root->hasChildNodes()){
            $elem = $root->childNodes;
            foreach($elem as $rr){
                $utente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
                $recensione = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
                $gioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;
                if ($utente == $_POST['idUtente'] && $recensione == $_POST['idRecensione'] && $gioco == $_POST['idGioco']) {
                    $flagLike = $rr->getAttribute("flagLike");
                    $flagDislike = $rr->getAttribute("flagDislike");
                    if ($flagDislike == "1" && $_POST['tipo'] == "like") $route = "1"; // ha messo like al posto di dislike
                    else if ($flagLike == "1" && $_POST['tipo'] == "dislike")   $route= "2"; // mette dislike al posto di like
                    else if ($flagLike == "1" && $_POST['tipo'] == "like") $route = "3"; // ha già messo like, lo sta togliendo
                    else if ($flagDislike == "1" && $_POST['tipo'] == "dislike") $route= "4"; // ha già messo dislike, lo sta togliendo
                    }
                }   
        }

if ($_POST['tipo'] == "like") {
    if ($route == "0"){
        $xmlString="";
                                        
        foreach(file("XML/Recensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $recensione){
                if ($recensione->getAttribute("id_recensione") == $_POST['idRecensione'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $like = $recensione->getAttribute("like");
                    $like = $like + 1;
                    $recensione->setAttribute("like", $like);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }

        $xmlString="";
                                        
        foreach(file("XML/LikeRecensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        $nuovoLike = $doc->createElement("ref_Recensioni");
        $nuovoLike->setAttribute("flagLike", "1");
        $nuovoLike->setAttribute("flagDislike", "0");
        $nuovoLike->appendChild($doc->createElement("Id_Utente", $_POST['idUtente']));
        $nuovoLike->appendChild($doc->createElement("Id_Recensione", $_POST['idRecensione']));
        $nuovoLike->appendChild($doc->createElement("Id_Gioco", $_POST['idGioco']));
        $root->appendChild($nuovoLike);
        $doc->save("XML/LikeRecensioni.xml");
        echo 'like';
    
    }
    
    if ($route == "1"){
        $xmlString="";
                                        
        foreach(file("XML/Recensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $recensione){
                if ($recensione->getAttribute("id_recensione") == $_POST['idRecensione'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
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

        $xmlString="";
        foreach(file("XML/LikeRecensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $rr){
            $idUtente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idRecensione = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
            $idGioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;
            if ($idUtente == $_POST['idUtente'] && $idRecensione == $_POST['idRecensione'] && $idGioco == $_POST['idGioco']) {
                $rr->setAttribute("flagLike", "1");
                $rr->setAttribute("flagDislike", "0");
                $doc->save("XML/LikeRecensioni.xml");
            }
        }
        echo 'cambioDislikeLike';
    }

    if ($route == "3"){
        $xmlString="";
                                        
        foreach(file("XML/Recensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $recensione){
                if ($recensione->getAttribute("id_recensione") == $_POST['idRecensione'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $like = $recensione->getAttribute("like");
                    $like = $like - 1;
                    $recensione->setAttribute("like", $like);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }
        
        $xmlString="";
        foreach(file("XML/LikeRecensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $rr){
            $idUtente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idRecensione= $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
            $idGioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;
            if ($idUtente == $_POST['idUtente'] && $idRecensione == $_POST['idRecensione'] && $idGioco == $_POST['idGioco']) {
                $parent = $rr->parentNode;
                $parent->removeChild($rr);
                $doc->save("XML/LikeRecensioni.xml");
        }
    }

    echo 'rimozioneLike';
    }
}


if ($_POST['tipo'] == "dislike") {
    
    if ($route == "0"){
        // incremento dislike del recensioni$recensione in Recensioni.xml se non ha ancora messo like o dislike
        $xmlString="";
                                        
        foreach(file("XML/Recensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $recensione){
                if ($recensione->getAttribute("id_recensione") == $_POST['idRecensione'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $dislike = $recensione->getAttribute("dislike");
                    $dislike = $dislike + 1;
                    $recensione->setAttribute("dislike", $dislike);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }

        $xmlString="";
                                        
        foreach(file("XML/LikeRecensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        $nuovoLike = $doc->createElement("ref_Recensioni");
        $nuovoLike->setAttribute("flagLike", "0");
        $nuovoLike->setAttribute("flagDislike", "1");
        $nuovoLike->appendChild($doc->createElement("Id_Utente", $_POST['idUtente']));
        $nuovoLike->appendChild($doc->createElement("Id_Recensione", $_POST['idRecensione']));
        $nuovoLike->appendChild($doc->createElement("Id_Gioco", $_POST['idGioco']));
        $root->appendChild($nuovoLike);
        $doc->save("XML/LikeRecensioni.xml");

        echo 'dislike';
    }
    else if ($route == "2"){
        // cambio da like a dislike
        $xmlString="";
                                        
        foreach(file("XML/Recensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $recensione){
                if ($recensione->getAttribute("id_recensione") == $_POST['idRecensione'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
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

        $xmlString="";
        foreach(file("XML/LikeRecensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $rr){
            $idUtente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idRecensioni= $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
            $idGioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;
            if ($idUtente == $_POST['idUtente'] && $idRecensioni == $_POST['idRecensione'] && $idGioco == $_POST['idGioco']) {
                $rr->setAttribute("flagLike", "0");
                $rr->setAttribute("flagDislike", "1");
                $doc->save("XML/LikeRecensioni.xml");
            }
        }
        echo 'cambioLikeDislike';
    }

    else if($route == "4"){
       
        // toglie dislike
        $xmlString="";
                                        
        foreach(file("XML/Recensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $gioco){
            foreach($gioco->childNodes as $recensione){
                if ($recensione->getAttribute("id_recensione") == $_POST['idRecensione'] && $gioco->getAttribute("id_gioco") == $_POST['idGioco']) {
                    $dislike = $recensione->getAttribute("dislike");
                    $dislike = $dislike - 1;
                    $recensione->setAttribute("dislike", $dislike);
                    $doc->save("XML/Recensioni.xml");
                }
            }
        }
        

        $xmlString="";
        foreach(file("XML/LikeRecensioni.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
        $elem = $root->childNodes;
        foreach($elem as $rr){
            $idUtente = $rr->getElementsByTagName("Id_Utente")->item(0)->textContent;
            $idRecensione = $rr->getElementsByTagName("Id_Recensione")->item(0)->textContent;
            $idGioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;
            if ($idUtente == $_POST['idUtente'] && $idRecensione == $_POST['idRecensione'] && $idGioco == $_POST['idGioco']) {
                $parent = $rr->parentNode;
                $parent->removeChild($rr);
                $doc->save("XML/LikeRecensioni.xml");
            }
        }
        echo 'rimozioneDislike';
    }
    
}

?>