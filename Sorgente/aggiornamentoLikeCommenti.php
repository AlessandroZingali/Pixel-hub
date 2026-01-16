<?php
require 'serverUtility.php'; // Includo il file di utilità per ricavare gli elemneti dei file XML (tramite DOMDocument)
$xmlString="";
$route="0"; // F

foreach(file("XML/LikeCommenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
        $root = $doc->documentElement;
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

if ($_POST['tipo'] == "like") {
    if ($route == "0"){
        $xmlString="";
                                        
        foreach(file("XML/Commenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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

        $xmlString="";
                                        
        foreach(file("XML/LikeCommenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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
        echo 'like';
    
    }
    
    if ($route == "1"){
        $xmlString="";
                                        
        foreach(file("XML/Commenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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

        $xmlString="";
        foreach(file("XML/LikeCommenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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
        echo 'cambioDislikeLike';
    }

    if ($route == "3"){
        $xmlString="";
                                        
        foreach(file("XML/Commenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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
        
        $xmlString="";
        foreach(file("XML/LikeCommenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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


if ($_POST['tipo'] == "dislike") {
    
    if ($route == "0"){
        // incremento dislike del commento in Commenti.xml se non ha ancora messo like o dislike
        $xmlString="";
                                        
        foreach(file("XML/Commenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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

        $xmlString="";
                                        
        foreach(file("XML/LikeCommenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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

        echo 'dislike';
    }
    else if ($route == "2"){
        // cambio da like a dislike
        $xmlString="";
                                        
        foreach(file("XML/Commenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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

        $xmlString="";
        foreach(file("XML/LikeCommenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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
        echo 'cambioLikeDislike';
    }

    else if($route == "4"){
       
        // toglie dislike
        $xmlString="";
                                        
        foreach(file("XML/Commenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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
        

        $xmlString="";
        foreach(file("XML/LikeCommenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);
        $doc->formatOutput = true;
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
        echo 'rimozioneDislike';
    }
    
}

?>