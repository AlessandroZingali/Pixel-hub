<?php
error_reporting(E_ALL &~E_NOTICE);
/*File che presenta al suo interno funzioni utili al sito, come ad esempio ad evitare di riscrivere lo stasso codice più volte 
come la funzione che carica un file XML e lo restituisce come oggetto DOMDocument, o la funzione che restituisce la root di un file XML,
la funzione che restituisce il primo livello di nodi figli della root di un file XML o la funzione che setta 
il limite massimo di giochi da mostrare in ogni slider.*/
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
    $doc = getDoc($file);
    $root=$doc->documentElement;
    return $root;
}
// funzione semplice che restituisce il primo elemento figlio della radice di un file XML
function xmlPointer($file){
    $root = getRoot($file);
    $elem = $root->childNodes;

    return $elem;
}

//Imposto un limite massimo di giochi da mostrare in ogni slider
function setLimiteSlider($elem){
    if($elem->length<20){
        $limite=$elem->length;
    } else {
        $limite=23;
    }
    return $limite;
}

function connectDB(){
    //nome del database ovviamente dovra essere uguale in crea database 
    $db_name = "Database_Pixel_Hub";
    $table_users = "Tabella_Utenti";
    // inserire per una nuova installazione il nome utente per il mariaDB e la password
    $usernameDB = "Alessandro";
    $passwordDB = "belandi";

    $mysqliConnection = new mysqli("localhost", $usernameDB, $passwordDB, $db_name);
    return $mysqliConnection;
}


?>