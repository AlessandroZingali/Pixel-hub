<?php
session_start();
$tupleCom = [];
if(isset($_SESSION['tipoUtente'])){
    header('Content-Type: application/json');
    $found = false;
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
                        $found = true;
                        $tupleCom[] = [
                            'idCommento' => $commento,
                            'flagLike' => $flagLike,
                            'flagDislike' => $flagDislike
                        ];
                        }
                        
                    } 
                    if ($found) echo json_encode($tupleCom);  
                    else echo "";
            }
            else echo json_encode($tupleCom);
    }
    else echo json_encode($tupleCom);    
    

?>
