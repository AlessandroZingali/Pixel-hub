<?php
session_start();
header('Content-Type: application/json');
$xmlString="";

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
                if ($utente == $_SESSION['userId']  && $gioco == $_POST['idGioco']) {
                    $flagLike = $rc->getAttribute("flagLike");
                    $flagDislike = $rc->getAttribute("flagDislike");
                    }
                    $tuple[] = [
                        'idCommento' => $commento,
                        'flagLike' => $flagLike,
                        'flagDislike' => $flagDislike
                    ];
                } 
                echo json_encode($tuple);  
        }
