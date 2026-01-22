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
    require 'serverUtility.php';
    class scontiUtente{
        private $idUtente;
        private $sconti=[];

        function __construct($idUtente){
            $this->idUtente=$idUtente;
            scanner($idUtente);
            $elem = xmlPointer('XML/ScontiAssegnati.xml');
            foreach($elem as $user){
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
                $doc = getDoc('XML/ScontiAssegnati.xml');
                $root = $doc->documentElement;
                $elemAss= $root->childNodes;
                foreach($elemAss as $utente){
                    if($utente->getAttribute('id_user')==$idUtente){
                        foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                            if($scontoRef->textContent==1){
                                $alreadyAssigned=true;
                            }
                        }
                    }
                }
                $elemAss= $root->childNodes;
                foreach($elemAss as $utente){
                    if($utente->getAttribute('id_user')==$idUtente){
                        $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                        if(!$alreadyAssigned){
                            $sconto=$doc->createElement('Sconto', '1');
                            $listaSconti->appendChild($sconto);
                            $doc->save('XML/ScontiAssegnati.xml');
                        }
                    }
                }
           }

        // spesa da una certa data
        $elemUtenti = xmlPointer('XML/utenti.xml');
        $SettingSelector = getRoot('XML/SettingsSconti.xml');

        $ValoreSpesa= $SettingSelector->getElementsByTagName('MinimiSpesiDa')[0]->getElementsByTagName('Valore')->item(0)->textContent;
        $SpesaData= trim($SettingSelector->getElementsByTagName('MinimiSpesiDa')[0]->getElementByTagName('DataInizio')->item(0)->textContent);
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
                $doc = getDoc('XML/ScontiAssegnati.xml');
                $root = $doc->documentElement;
                $elemAss= $root->childNodes;
                foreach($elemAss as $utente){
                    if($utente->getAttribute('id_user')==$idUtente){
                        foreach($utente->getElementsByTagName('scontiAssegnati')[0]->getElementsByTagName('Sconto') as $scontoRef){
                            if($scontoRef->textContent==1){
                                $alreadyAssigned=true;
                            }
                        }
                    }
                }
                $elemAss= $root->childNodes;
                foreach($elemAss as $utente){
                    if($utente->getAttribute('id_user')==$idUtente){
                        $listaSconti=$utente->getElementsByTagName('scontiAssegnati')[0];
                        if(!$alreadyAssigned){
                            $sconto=$doc->createElement('Sconto', '2');
                            $listaSconti->appendChild($sconto);
                            $doc->save('XML/ScontiAssegnati.xml');
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
                            if($giocoRef->item(0)->textContent == $idGioco){
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