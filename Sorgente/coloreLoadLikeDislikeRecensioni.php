<?php
session_start();
$tupleRec = [];
if(isset($_SESSION['tipoUtente'])){
    header('Content-Type: application/json');
    $found = false;
    $xmlString="";

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
                    $Recensioni = $rr->getElementsByTagName("Id_Recensioni")->item(0)->textContent;
                    $gioco = $rr->getElementsByTagName("Id_Gioco")->item(0)->textContent;
                    if ($utente == $_SESSION['userId']  && $gioco == $_POST['idGioco']) {
                        $flagLike = $rr->getAttribute("flagLike");
                        $flagDislike = $rr->getAttribute("flagDislike");
                        $found = true;
                        $tupleRec[] = [
                            'idRecensione' => $Recensioni,
                            'flagLike' => $flagLike,
                            'flagDislike' => $flagDislike
                        ];
                        }
                        
                    } 
                    if ($found) json_encode($tupleRec);  
                    else echo json_encode($tupleRec);
            }
            else echo json_encode($tupleRec);
}
else echo json_encode($tupleRec);

?>