<?php
/* Pagina che mostrera il carrello ancora in fase di sviluppo;
L'idea è di un elenco dei prodotti acquistati con il nome  il prezzo di partenza e quello finale dato dopo gli sconti */
require 'baseScontiUtente.php';
require_once 'gestioneEsperienzauser.php';

require_once 'serverUtility.php';




class Game{
    public $idGioco = 0;
    public $titolo;
    public $prezzoIniziale;
    public $prezzoFinale;
    public $scontoFinale;
    public $scontiApplicati = [];
    
    public function __construct($idGioco, $titolo, $prezzoIniziale, $prezzoFinale, $scontiApplicati, $scontoFinale){
        $this->idGioco = $idGioco;
        $this->titolo = $titolo;
        $this->prezzoIniziale = $prezzoIniziale;
        $this->prezzoFinale = $prezzoFinale;
        $this->scontoFinale = $scontoFinale;
        $this->scontiApplicati = $scontiApplicati;
    }
}

class Carrello{
    public $idUtente;
    public $listaGiochi = [];

    public function __construct($idUtente){
        $this->idUtente = $idUtente;
    }

    public function aggiungiGioco($idGioco, $titolo, $prezzoIniziale, $prezzoFinale, $scontiApplicati, $scontoFinale){
        $newGame = new Game($idGioco, $titolo, $prezzoIniziale, $prezzoFinale, $scontiApplicati, $scontoFinale);
        array_push($this->listaGiochi, $newGame);
    }

    public function rimuoviGioco($idGioco){
        $i=0;
        foreach($this->listaGiochi as $game){
            $i++;
            if($game->idGioco == $idGioco){
                unset($this->listaGiochi[$i]);
            }
        }
    }

    public function svuotaCarrello(){
        $this->listaGiochi = [];
    }

    public function calcolaTotaleScontato(){
        $totale = 0;
        foreach($this->listaGiochi as $game){
            $totale += $game->prezzoFinale;
        }
        return $totale;
    }
    public function calcolaTotaleIniziale(){
        $totale = 0;
        foreach($this->listaGiochi as $game){
            $totale += $game->prezzoIniziale;
        }
        return $totale;
    }

}
error_reporting(E_ALL & ~E_NOTICE);
$service = 0;
$utente = "";
$table_users = "Tabella_Utenti";

session_start();
if(isset($_SESSION['userId'])){
    
    $utente = $_SESSION['userName'];
    $service = 1;
    $pageCart = new Carrello($_SESSION['userId']);
    $servizioSconti = new scontiUtente($_SESSION['userId']);
}


if(isset($_POST['Acquista'])){
    connectDB();
    $listaGiochi_json = json_decode($_SESSION['gameList']);
    // var_dump($listaGiochi_json);

    if (mysqli_connect_errno()){

        printf("problemi di connessione : %s", mysqli_connect_error(connectDB()));
    }
    $emailNickname = $_SESSION['Email'];

    $queryLogin = "SELECT * FROM $table_users WHERE Email='$emailNickname' ";
    $resultQ = mysqli_query(connectDB(), $queryLogin);
    $num = mysqli_num_rows($resultQ); 
    if($num == 1){
        $row = mysqli_fetch_array($resultQ);
        $saldoUtente = (float)$row['Saldo_attuale'];
        $pixels = (int)$row['Pixels'];
        if($saldoUtente >= $_POST['SaldoTotale']){
            echo "saldo utente: ".$saldoUtente;
            echo "Saldo totale: ".$_POST['SaldoTotale'];
            $nuovoSaldo = $saldoUtente - (float)$_POST['SaldoTotale'];
            echo "risultante: ".$nuovoSaldo;

            $updateSaldoQuery = "UPDATE $table_users SET Saldo_attuale='$nuovoSaldo'  WHERE (Email='$emailNickname' OR Username='$emailNickname')";

            mysqli_query(connectDB(), $updateSaldoQuery);

            // Aggiungo i giochi acquistati alla lista giochi posseduti dell'utente
            $doc = getDoc('XML/utenti.xml');
            $root = $doc->documentElement;
            $elemUtenti = $root->childNodes;
            foreach($elemUtenti as $utente){
                if($utente->getAttribute('id_user')==$_SESSION['userId']){
                    $listaGiochi=$utente->getElementsByTagName('listaGiochi')[0];
                    $elemCarrello = xmlPointer('XML/Carrelli.xml');
                    foreach($elemCarrello as $carrello){
                        if($carrello->getAttribute('id_user')==$_SESSION['userId']){
                            $elemGioco = $carrello->getElementsByTagName('gioco');
                            $i=0;
                            
                            foreach($elemGioco as $gioco){
                                $nuovoGioco = $doc->createElement('idGiocoPosseduto', $gioco->getAttribute('id_gioco'));
                                $nuovoGioco->setAttribute('data_acquisizione', date("d-m-Y"));
                                $nuovoGioco->setAttribute('spesa', (float)$listaGiochi_json[$i]->prezzoFinale);
  
                                $listaGiochi->appendChild($nuovoGioco);
                                $i++;
                                
                            }
                            $doc->save('XML/utenti.xml');

                        }
                    }
                }
            }
            calcoloEsperienza();

            
            // Svuoto il carrello
            $doc = getDoc('XML/Carrelli.xml');
            $root = $doc->documentElement;
            $elemCarrello = $root->childNodes;
            foreach($elemCarrello as $carrello){
            if($carrello->getAttribute('id_user')==$_SESSION['userId']){
                $elemGioco = $carrello->getElementsByTagName('gioco');
                foreach($elemGioco as $gioco){
                        $carrello->removeChild($gioco);
                        $doc->save('XML/Carrelli.xml');
                        
                }
            }
            }
            $_SESSION['Saldo'] = $nuovoSaldo;

            header("Location: Profilo.php");
            
            

        }
        else{
            echo "<script>alert('Saldo insufficiente per completare l\'acquisto. Ricarica il tuo saldo e riprova.');</script>";
        }
    }

    
}

