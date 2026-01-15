<?php
function xmlPointer($file){
    $xmlString="";
                                
    foreach(file($file) as $node){ 
        $xmlString .= trim($node);
    }
    
    $doc= new DOMDocument();
    $doc->loadXML($xmlString);
    $root=$doc->documentElement;
    $elem=$root->childNodes;

    return $elem;
}
?>