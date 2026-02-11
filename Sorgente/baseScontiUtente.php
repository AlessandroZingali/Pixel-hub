<!-- 
1.  clienti che hanno speso N crediti finora, 
2.  clienti che hanno speso M crediti da una certa data, 
3.  clienti che hanno acquistato una certa offerta(giochi correlati); 
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
        private $sconti=[];

        function __construct($idUtente){
            $this->idUtente=$idUtente;
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
        private function scanner($idUtente){
            $elemUtenti = xmlPointer('XML/utenti.xml');
            $SettingSelector = getRoot('XML/SettingsSconti.xml');
            $assPointer = new scontiAss();
            $alreadyAssigned=false;


            // 1.  clienti che hanno speso N crediti finora,
            $saldo = 0;
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

            // 2. spesa da una certa data
            $saldo=0;
            $alreadyAssigned=false;
            $elemUtenti = xmlPointer('XML/utenti.xml');
            $SettingSelector = getRoot('XML/SettingsSconti.xml');
            $ValoreSpesa= $SettingSelector->getElementsByTagName('MinimiSpesiDa')->item(0)->getElementsByTagName('Valore')->item(0)->textContent;
            $SpesaData = trim($SettingSelector->getElementsByTagName('MinimiSpesiDa')[0]->getElementsByTagName('DataInizio')->item(0)->textContent);
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
                            
                            if($annoC>$anno || ($annoC==$anno && $meseC>$mese) || ($annoC==$anno && $meseC==$mese && $giornoC>=$giorno)){
                            $saldo+= (int)$gioco->getAttribute('spesa');
                            
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
                                $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                                $assPointer->save();
                            }
                        }
                    }
                }
                
           }


                       //3: sconti per generi correlati
            $offertaFlag = false;
            $giochiCor = [];
            
            $giochi = xmlPointer('XML/Giochi.xml');
            $utenti = xmlPointer('XML/utenti.xml');
            $sconti = xmlPointer('XML/Sconti.xml');
            foreach($sconti as $sconto){
                if($sconto->getAttribute('id_tipoSconto')==3) $scontoPointer = $sconto;
            }
            $scontoSel = $scontoPointer;
            $assPointer->reset();
            $isIn = false;
            $alreadyAssigned=false;

            foreach($giochi as $gioco){
                if($gioco->getElementsByTagName('TitoliCorrelati')->length > 0){
                    $correlati = $gioco->getElementsByTagName('TitoliCorrelati')[0]->getElementsByTagName('idGiocoCorrelato');
                    foreach($scontoSel->getElementsByTagName('Gioco') as $giocoSconto){
                        if($gioco->getAttribute('id_gioco') == $giocoSconto->textContent){
                            $isIn = true;
                            break;
                        }
                    }
                    foreach($correlati as $correlato){
                        array_push($giochiCor, (int)$correlato->textContent);
                    }
                }
                
                $isIn = false;
            }
            //var_dump($giochiCor);
            $giochiCor = array_unique($giochiCor);
            

            foreach($utenti as $utente){
                $giochiPosseduti = $utente->getElementsByTagName('listaGiochi')[0]->getElementsByTagName('idGiocoPosseduto');
                foreach($giochiPosseduti as $gp){
                    foreach($giochiCor as $gc){
                        if($gp->textContent == $gc){
                            $offertaFlag = true;
                            break;
                        }
                    }
                }
            }
            

            if($offertaFlag){
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
                foreach($assPointer->elemAss as $utente){
                    if($utente->getAttribute('id_user')==$idUtente){
                        $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                        if(!$alreadyAssigned){
                            $sconto=$assPointer->doc->createElement('Sconto', '3');
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
                            if($scontoRef->textContent==3){
                                $scontoDaEliminare = $scontoRef;
                                $scontoDaEliminare->parentNode->removeChild($scontoDaEliminare);
                                $assPointer->save();
                            }
                        }
                    }
                }
                
           }

            //    caso 4: clienti che hanno una certa reputazione;
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


                    $diffAnni = (int)date("Y") - (int)$annoC;
                    $diffMesi = (int)date("m") - ((int)$meseC + ($diffAnni * 12));

                    $type = 0;
                    if($diffMesi >= (int)$mesiMin) $type = 5;
                    else if ($diffAnni >= (int)$anniMin) $type = 6;


                    if($diffMesi >= (int)$mesiMin || $diffAnni >= (int)$anniMin){
                        $assPointer->reset();
                        $alreadyAssignedTypeFive=false;
                        $alreadyAssignedTypeSix=false;
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                    if($scontoRef->textContent==5){
                                        $alreadyAssignedTypeFive=true;
                                    }
                                    else if($scontoRef->textContent==6){
                                        $alreadyAssignedTypeSix=true;
                                    }
                                }
                            }
                        }
                        $assPointer->reset();
                        foreach($assPointer->elemAss as $utente){
                            if($utente->getAttribute('id_user')==$idUtente){
                                $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                                if(!($alreadyAssignedTypeFive && $alreadyAssignedTypeSix)){
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
                                    if(($scontoRef->textContent==5 && $type == 5) || ($scontoRef->textContent==6 && $type == 6)){
                                        $scontoDaEliminare = $scontoRef;
                                        $assPointer->doc->removeChild($scontoDaEliminare);
                                        $assPointer->save();
                                    }
                                }
                            }
                        } 
                    }
                }

            }

            // caso 7 il gioco appartiene ad una casa di sviluppo

            $alreadyAssigned=false;
            $SettingSelector = getRoot('XML/SettingsSconti.xml');
            $casaSviluppo = $SettingSelector->getElementsByTagName('CasaSconto')->item(0)->textContent;
            $giochi = xmlPointer('XML/Giochi.xml');
            foreach($giochi as $gioco){
                if($gioco->getAttribute('CasaSconto') == $casaSviluppo){
                    $assPointer->reset();
                    $alreadyAssigned=false;
                    foreach($assPointer->elemAss as $utente){
                        if($utente->getAttribute('id_user')==$idUtente){
                            foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                                if($scontoRef->textContent==7){
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
                                $sconto=$assPointer->doc->createElement('Sconto', '7');
                                $listaSconti->appendChild($sconto);
                                $assPointer->save();
                            }
                        }
                    }
                }
            }

            // caso 8 il gioco appartiene ad un certo genere
            $alreadyAssigned=false;
            $SettingSelector = getRoot('XML/SettingsSconti.xml');
            $genereSelezionato = $SettingSelector->getElementsByTagName('GenereSconto')->item(0)->textContent;
            $utenti = xmlPointer('XML/utenti.xml');
            foreach($utenti as $u){
                $refGiochi = $u->getElementsByTagName('listaGiochi')[0]->getElementsByTagName('idGiocoPosseduto');
                foreach($refGiochi as $rg){
                    $giochiPointer = xmlPointer('XML/Giochi.xml');
                    foreach($giochiPointer as $g){
                        if($g->getAttribute('id_gioco') == $rg->textContent){
                            $genereDaConfrontare = $g->getElementsByTagName('Generi')->item(0)->textContent;
                            if($genereDaConfrontare == $genereSelezionato){
                            $assPointer->reset();
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
                            if(!$alreadyAssigned){
                                $sconto=$assPointer->doc->createElement('Sconto', '8');
                                $listaSconti->appendChild($sconto);
                                $assPointer->save();
                            }
                        }
                    }
                }
                        }
                    }
                    
                }
                
            }
            // caso 9 sconto indipendente
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

            return $arraySconti;
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