if(isset($_POST['buttonRimuoviAll'])){
    $doc = getDoc('XML/Carrelli.xml');
    $root = $doc->documentElement;
    $elemCarrello = $root->childNodes;
    
    foreach($elemCarrello as $carrello){
    if($carrello->getAttribute('id_user')==$_SESSION['userId']){
        
        while ($carrello->hasChildNodes()) {
                $carrello->removeChild($carrello->firstChild);
            }
       
        $pageCart->svuotaCarrello();
        $carrello->parentNode->removeChild($carrello);
        $doc->save('XML/Carrelli.xml');
    }
    }

}

if(isset($_POST['buttonRimuovi'])){
    $doc = getDoc('XML/Carrelli.xml');
    $root = $doc->documentElement;
    $elemCarrello = $root->childNodes;
    foreach($elemCarrello as $carrello){
    if($carrello->getAttribute('id_user')==$_SESSION['userId']){
        $elemGioco = $carrello->getElementsByTagName('gioco');
        foreach($elemGioco as $gioco){
            $idGioco = $gioco->getAttribute('id_gioco');
            if($idGioco == $_POST['idGioco']){
                $pageCart->rimuoviGioco($idGioco);
                $carrello->removeChild($gioco);
                $doc->save('XML/Carrelli.xml');
                
            }
        }
    }
    }
 header("Location: Carrello.php");
}

