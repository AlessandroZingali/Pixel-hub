<?php
// funzione che carica un file XML e lo restituisce come oggetto DOMDocument
function getDoc($file){
    $xmlString="";
                                
    foreach(file($file) as $node){ 
        $xmlString .= trim($node);
    }
    
    $doc= new DOMDocument();
    $doc->loadXML($xmlString);
    $doc->formatOutput = true;
    return $doc;
}
// funzione che associa direttamente la root di un documento XML 
function getRoot($file){
    $doc=getDoc($file);
    $root=$doc->documentElement;
    return $root;
}
// funzione semplice che restituisce il primo elemento figlio della radice di un file XML
function xmlPointer($file){
    $root = getRoot($file);
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