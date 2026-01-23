<?php
    session_start();
    function calcoloEsperienza(){

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

            $db_name = "Database_Pixel_Hub";
            $table_users = "Tabella_Utenti";
            $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

            if (mysqli_connect_errno()){

                printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
            }
            $queryLogin = "SELECT * FROM $table_users WHERE (Email=".$_SESSION['Email']." OR Username='".$_SESSION['username']."');";
            $resultQ = mysqli_query($mysqliConnection, $queryLogin);
            $num = mysqli_num_rows($resultQ);

            if($num == 1){
                $row = mysqli_fetch_array($resultQ);
                $nuoviPixels = $_SESSION['Pixels'];
                $nuovaEsperienza = $_SESSION['Esperienza'];

                $updateQuery = "UPDATE $table_users SET Pixels = $nuoviPixels, Esperienza = $nuovaEsperienza WHERE id_utente = $idUtenteLoggato;";
                mysqli_query($mysqliConnection, $updateQuery);
            }
        }
    }
?>