<?php
//Funzione che restituisce il puntatore ai nodi figli della root di un file XML
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

//Imposto un limite massimo di giochi da mostrare in ogni slider
function setLimiteSlider($elem){
    if($elem->length<20){
        $limite=$elem->length;
    } else {
        $limite=20;
    }
    return $limite;
}
?>