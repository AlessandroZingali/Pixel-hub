<?php 

require_once 'serverUtility.php';

if(isset($_POST['idCommento']) && isset($_POST['idGioco'])){
    $doc = getDoc('XML/Commenti.xml');
    $root = $doc->documentElement;
    $giochi = $root->childNodes;
    foreach($giochi as $gioco){
        if($gioco->getAttribute('id_gioco') == $_POST['idGioco']){
            $commenti = $gioco->childNodes;
            foreach($commenti as $commento){
                if($commento->getAttribute('id_commento') == $_POST['idCommento']){
                    $numSegnalazioni = $commento->getAttribute('segnalazioni');
                    $commento->setAttribute('segnalazioni', $numSegnalazioni + 1);
                }
            }
        }
    }
    $doc->save('XML/Commenti.xml');
}
?>