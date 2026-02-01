
<?php

require_once 'serverUtility.php';
    session_start();
    function calcoloEsperienza(){
        $table_users = "Tabella_Utenti";

        //var_dump($_SESSION['gameList']);
        $listaGiochi_json = json_decode($_SESSION['gameList']);

        if(isset($_SESSION['userId'])){
            $idUtenteLoggato = $_SESSION['userId'];
            $rapporti = [];
            $pocketPixel = [];

            $elem = xmlPointer("XML/Commenti.xml");
            foreach($elem as $i){

                if($i->getAttribute("id_user")==$idUtenteLoggato){
                                        
                    $commentoId = $i->getElementsByTagName("Commento"); 
                    foreach($commentoId as $c){
                        $likeCommento = $c->getAttribute('like'); 
                        $dislikeCommento = $c->getAttribute('dislike');
                        array_push($rapporti, ($likeCommento/$dislikeCommento) );
                    }
                    $sommatoria = array_sum($rapporti);
                    $_SESSION['modCommenti'] = $sommatoria;
                    $modcommenti = $_SESSION['modCommenti']/100;
                }
            }

            foreach($listaGiochi_json as $game){
                array_push($pocketPixel, ($game->prezzoFinale)*5);
            }


            $esperienzaGuadagnata = ((int)$pocketPixel * (int)$modcommenti);

            $_SESSION['Pixels'] = $_SESSION['Pixels'] + (int)$pocketPixel;
            $_SESSION['Esperienza'] = $_SESSION['Esperienza'] + $esperienzaGuadagnata;

            connectDB();

            if (mysqli_connect_errno()){

                printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
            }
            $queryLogin = "SELECT * FROM $table_users WHERE (Email=".$_SESSION['Email']." OR Username='".$_SESSION['username']."');";
            $resultQ = mysqli_query(connectDB(), $queryLogin);
            $num = mysqli_num_rows($resultQ);

            if($num == 1){
                $row = mysqli_fetch_array($resultQ);
                $nuoviPixels = $_SESSION['Pixels'];
                $nuovaEsperienza = $_SESSION['Esperienza'];
                $gradoAttuale = $row['Grado'];

                switch($gradoAttuale > 0){
                    case $grado = 1:
                        $capEsperienza = 500;
                        break;
                    case $grado = 2:
                        $capEsperienza = 1000;
                        break;
                    case $grado = 3:
                        $capEsperienza = 3000;
                        break;
                    case $grado = 4:
                        $capEsperienza = 5000;
                        break;
                    case $grado = 5:
                        $capEsperienza = 10000;
                        break;
                    default:
                        $capEsperienza = 0;
                        break;
                }

                if($nuovaEsperienza >= $capEsperienza){
                    $nuovoGrado = $gradoAttuale + 1;
                    $updateGradoQuery = "UPDATE $table_users SET Grado = $nuovoGrado WHERE id_utente = $idUtenteLoggato;";
                    mysqli_query(connectDB(), $updateGradoQuery);
                }

            }

                $updateQuery = "UPDATE $table_users SET Pixels = $nuoviPixels, Esperienza = $nuovaEsperienza WHERE id_utente = $idUtenteLoggato;";
                mysqli_query(connectDB(), $updateQuery);
            
        }
    }
?>