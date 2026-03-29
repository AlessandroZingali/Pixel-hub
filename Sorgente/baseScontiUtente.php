<!-- 
1.  clienti che hanno speso N crediti finora, 
2.  clienti che hanno speso M crediti da una certa data, 
3.  clienti che hanno acquistato una certa offerta(giochi ListAdmin); 
4.  clienti che hanno una certa reputazione; 
5.  clienti che sono con noi da X mesi; 
6.  clienti che sono con noi da Y anni; 
7.  il gioco appartiene ad una certa casa di sviluppo 
8.  il gioco appartiene ad un certo genere (magari tra quelli più apprezzati dal cliente)
9.  sconto indipendente(esempio saldi invernali o festivi o altro)-->

<?php
    require_once 'serverUtility.php';

    class scontiAss{
        public $doc;
        public $root;
        public $elemAss;

        function __construct(){
            $this->doc = getDoc('XML/ScontiAssegnati.xml');
            $this->root = $this->doc->documentElement;
            $this->elemAss = $this->root->childNodes;
        }

        public function reset(){
            $this->doc = getDoc('XML/ScontiAssegnati.xml');
            $this->root = $this->doc->documentElement;
            $this->elemAss = $this->root->childNodes;
        }
        
        public function save(){$this->doc->save('XML/ScontiAssegnati.xml');}

    }

    class scontiUtente{
        private $idUtente;
        private $grado;
        private $sconti=[];

        function __construct($idUtente){
            $this->idUtente=$idUtente;

            $pointDB = new connectionDB();
            $mysqliConnection = $pointDB->connectDB();


            if (mysqli_connect_errno()){

                printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
            }

            $query = "SELECT * FROM {$pointDB->getTableUsers()} WHERE ID='$idUtente'";
            $resultQ = mysqli_query($mysqliConnection, $query);
            $num = mysqli_num_rows($resultQ);
            if($num == 1){
                $row=mysqli_fetch_array($resultQ);
                $this->grado = $row['Grado'];
            }

            //Ad ogni creazione dello scanner (quindi ad ogni reload della pagina dove è inserito) 
            // vengono ricalcolati gli sconti assegnati all'utente, 
            // in modo da essere sempre aggiornati in base alle azioni dell'utente stesso
            $this->scanner($idUtente);
            $elem = new scontiAss();
            foreach($elem->elemAss as $user){
                if($user->getAttribute('id_user')== $this->idUtente){
                    $ref = $user->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto');
                    foreach($ref as $scontoRef){
                        $sconto = $scontoRef->textContent;
                        array_push($this->sconti, (int)$sconto);
                    }
                }
            }

        }
        private function blacklistChecker($idUtente, $categoria){
            $assPointer = new scontiAss();
            $blacklistPointer = xmlPointer('XML/BlacklistSconti.xml');
            foreach($blacklistPointer as $utente){
                if($utente->getAttribute('id_user')== $idUtente){
                    foreach($utente->childNodes as $sconto){
                        if($sconto->textContent == $categoria){
                            $assPointer->reset();
                        
                            foreach($assPointer->elemAss as $u){
                                if($u->getAttribute('id_user')==$idUtente){
                                    foreach($u->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                        if($scontoRef->textContent==$categoria){
                                            $scontoDaEliminare = $scontoRef;
                                        }
                                    }
                                }
                            }
                            if(isset($scontoDaEliminare)){
                                $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                                $assPointer->save();
                            }
                            return true;
                        }
                    }
                }
            }
            return false;
        }
        private function scanner($idUtente){
            $elemUtenti = xmlPointer('XML/utenti.xml');
            $SettingSelector = getRoot('XML/SettingsSconti.xml');
            $assPointer = new scontiAss();
            $alreadyAssigned=false;


            // 1.  clienti che hanno speso N crediti finora,
            if(!($this->blacklistChecker($idUtente, 1))){
                
                $saldo = 0;
                $assPointer->reset();
                foreach($elemUtenti as $utente){
                    if($utente->getAttribute('id_user')== $idUtente){
                        if($utente->getElementsByTagName('listaGiochi')->length!=0){
                            $listaGiochi=$utente->getElementsByTagName('listaGiochi')[0]->getElementsByTagName('idGiocoPosseduto');
                        foreach($listaGiochi as $gioco){
                            $saldo+= (int)$gioco->getAttribute('spesa');
                        }    
                        }
                        
                    }
                }

                $SpesaMinima=$SettingSelector->getElementsByTagName('MinimiSpesi')->item(0)->textContent;
                if($saldo>=$SpesaMinima){
                        $alreadyAssigned=false;
                        
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                    if($scontoRef->textContent==1){
                                        $alreadyAssigned=true;
                                    }
                                }
                            }
                        }
                    $assPointer->reset();
                    foreach($assPointer->elemAss as $utente){
                        if($utente->getAttribute('id_user')==$idUtente){
                            $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                            if(!$alreadyAssigned){
                                $sconto=$assPointer->doc->createElement('Sconto', '1');
                                $listaSconti->appendChild($sconto);
                                $assPointer->save();
                            }
                        }
                    }
                }
                else{
                        $assPointer->reset();
                        
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                    if($scontoRef->textContent==1){
                                        $scontoDaEliminare = $scontoRef;
                                        $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                                        $assPointer->save();
                                    }
                                }
                            }
                        }
                        
                }
            }

            if(!($this->blacklistChecker($idUtente, 2))){
                    $saldo=0;
                    $alreadyAssigned=false;
                    $elemUtenti = xmlPointer('XML/utenti.xml');
                    $SettingSelector = getRoot('XML/SettingsSconti.xml');
                    $ValoreSpesa= $SettingSelector->getElementsByTagName('MinimiSpesiDa')->item(0)->getElementsByTagName('Valore')->item(0)->textContent;
                    $SpesaData = trim($SettingSelector->getElementsByTagName('MinimiSpesiDa')->item(0)->getElementsByTagName('DataInizio')->item(0)->textContent);
                    $giorno = substr($SpesaData, 0, 2);
                    $mese  = substr($SpesaData, 3, 2);
                    $anno  = substr($SpesaData, 6, 4);
                
                
                

                    foreach($elemUtenti as $utente){
                        if($utente->getAttribute('id_user')== $idUtente){
                            if($utente->getElementsByTagName('listaGiochi')->length!=0){
                                $listaGiochi=$utente->getElementsByTagName('listaGiochi')[0]->getElementsByTagName('idGiocoPosseduto');
                                foreach($listaGiochi as $gioco){
                                    
                                    $dataAcquisto=trim($gioco->getAttribute('data_acquisizione'));
                                    $giornoC = substr($dataAcquisto, 0, 2);
                                    $meseC  = substr($dataAcquisto, 3, 2);
                                    $annoC  = substr($dataAcquisto, 6, 4);
                                    
                                    if($annoC>=$anno){
                                        if($meseC>=$mese){
                                            if($giornoC>=$giorno){
                                                $saldo+= (int)$gioco->getAttribute('spesa');
                                            }
                                        }
                                    }    
                                }
                            }
                        }

                    }

                

                    if($saldo>=$ValoreSpesa){
                        $alreadyAssigned=false;
                        $assPointer->reset();
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                    if($scontoRef->textContent==2){
                                        $alreadyAssigned=true;
                                    }
                                }
                            }
                        }
                        $assPointer->reset();
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                                if(!$alreadyAssigned){
                                    $sconto=$assPointer->doc->createElement('Sconto', '2');
                                    $listaSconti->appendChild($sconto);
                                    $assPointer->save();
                                }
                            }
                        }
                }
                else{
                    $assPointer->reset();
                    
                    foreach($assPointer->elemAss as $utente){
                        if($utente->getAttribute('id_user')==$idUtente){
                            foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                if($scontoRef->textContent==2){
                                    $scontoDaEliminare = $scontoRef;
                                }
                            }
                        }
                    }
                    if(isset($scontoDaEliminare)){
                        if($scontoDaEliminare != null && $scontoDaEliminare->parentNode != null){
                            $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                            $assPointer->save();
                        }
                    }
                    
                }
            }
           
     
           //caso 3: clienti che hanno acquistato una certa offerta(giochi correlati)
           if(!($this->blacklistChecker($idUtente, 3))){
                $assPointer->reset();
                $alreadyAssigned=false;
                $listaGiochiUtente = [];
                $giochiAdmin = [];

                $SettingSelector = getRoot('XML/SettingsSconti.xml');
                $listaGiochiAdmin = $SettingSelector->getElementsByTagName('listaGiochiAdmin')->item(0)->getElementsByTagName('Gioco');

                foreach($listaGiochiAdmin as $gioco){
                    array_push($giochiAdmin, $gioco->textContent);
                }

                $utenti = xmlPointer('XML/utenti.xml');
                foreach($utenti as $utente){
                    $listaGiochi=$utente->getElementsByTagName('listaGiochi')[0]->getElementsByTagName('idGiocoPosseduto');
                    foreach($listaGiochi as $gioco){
                        if($utente->getAttribute('id_user')== $idUtente){
                            array_push($listaGiochiUtente, $gioco->textContent);
                        }
                    }
                }

                $intersezione = array_intersect($giochiAdmin, $listaGiochiUtente);
                if(!(empty($intersezione))){

                    foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                    if($scontoRef->textContent==3){
                                        $alreadyAssigned=true;
                                    }
                                }
                            }
                        }
                    $assPointer->reset();    
                    if(!$alreadyAssigned){
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                                $sconto=$assPointer->doc->createElement('Sconto', '3');
                                $listaSconti->appendChild($sconto);
                                $assPointer->save();
                            }
                        }
                    }
                }
                
                else{
    
                    foreach($assPointer->elemAss as $utente){
                        if($utente->getAttribute('id_user')==$idUtente){
                            foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                if($scontoRef->textContent==3){
                                    $scontoDaEliminare = $scontoRef;
                                }
                            }
                        }
                    }
                    if(isset($scontoDaEliminare)){ 
                        if($scontoDaEliminare != null && $scontoDaEliminare->parentNode != null){
                            $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                            $assPointer->save();
                        }
                    }
                }
                    

            }

            //    caso 4: clienti che hanno una certa reputazione(esperienza);
            if(!($this->blacklistChecker($idUtente, 4))){
                $elemUtenti = xmlPointer('XML/utenti.xml');
                $SettingSelector = getRoot('XML/SettingsSconti.xml');
                $ReputazioneMin = $SettingSelector->getElementsByTagName('ReputazioneMin')->item(0)->textContent;

                foreach($elemUtenti as $utente){
                    if($utente->getAttribute('id_user')== $idUtente){
                        $reputazioneUtente= ($_SESSION['Esperienza']);
                        
                        if((int)$reputazioneUtente>=(int)$ReputazioneMin){
                            $type = 4;
                            $assPointer->reset();
                            $alreadyAssigned=false;
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                        if($scontoRef->textContent==4){
                                            $alreadyAssigned=true;
                                        }
                                    }
                                }
                            }
                            $assPointer->reset();
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                                    if(!$alreadyAssigned){
                                        $sconto=$assPointer->doc->createElement('Sconto', $type);
                                        $listaSconti->appendChild($sconto);
                                        $assPointer->save();
                                    }
                                }
                            }
                        }
                        else{
                            $assPointer->reset();
                    
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                        if($scontoRef->textContent==4){
                                            $scontoDaEliminare = $scontoRef;
                                            $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                                            $assPointer->save();
                                        }
                                    }
                                }
                            } 
                        }

                    }

                }
            }

            // caso 5: clienti che sono con noi da X mesi && caso 6: ... da Y anni;
            
            $elemUtenti = xmlPointer('XML/utenti.xml');
            $SettingSelector = getRoot('XML/SettingsSconti.xml');

            $mesiMin = $SettingSelector->getElementsByTagName('MesiMin')->item(0)->textContent;
            $anniMin = $SettingSelector->getElementsByTagName('AnniMin')->item(0)->textContent;

            foreach($elemUtenti as $utente){
                if($utente->getAttribute('id_user')== $idUtente){
                    $dataIscrizioneUtente=trim($utente->getElementsByTagName('DataIscrizione')->item(0)->textContent);
                    $giornoC = substr($dataIscrizioneUtente, 0, 2);
                    $meseC  = substr($dataIscrizioneUtente, 3, 2);
                    $annoC  = substr($dataIscrizioneUtente, 6, 4);
                    $dataAttuale = date('d-m-Y');
                    $dataInizio = new DateTime($dataAttuale);
                    $dataEffettiva = new DateTime("$giornoC-$meseC-$annoC");
                    $intervallo = $dataEffettiva->diff($dataInizio);
                    $diffMesi = ($intervallo->y * 12) + $intervallo->m;
                    

                    if(!($this->blacklistChecker($idUtente, 5))){
                        if($diffMesi >= $mesiMin){
                            $assPointer->reset();
                            $alreadyAssignedTypeFive=false;
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                        if($scontoRef->textContent==5){
                                            $alreadyAssignedTypeFive=true;
                                        }
                                    }
                                }
                            }
                            $assPointer->reset();
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                                    if(!($alreadyAssignedTypeFive)){
                                        $sconto=$assPointer->doc->createElement('Sconto', 5);
                                        $listaSconti->appendChild($sconto);
                                        $assPointer->save();
                                    }
                                }
                            }
                        }
                        else{
                            $assPointer->reset();
                    
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                        if($scontoRef->textContent==5){
                                            $scontoDaEliminare = $scontoRef;
                                            $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                                            $assPointer->save();
                                        }
                                    }
                                }
                            } 
                        }
                    }
                    if(!($this->blacklistChecker($idUtente, 6))){
                        if($diffMesi >= (12*$anniMin)){
                            $assPointer->reset();
                            $alreadyAssignedTypeSix=false;
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                        if($scontoRef->textContent==6){
                                            $alreadyAssignedTypeSix=true;
                                        }
                                    }
                                }
                            }
                            $assPointer->reset();
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                                    if(!($alreadyAssignedTypeSix)){
                                        $sconto=$assPointer->doc->createElement('Sconto', 6);
                                        $listaSconti->appendChild($sconto);
                                        $assPointer->save();
                                    }
                                }
                            }
                        }
                        else{
                            $assPointer->reset();
                    
                            foreach($assPointer->elemAss as $utente){
                                if($utente->getAttribute('id_user')==$idUtente){
                                    foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                        if($scontoRef->textContent==6){
                                            $scontoDaEliminare = $scontoRef;
                                            $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                                            $assPointer->save();
                                        }
                                    }
                                }
                            } 
                        }
                    }
                }

            }

            // caso 7 il gioco appartiene ad una casa di sviluppo
            if(!($this->blacklistChecker($idUtente, 7))){
                $alreadyAssigned=false;
                $SettingSelector = getRoot('XML/SettingsSconti.xml');
                $casaSviluppo = $SettingSelector->getElementsByTagName('CasaSconto')->item(0)->textContent;
                $utenti = xmlPointer('XML/utenti.xml');
                foreach($utenti as $utente){
                        if($utente->getAttribute('id_user')== $idUtente){
                            if($utente->getElementsByTagName('CasaDiSviluppoPreferita')->length!=0){
                                $casaPreferita=trim($utente->getElementsByTagName('CasaDiSviluppoPreferita')[0]->textContent);

                        }
                    }
                }
                $assPointer->reset();
                foreach($assPointer->elemAss as $utente){
                    if($utente->getAttribute('id_user')==$idUtente){
                        foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                            if($scontoRef->textContent==7){
                                $alreadyAssigned=true;
                            }
                        }
                    }
                }
                if(isset($casaPreferita)){
                if($casaPreferita == $casaSviluppo && !$alreadyAssigned){
                        $assPointer->reset();
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                                $sconto=$assPointer->doc->createElement('Sconto', '7');
                                $listaSconti->appendChild($sconto);
                                $assPointer->save();
                            }
                        }
                    }
                    else if($casaPreferita != $casaSviluppo && $alreadyAssigned){
                        $assPointer->reset();
                        
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                    if($scontoRef->textContent==7){
                                        $scontoDaEliminare = $scontoRef;
                                        $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                                        $assPointer->save();
                                    }
                                }
                            }
                        } 
                    }  
                }
            }
            


            
            // caso 8 il gioco appartiene ad un certo genere
            if(!($this->blacklistChecker($idUtente, 8))){
                $alreadyAssigned=false;
                $offertaValida=false;
                $assPointer->reset();
                $SettingSelector = getRoot('XML/SettingsSconti.xml');
                $genereSelezionato = $SettingSelector->getElementsByTagName('GenereSconto')->item(0)->textContent;
                $utenti = xmlPointer('XML/utenti.xml');
                $giochiPointer = xmlPointer('XML/Giochi.xml');
                foreach($utenti as $utente){
                    if($utente->getAttribute('id_user')== $idUtente){
                        $utenteBase = $utente;
                    }
                }

                $utenteNode = $utenteBase;
                foreach($giochiPointer as $gioco){
                    foreach($utenteNode->getElementsByTagName('listaGiochi')[0]->getElementsByTagName('idGiocoPosseduto') as $giocoUtente){
                        if($gioco->getAttribute('id_gioco') == $giocoUtente->textContent){
                            if($gioco->getElementsByTagName('Generi')->item(0)->textContent == $genereSelezionato){
                                $offertaValida=true;
                            }
                        }
                    }
                    $utenteNode = $utenteBase;
                }
                
                $alreadyAssigned=false;
                foreach($assPointer->elemAss as $utente){
                    if($utente->getAttribute('id_user')==$idUtente){
                        foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                            if($scontoRef->textContent==8){
                                $alreadyAssigned=true;
                            }
                        }
                    }
                }
                $assPointer->reset();
                foreach($assPointer->elemAss as $utente){
                        if($utente->getAttribute('id_user')==$idUtente){
                            $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                            if(!$alreadyAssigned && $offertaValida){
                                $sconto=$assPointer->doc->createElement('Sconto', '8');
                                $listaSconti->appendChild($sconto);
                                $assPointer->save();
                            }
                            else if($alreadyAssigned && !$offertaValida){
                                foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                    if($scontoRef->textContent==8){
                                        $scontoDaEliminare = $scontoRef;
                                        
                                    }
                                }
                        }
                    }
                }
                
                if(isset($scontoDaEliminare)){
                    if($scontoDaEliminare != null && $scontoDaEliminare->parentNode != null){
                        $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                        $assPointer->save();
                    }
                }
            }
            // caso 9 sconto indipendente
            if(!($this->blacklistChecker($idUtente, 9))){
                $alreadyAssigned=false;
                $assPointer->reset();
                foreach($assPointer->elemAss as $utente){
                    if($utente->getAttribute('id_user')==$idUtente){
                        $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                        foreach($listaSconti->getElementsByTagName('Sconto') as $scontoRef){
                            $scontoId = (int)$scontoRef->textContent;
                            if($scontoId == 9) $alreadyAssigned=true;
                            
                        }
                    }
                }
                if(!$alreadyAssigned){
                    $assPointer->reset();
                    foreach($assPointer->elemAss as $utente){
                        if($utente->getAttribute('id_user')==$idUtente){
                            $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                            $sconto=$assPointer->doc->createElement('Sconto', '9');
                            $listaSconti->appendChild($sconto);
                            $assPointer->save();
                        }
                    }
                }
            }
        }
        
        

        public function getSconti(){
            return $this->sconti;
        }


        public function percentualeScontoGioco($idGioco){
            $arraySconti = [];

            foreach($this->sconti as $s){
                $elem = xmlPointer('XML/Sconti.xml');
                foreach($elem as $sconto){
                    if($sconto->getAttribute('id_tipoSconto') == $s){
                        $ref = $sconto->getElementsByTagName('Gioco');
                        foreach($ref as $giocoRef){
                            if($giocoRef->textContent == $idGioco){
                                array_push($arraySconti, (int)$giocoRef->getAttribute('valoreSconto'));
                            }
                        }
                    }
                }
            }
            rsort($arraySconti);
            $scontiInUscita = [];
            if($this->grado==3){
                $scontiInUscita = array_slice($arraySconti, 0, 1);
            }
            else if($this->grado==4){
                $scontiInUscita = array_slice($arraySconti, 0, 2);

            }
            else if($this->grado>=5){
                $scontiInUscita = array_slice($arraySconti, 0, 3);

            }


            return $scontiInUscita;
        }

        
        public function percentualeScontoGiocoAndTipo($idGioco, $tipoSconto){
            $elem = xmlPointer('XML/Sconti.xml');
            foreach($elem as $sconto){
                if($sconto->getAttribute('id_tipoSconto') == $tipoSconto){
                    $ref = $sconto->getElementsByTagName('Gioco');
                    foreach($ref as $giocoRef){
                        if($giocoRef->item(0)->textContent == $idGioco){
                            return (int)$giocoRef->getAttribute('valoreSconto');
                        }
                    }
                }
            }
            return 0;
        }
    }
?>