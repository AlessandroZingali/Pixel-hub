
<?php

require_once 'serverUtility.php';
    class GameAcquistato{
        public $titolo;
        public $id_gioco;
        public $prezzoFinale;

        function __construct($id_gioco, $titolo, $prezzoFinale){
            $this->titolo = $titolo;
            $this->id_gioco = $id_gioco;
            $this->prezzoFinale = $prezzoFinale;
        }
    }


    session_start();

    

    function calcoloEsperienza(){
        $table_users = "Tabella_Utenti";
        $pixelIniziali= $_SESSION['Pixels'];

        //var_dump($_SESSION['gameList']);
        $listaGiochi_json = json_decode($_SESSION['gameList']);
        $logAcquisti = [];

        if(isset($_SESSION['userId'])){
            $idUtenteLoggato = $_SESSION['userId'];
            $pocketPixel = [];

            $elem = xmlPointer("XML/Commenti.xml");
            calcoloModCommenti($elem);
            if($_SESSION['modCommenti'] == 0) $modcommenti = 1/100;
            else $modcommenti = $_SESSION['modCommenti']/100;
            
            foreach($listaGiochi_json as $game){
                array_push($pocketPixel, ($game->prezzoFinale)*5);
                array_push($logAcquisti, new GameAcquistato($game->idGioco, $game->titolo, $game->prezzoFinale));
            }

            $pocketPixelTotale = array_sum($pocketPixel);
            $esperienzaGuadagnata = ($pocketPixelTotale * $modcommenti);
            echo "pixel guadagnati " . $pocketPixelTotale;
            echo "mod commenti: " . $modcommenti;
            echo "Esperienza prima: " . $_SESSION['Esperienza'];
            $_SESSION['Pixels'] = $_SESSION['Pixels'] + (int)$pocketPixelTotale;
            $_SESSION['Esperienza'] = $_SESSION['Esperienza'] + $esperienzaGuadagnata;

            connectDB();

            if (mysqli_connect_errno()){

                printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
            }
            $queryLogin = "SELECT * FROM $table_users WHERE (Email='".$_SESSION['Email']."' OR Username='".$_SESSION['userName']."');";
            $resultQ = mysqli_query(connectDB(), $queryLogin);
            $num = mysqli_num_rows($resultQ);

            if($num == 1){
                $row = mysqli_fetch_array($resultQ);
                $nuoviPixels = $_SESSION['Pixels'];
                $nuovaEsperienza = $_SESSION['Esperienza'];
                $gradoAttuale = $row['Grado'];

                switch($gradoAttuale > 0){
                    case $gradoAttuale= 1:
                        $capEsperienza = 500;
                        break;
                    case $gradoAttuale = 2:
                        $capEsperienza = 1000;
                        break;
                    case $gradoAttuale = 3:
                        $capEsperienza = 3000;
                        break;
                    case $gradoAttuale = 4:
                        $capEsperienza = 5000;
                        break;
                    case $gradoAttuale = 5:
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
                var_dump($_SESSION['Esperienza']);
                echo "Esperienza guadagnata: " . $esperienzaGuadagnata;
                $updateQuery = "UPDATE $table_users SET Pixels = $nuoviPixels, Esperienza = $nuovaEsperienza WHERE ID = $idUtenteLoggato;";
                mysqli_query(connectDB(), $updateQuery);
                logAcquistiRegister($idUtenteLoggato, $logAcquisti, $modcommenti, $pixelIniziali);
                return;
            
        }
    }
    
    function calcoloModCommenti($elem){
        if(isset($_SESSION['userId'])){
            $idUtenteLoggato = $_SESSION['userId'];
            $rapporti = [];

           

            foreach($elem as $i){
                $commentoId = $i->getElementsByTagName("Commento"); 
                foreach($commentoId as $c){
                 if($c->getAttribute("id_utente")==$idUtenteLoggato){
                    $likeCommento = $c->getAttribute('like'); 
                    $dislikeCommento = $c->getAttribute('dislike');
                    if($dislikeCommento==0 || $likeCommento==0){
                        $base = 1;
                    }
                    else $base = ($likeCommento/$dislikeCommento);
                    array_push($rapporti, $base);
                 }
                }
                }
                $sommatoria = array_sum($rapporti);


                return $sommatoria;
            }
            
        }
    

    function logAcquistiRegister($idUtente, $logAcquisti, $modificatore, $pixelIniziali){

        $doc=getDoc('XML/LogTransazioniGiochi.xml');

        $root =  $doc->documentElement;
        $elem = $root->childNodes;
        if($root->hasChildNodes()){
            $num = $root->childNodes->length;
            $nuovoIdTransizione = $num + 1;
        }
        else{
            $nuovoIdTransizione = 1;
        }

        $nuovoLog = $doc->createElement("Transazione");
        
        $nuovoLog->setAttribute("IDTransazione",$nuovoIdTransizione);
        $nuovoLog->setAttribute("ModCommentiUsato",$modificatore);
        $nuovoLog->setAttribute("IDGiocatore", $idUtente);
        $nuovoLog->setAttribute("DataOra", date("d/m/y H:i"));
        $nuovoLog->setAttribute("PixelIniziali", $pixelIniziali);

        

        foreach($logAcquisti as $acquisto){
            $GiocoInLog = $doc->createElement("Gioco");

  
            $idGioco = $doc->createElement("IDGioco", $acquisto->id_gioco);
            $titolo = $doc->createElement("Titolo", $acquisto->titolo);
            $importoSpeso = $doc->createElement("Importo", $acquisto->prezzoFinale);

            $GiocoInLog->appendChild($titolo);
            $GiocoInLog->appendChild($idGioco);
            $GiocoInLog->appendChild($importoSpeso);

            $nuovoLog->appendChild($GiocoInLog);
            
        }
        $root->appendChild($nuovoLog);
        $doc->save('XML/LogTransazioniGiochi.xml');
        return;
    }
?>

 