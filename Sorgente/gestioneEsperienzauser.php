
<?php
/* Questo script gestisce il calcolo del mod commenti e dell'esperienza dell'utente. I due si trovano nello steso file perchè l'exp
deriva anche dal mod commenti. Il mod commenti è calcolato attraverso uan media Bayesiana, 
rapportata ad un engament con il commento da parte degli alti utenti del sito, e sfruttando un divaitore in modo che il tasso del modificatore possa salire o scendere.
Abbiamo anche impostato dei bound per il tasso del modificatore, in modo che non possa variare oltre certi limiti e stabilizzare l'algoritmo */
require_once 'serverUtility.php';
require_once 'baseScontiUtente.php';
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
        $pointDB = new connectionDB(); // Creo un oggetto per la connessione al database, se necessario in futuro
        $pixelIniziali= $_SESSION['Pixels'];

        //var_dump($_SESSION['gameList']);
        //L'esperienza viene calcolata ogni nuovo acquisto, e si basa sui pixel guadagnati da quel acquisto, moltiplicati per il modificatore dei commenti. In questo modo, se un utente ha un buon mod commenti, guadagnerà più esperienza dai suoi acquisti, mentre se ha un mod commenti basso, guadagnerà meno esperienza. Questo sistema incentiva gli utenti a interagire positivamente con la community, in quanto un buon mod commenti non solo migliora la loro reputazione, ma aumenta anche i benefici derivanti dai loro acquisti.
        $listaGiochi_json = json_decode($_SESSION['gameList']);
        $logAcquisti = [];

        if(isset($_SESSION['userId'])){
            $idUtenteLoggato = $_SESSION['userId'];
            $pocketPixel = [];

            $elem = xmlPointer("XML/Commenti.xml");
            $modcommenti= calcoloModCommenti($elem); //Viene calcolato il MOD commenti (vedere sotto)
            $_SESSION["modCommenti"] = $modcommenti;

            //Viene calcolato il totale dei pixel guadagnati da un acquisto, sommando i pixel guadagnati da ogni gioco acquistato. 
            // I pixel guadagnati da ogni gioco sono calcolati moltiplicando il prezzo finale del gioco per 5, 
            // in modo da convertire il valore monetario dell'acquisto in pixel. Quindi, se un utente acquista un gioco che costa 10 euro, guadagnerà 50 pixel (10 euro * 5). 
            // Questo sistema permette di premiare gli utenti in base al valore dei loro acquisti, incentivandoli a spendere di più per ottenere più pixel e, di conseguenza, più esperienza.
            foreach($listaGiochi_json as $game){
                array_push($pocketPixel, ($game->prezzoFinale)*5);
                array_push($logAcquisti, new GameAcquistato($game->idGioco, $game->titolo, $game->prezzoFinale));
            }

            $pocketPixelTotale = array_sum($pocketPixel);//Viene sommato il totale dei pixel guadagnati da un acquisto, sommando i pixel guadagnati da ogni gioco acquistato.
            $esperienzaGuadagnata = ($pocketPixelTotale * $modcommenti);
            echo "pixel guadagnati " . $pocketPixelTotale;
            echo "mod commenti: " . $modcommenti;
            echo "Esperienza prima: " . $_SESSION['Esperienza'];
            $sqlConnection = $pointDB->connectDB();

            //Viene chiamato il DB per inserire la nuova esperienza e in caso modificatore il grado dell'utente.
            //Questo script corregge anche il grado portandolo ad un valore rapportato alla propria esperienza.
            if (mysqli_connect_errno()){

                printf("problemi di connessione : %s\n", mysqli_connect_error($sqlConnection));
            }
            $queryLogin = "SELECT * FROM ".$pointDB->getTableUsers()." WHERE (Email='".$_SESSION['Email']."' OR Username='".$_SESSION['userName']."');";
            $resultQ = mysqli_query($sqlConnection, $queryLogin);
            $num = mysqli_num_rows($resultQ);

            if($num == 1){
                $row = mysqli_fetch_array($resultQ);
                $nuoviPixels = $row["Pixels"]+(int)$pocketPixelTotale;
                $_SESSION['Pixels'] = $nuoviPixels;
                $nuovaEsperienza = $row['Esperienza'] + $esperienzaGuadagnata;
                $_SESSION['Esperienza'] = $nuovaEsperienza;
                $gradoAttuale = $row['Grado'];

                switch($gradoAttuale > 0){
                    case $gradoAttuale == 1:
                        $lowcap=-1;
                        $capEsperienza = 500;
                        break;
                    case $gradoAttuale == 2:
                        $lowcap=500;
                        $capEsperienza = 1000;
                        break;
                    case $gradoAttuale == 3:
                        $lowcap=1000;
                        $capEsperienza = 3000;
                        break;
                    case $gradoAttuale == 4:
                        $lowcap=3000;
                        $capEsperienza = 4000;
                        break;
                    case $gradoAttuale == 5:
                        $lowcap=4000;
                        $capEsperienza = 8000;
                        break;

                    case $gradoAttuale == 6:
                        $lowcap=8000;
                        break;
                }
                //Se la nuova esperienza supera il cap di esperienza per il grado attuale, l'utente viene promosso al grado successivo.
                if(isset($capEsperienza)){
                    if($nuovaEsperienza >= $capEsperienza){
                        $nuovoGrado = $gradoAttuale + 1;
                        $updateGradoQuery = "UPDATE ".$pointDB->getTableUsers()." SET Grado = $nuovoGrado WHERE ID = $idUtenteLoggato;";
                        mysqli_query($pointDB->connectDB(), $updateGradoQuery);
                    }
                }
                if($nuovaEsperienza < $lowcap && $gradoAttuale > 0){
                    //Se la nuova esperienza è inferiore al cap di esperienza per il grado attuale, ma l'utente ha già un grado, viene retrocesso al grado precedente.
                    $nuovoGrado = $gradoAttuale - 1;
                    $updateGradoQuery = "UPDATE ".$pointDB->getTableUsers()." SET Grado = $nuovoGrado WHERE ID = $idUtenteLoggato;";
                    mysqli_query($pointDB->connectDB(), $updateGradoQuery);

                }
                
                echo "Esperienza guadagnata: " . $esperienzaGuadagnata;
                
                //Viene aggiornato il DB con i nuovi pixel e la nuova esperienza, 
                // e viene loggato l'acquisto con il modificatore dei commenti usato, i pixel iniziali e i giochi acquistati.
                $updateQuery = "UPDATE ".$pointDB->getTableUsers()." SET Pixels = $nuoviPixels, Esperienza = $nuovaEsperienza WHERE ID = $idUtenteLoggato;";
                mysqli_query($pointDB->connectDB(), $updateQuery);

                //Viene loggato l'acquisto nel file XML apposito con il modificatore dei commenti usato, i pixel iniziali e i giochi acquistati.
                logAcquistiRegister($idUtenteLoggato, $logAcquisti, $modcommenti, $pixelIniziali); 
                $baseScanner = new scontiUtente($idUtenteLoggato);
                return;

            }
            
        }
    }
    
    function calcoloModCommenti($elem){
        if(isset($_SESSION['userId'])){
            $ElemSetting = xmlPointer("XML/SettingModCommenti.xml");

            $rangeMax = (float)$ElemSetting->item(0)->getAttribute("RangeMax");
            $forza = (int)$ElemSetting->item(0)->getAttribute("Forza");
    
            $idUtenteLoggato = $_SESSION['userId'];
            $commenti=$elem;
            $numeroCommenti = 0;
            $likeTotali = 0;
            $dislikeTotali = 0;
            
            //Il modificatore dei commenti viene calcolato attraverso una media Bayesiana, 
            // che tiene conto del numero di commenti fatti dall'utente, dei like e dislike ricevuti sui suoi commenti, 
            // e di un fattore di forza che stabilizza l'algoritmo.
            //Vengono anche calcolati l'engagement, in modo da rapportare il modificatore dei commenti 
            // all'interazione che stanno generando i commenti dell'utente sulla Piattaforma.
            foreach($commenti as $i){ //Prima prendiamo tutti i like ei dislike ricevuti in ogni commento.
                $commentoId = $i->getElementsByTagName("Commento"); 
                    foreach($commentoId as $c){
                        if($c->getAttribute("id_utente")==$idUtenteLoggato){
                        $numeroCommenti++;
                        $likeCommento = $c->getAttribute('like'); 
                        $dislikeCommento = $c->getAttribute('dislike');
                        $likeTotali+=$likeCommento;
                        $dislikeTotali+=$dislikeCommento;
                        }
                    }
            }

            if ($numeroCommenti==0) return 1.0; //Se l'utente non ha fatto commenti, il modificatore dei commenti è 1.0, in modo da non penalizzare o premiare un nuovo utente.
            
            //Il fattore di forza serve per creare una media pesata con un fattore di stabilizzazione, 
            // in modo che il modificatore dei commenti non possa variare troppo drasticamente con pochi commenti o pochi like/dislike.
            
            $AdLike = $likeTotali + $forza;
            $totaleInterazioni = $likeTotali + $dislikeTotali + (2*$forza);

            //il valore del ratio è impostato automaticamente alla meta in caso di assenza di interazioni (doppia sicurezza)
            if($totaleInterazioni > (2*$forza)) $ratio = $AdLike / $totaleInterazioni;
            else $ratio = 0.5;
            
            //Calcolo dell'engament "COMPLESSIVO" dei commenti dell'utente
            $totaleVoti = $likeTotali + $dislikeTotali;
            $Engagement = min(1.0, log10($totaleVoti + 1)/2);
            
            //Viene applicato il deviatore cosi da far diventare il radio un valore che può variare da -1 a 1, in modo da poterlo moltiplicare 
            // per l'engagement e il range massimo del modificatore dei commenti, in modo da ottenere una deviazione finale 
            // che può aumentare o diminuire il modificatore dei commenti in base all'engagement generato dai commenti dell'utente.
            $deviatore = ($ratio - 0.5)*2;
            $deviazioneFinale = $deviatore * $Engagement;

            $mod = 1.0 + ($deviazioneFinale * $rangeMax);

            //Infine si applica un bound al modificatore dei commenti, 
            // in modo che non possa variare oltre 0.75 e 1.25, stabilizzando l'algoritmo e evitando che utenti 
            // con pochi commenti o pochi like/dislike possano avere un modificatore dei commenti troppo alto o troppo basso.
            return round(max(0.75, min(1.25, $mod)), 2); 
        }
            
    }
    
    // Questa funzione logga gli acquisti degli utenti in un file XML apposito, 
    // registrando il modificatore dei commenti usato, i pixel iniziali e i giochi acquistati.
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

 