?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Catalogo</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/Carrello.css?v=3" />
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" /> 

      
    </head>
    <body>    
        <div id="container">
            <div id="header">
                <div id="logo">
                
                    <img src='Loghi/logo pixelhub slim.png' alt="Logo di Pixel Hub" id="logoimg"/>
                
                </div>
                
                <h2>Il tuo shop preferito di videogiochi</h2>
 
            </div>

            <div id="navigation">
                <div class="dropMenu">
                    <button class="botMenu"><img src="Stile/Icone/iconamenu.png" alt=""></button>
                    <ul>
                        <li><a href="Homepage.php">Home</a></li>
                        <li><a href="catalogo.php">Catalogo </a></li>
                        <li><a href="carrello.php">Carrello </a></li>
                        <?php
                            
                            if($service == 0){
                                if(isset($_SESSION['userId']) && isset($_SESSION['generePreferito'])){
                                    echo "<script>";
                                    echo "sessionStorage.removeItem(\"idUser\");";
                                    echo "sessionStorage.removeItem(\"genPref\");";
                                    echo "</script>"; 
                                }
                                echo "<li><a href=\"login.php\">Log in </a></li>";
                            }
                            else if($service == 1){
                                echo "<li><a href=\"login.php\">Log out </a></li>";
                                echo "<li>
                                <a href=\"Profilo.php\">Profilo</a>
                                </li> 
                                <p id=\"saldo\"> Pixels: ".$_SESSION['Pixels']." </br> Saldo attuale: ".$_SESSION['Saldo']." € </p>";
                            
                                if($_SESSION['tipoUtente'] == '1'){
                                echo "<li><a href=\"GestioneAdmin.php\">Gestione</a></li>";
                            }
                            if(isset($_SESSION['tipoUtente'])){
                                if($_SESSION['tipoUtente'] == "2")
                                echo "<li><a href=\"gestionePublisher.php\">Gestione</a></li>";
                            }
                            }
                        ?>
                    </ul>
                </div>

                    <form id="searchBar" onsubmit="return false;">
                        <input type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>

            <h1>Il mio Carrello</h1>
            <?php
            $elemCarrello = xmlPointer('XML/Carrelli.xml');
            $flagCarrelloVuoto = true;
            if($service==1){ 
                foreach($elemCarrello as $carrello){
                    
                    if($carrello->getAttribute('id_user')==$_SESSION['userId']){
                        $elemGioco = $carrello->getElementsByTagName('gioco');
                        if($elemGioco->length == 0){
                            var_dump($carrello->getAttribute('id_cart'));
                            $flagCarrelloVuoto = false;
                            echo "<h2 >Il tuo carrello e' vuoto! Torna al <a id=\"messageEmpty\" href='Catalogo.php'>catalogo</a> per aggiungere giochi!</h2>";
                        }
                        else{
                            $flagCarrelloVuoto = false;
                            echo "<h2>Ecco i giochi presenti nel tuo carrello:</h2>";
                            
                            echo "<div id=\"CarrelloMain\">
                                    <table>
                                        <tr>
                                            <th> Nome Gioco </th>
                                            <th> Prezzo Iniziale </th>
                                            <th> Prezzo Finale </th>
                                        
                                            <th> Sconti Applicati </th>
                                            <th> Sconto Totale</th>
                                            <th> Rimuovi </th>
                                        </tr> ";
                                        
                                        $scontoFinale = 0;
                                        $prezzoIniziale = 0;
                                        $prezzoFinale = 0;
                                        $elemCarrello = xmlPointer('XML/Carrelli.xml');
                                        foreach($elemCarrello as $carrello){
                                            if($carrello->getAttribute('id_user')==$_SESSION['userId']){
                                                $elemGioco = $carrello->getElementsByTagName('gioco');
                                                foreach($elemGioco as $gioco){
                                                    echo "<tr>";
                                                    $scontiSulGioco = [];
                                                    $idGioco = $gioco->getAttribute('id_gioco');
                                                    $titolo=$gioco->getElementsByTagName('titolo')->item(0)->textContent;
                                                    $prezzoIniziale=$gioco->getElementsByTagName('prezzo')->item(0)->textContent;
                                                    $scontiSulGioco = $servizioSconti->percentualeScontoGioco($idGioco);
                                                    

                                                    connectDB();

                                                    if (mysqli_connect_errno()){

                                                        printf("problemi di connessione : %s", mysqli_connect_error(connectDB()));
                                                    }
                                                    $emailNickname = $_SESSION['Email'];

                                                    connectDB();

                                                    

                                                    $queryLogin = "SELECT * FROM $table_users WHERE (Email='$emailNickname')";
                                                    $resultQ = mysqli_query(connectDB(), $queryLogin);
                                                    $num = mysqli_num_rows($resultQ); 
                                                    if($num == 1){
                                                        $row = mysqli_fetch_array($resultQ);
                                                        $gradoUtente = $row['Grado'];


                                                    if(count($scontiSulGioco) < 3){
                                                        foreach($scontiSulGioco as $sconto){
                                                            $scontoFinale+=$sconto;   
                                                        }
                                                    }
                                                    else{
                                                        for($i=0; $i<$gradoUtente; $i++){
                                                            $scontoFinale+=$scontiSulGioco[i];
                                                        }
                                                    }
                                                    }
                                                    $prezzoFinale = round(  $prezzoIniziale - ($prezzoIniziale * ($scontoFinale / 100)), 2);
                                                    $pageCart->aggiungiGioco($idGioco, $titolo, $prezzoIniziale, $prezzoFinale, $scontiSulGioco, $scontoFinale);
                                                    // $tiposconto = $servizioSconti->tipoScontoApplicato($idGioco);
                                                    

                                                    echo "<td><a href=\"Gamepage.php?titoloGioco= $titolo&idGioco=$idGioco\">$titolo</a></td>";
                                                    echo "<td>$prezzoIniziale €</td>";
                                                    echo "<td> $prezzoFinale €</td>";
                                                    // echo "<td> $tiposconto</td>";
                                                    echo "<td>";
                                                    foreach($scontiSulGioco as $sconto){
                                                        echo "$sconto % ";
                                                    }
                                                    echo "</td>";
                                                    echo "<td>$scontoFinale % </td>";
                                                    echo "<td>
                                                        <form method='post' action='Carrello.php'>
                                                            <input type='hidden' name='idGioco' value='$idGioco'>
                                                            <button type='submit' id='removebutton' name='buttonRimuovi'>❌</button>
                                                        </form>
                                                        </td>";

                                                    echo "</tr>";

                                                    $scontiSulGioco = [];
                                                    $scontoFinale = 0;
                                                    $prezzoIniziale = 0;
                                                }
                                                
                                            }
                                        }

                                        $totaleIniziale = $pageCart->calcolaTotaleIniziale();
                                        $totaleFinale = $pageCart->calcolaTotaleScontato();
                                       
                                        $risparmio = round($totaleIniziale - $totaleFinale, 2);
                                        $gameList_json = (json_encode($pageCart->listaGiochi));
                                        $_SESSION['gameList'] = $gameList_json;
                                        
                                   echo "<tr> <td>Totale finale:  $totaleFinale </td> </tr>";
                                  echo "  </table> 
                                    <form action=\"Carrello.php\" method=\"post\" id=\"formCarrello\">
                                        <input type=\"hidden\" name=\"SaldoTotale\" value=\"$totaleFinale\"/>
                                        <input type=\"submit\" name=\"Acquista\" value=\"Procedi al pagamento\" id=\"pagaButton\"/>
                                        <input type=\"submit\" name=\"buttonRimuoviAll\" value=\"Svuota carrello\" id=\"svuotaButton\"/>
                                    </form>
                                    
                                </div>";

                            
                    }
                }
                
            }
            
        ?>
        
        </div>       

        <div id="footer">
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>