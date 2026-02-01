<?php 

require_once 'serverUtility.php';

if(isset($_POST['idOggetto']) && isset($_POST['idGioco'])){
    if(isset($_POST['type'])){
    if($_POST['type'] == 'com'){
        $doc = getDoc('XML/Commenti.xml');
        $id_declare = 'id_commento';
    }
    else if($_POST['type'] == 'rec'){
        $doc = getDoc('XML/Recensioni.xml');
        $id_declare = 'id_recensione';
    }
    $root = $doc->documentElement;
    $giochi = $root->childNodes;
    foreach($giochi as $gioco){
        if($gioco->getAttribute('id_gioco') == $_POST['idGioco']){
            $oggetti = $gioco->childNodes;
            foreach($oggetti as $oggetto){
                if($oggetto->getAttribute($id_declare) == $_POST['idOggetto']){
                    $numSegnalazioni = $oggetto->getAttribute('segnalazioni');
                    $oggetto->setAttribute('segnalazioni', $numSegnalazioni + 1);
                }
            }
        }
    }
     if($_POST['type'] == 'com') $doc->save('XML/Commenti.xml');
     else if($_POST['type'] == 'rec') $doc->save('XML/Recensioni.xml');
    }
}
?>