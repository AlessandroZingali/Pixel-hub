<?php 
// Pagina del profilo utente:
// permette di modificare dati personali, acquistare immagini profilo
// e aggiornare informazioni salvate sia su DB che su XML

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

$service = 0;          // indica se l'utente è loggato
$utente = "";          // username dell'utente
$invalidFlag = 0;      // flag per gestire errori logici (email, password, acquisti ecc.)

// Avvio sessione
session_start();

// Controllo se l'utente è loggato
if (isset($_SESSION['userId'])) {
    $utente = $_SESSION['userName'];
    $service = 1;
}

$idUtenteEsterno = $_GET['idUtenteExt'];
$table_users = "Tabella_Utenti";
$tipoUtente = null;
$toggleState = 'false';
connectDB();


if (mysqli_connect_errno()) {
    printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
}

$sql = "
    SELECT * FROM $table_users WHERE ID=$idUtenteEsterno
";

$res = mysqli_query(connectDB(), $sql);
if (mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_array($res);
    $tipoUtente = $row['Tipologia_utente'];
}

if($tipoUtente != null && $tipoUtente == '2') {
    $elem = xmlPointer('XML/utenti.xml');
    foreach ($elem as $utente) {
        if ($utente->getAttribute('id_user') == $idUtenteEsterno){
            $toggleState = $utente->getElementsByTagName('ToggleAgency')->item(0)->textContent;
        }
    }    
}





?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Il mio profilo</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/ProfiloEsterno.css?v=3" />
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" />  
        <script>
            <?php  
            if($service == 1 && isset($_SESSION['generePreferito'])){
                echo "sessionStorage.setItem(\"idUser\", \"".$_SESSION['userId']."\");";
                echo "sessionStorage.setItem(\"genPref\", \"".$_SESSION['generePreferito']."\");";
            }
            ?>
            
        </script>
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>
        
        <?php
            
           
            if($toggleState == 'true'){
  
                echo "<script>";
                echo "sessionStorage.setItem(\"activeChangeEst\", \"true\");";
                echo "</script>";
            }
            else if($toggleState == 'false'){
               
                echo "<script>";
                echo "sessionStorage.setItem(\"activeChangeEst\", \"false\");";
                echo "</script>";
            }
            else{
                echo "<script>";
                echo "sessionStorage.setItem(\"activeChangeEst\", \"vuoto\");";
                echo "</script>";
            }

    
        ?>
        <script type="text/javascript" src="Script/cardGestioneProfiloEsterno.js?v=3"> </script>
    </head>
    <body>
        <div id="container">
             <div id="header"><!--header della pagina -->
                <div id="logo"><img src='Loghi/logo pixelhub slim.png' alt="Logo di Pixel Hub" id="logoimg"/></div>
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
                    <input id="searchBarInput" type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                    <div id="livesearch"></div>
                </form>
            </div>
            <!-- all'interno del wrapper che sara il divisore principale ci saranno contenuti la card profilo dove saranno mostrate tutte le informazioni principali del l'account con gli ultimi 4 giochi acquistati e due pulsanti  -->

            <div class="wrapper">
                <div class="cardProfilo" id="card1">
                    
                    <div class="baseProfilo" >
                
                    
                        <?php 
                            
                            connectDB();

                            if (mysqli_connect_errno()){

                                printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
                            }
                            

                            $queryLogin = "SELECT * FROM $table_users WHERE (ID='$idUtenteEsterno')";
                            $resultQ = mysqli_query(connectDB(), $queryLogin);
                            $num = mysqli_num_rows($resultQ); 
                            if($num == 1){
                                $flag=1;
                                
                                $row=mysqli_fetch_array($resultQ);
                                echo"<h1>Pagina Utente - ".$row['Username']."</h1>
                                        <div class=\"profilePicColum\">
                                            <div class=\"propic\"> 
                                                <img src=\"".$row['imgProfiloPath']."\" alt=\"Immagine di Default\"/>
                                            </div>
                                        </div>";  

                                echo"<div id=\"infoBox\">";

                                    $elem = xmlPointer("XML/utenti.xml");

                                    // Caricamento file XML utente con le varie informazioni
                                    
                                    foreach($elem as $i){

                                        if($i->getAttribute('id_user') == $idUtenteEsterno){

                                            if($i->getElementsByTagName('GenerePreferito')->item(0)->textContent != '') $GenerePref = $i->getElementsByTagName('GenerePreferito')->item(0)->textContent;
                                            else $GenerePref = "nessuno";
                                            if($i->getElementsByTagName('CasaDiSviluppoPreferita')->item(0)->textContent != '') 
                                                $CasaSvilPref = $i->getElementsByTagName('CasaDiSviluppoPreferita')->item(0)->textContent;
                                            else $CasaSvilPref = "nessuna";
                                            if($i->getElementsByTagName('DataIscrizione')->item(0)->textContent != '') 
                                                $DataIsc= $i->getElementsByTagName('DataIscrizione')->item(0)->textContent;
                                            else $DataIsc = "!Errore!::Informazione non presente, si prega di ricontrollare le impostazioni di iscrizione";
                                            if($i->getElementsByTagName('linkEsterno')->item(0)->textContent != '') 
                                                $Contatti = $i->getElementsByTagName('linkEsterno')->item(0)->textContent;
                                            else $Contatti = "nessuno";
                                            if($i->getElementsByTagName('Descrizione')->item(0)->textContent != '') 
                                                $Descrizione = $i->getElementsByTagName('Descrizione')->item(0)->textContent;
                                            else $Descrizione = "Nessuna Descrizione";
                                            }
                                        }
                                }
                                echo "
                                    <table class=\"info\">

                                        <tr>
                                            <td>Username:</td> 
                                            <td>".$row['Username']."</td>
                                        </tr>

                                        <tr>
                                            <td>Email:</td>
                                            <td>".$row['Email']."</td>
                                        </tr>

                                        <tr>
                                        <td>Numero di Pixel in possesso:</td> 
                                        <td> ".$row['Pixels']."</td>
                                        </tr>

                                        <tr>
                                        <td>Grado attuale:</td> 
                                        <td>".$row['Grado']."</td>
                                        </tr>

                                        <tr>
                                            <td>Il mio genere preferito:</td>
                                            <td>$GenerePref</td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Data Iscrizione:</td>
                                            <td>$DataIsc</td>
                                        </tr>
                                        
                                        <tr>
                                            <td>I miei contatti:</td>
                                            <td><a href=\"$Contatti\"> Link social </a></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>La mia casa di sviluppo preferita:</td>
                                            <td>$CasaSvilPref</td>
                                        </tr>";
                                    
                                        if($service == 1){
                                            if(isset($_SESSION['userId']) && isset($_SESSION['tipoUtente']) && $_SESSION['tipoUtente'] == '2'){

                                                echo"<tr>
                                                    <td>Descrizione:</td>
                                                    <td>$Descrizione</td>
                                                </tr>";
                                            }
                                        }
                                        echo"
                                            <tr>
                                                <td>I miei punti esperienza:</td>
                                                <td>".$row['Esperienza']."</td>
                                            </tr>
        
                                    </table>
                                </div>
                            ";
                        ?>

             </div> <!-- div di chiusura del baseProfilo -->



                        <!-- piccola griglia con gli ultimi 4 giochi posseduti -->
                        <div class="flexGridGames">
                            <div><h3 id="lastTitle">Ultimi Acquisti</h3></div>
                                <div class="lastGames">
                                    <?php
                                        $utente = xmlPointer("XML/utenti.xml");
                                        foreach($utente as $u){
                                            if($u->getAttribute('id_user') == $idUtenteEsterno){
                                                $gameList = $u->getElementsByTagName('listaGiochi')->item(0)->getElementsByTagName("idGiocoPosseduto");
                                                
                                                $idContainer = [];

                                                foreach($gameList as $id){
                                                    $idContainer[] =  $id->textContent;
                                                }
                

                                                //Come per il catalogo si inseriscono nell array idcontainer gli id di tutti i giochi posseduti dall'utente e 
                                                //si effettua un reverse sort per invertire l'ordine 
                                            
                                            $idContainer = array_reverse($idContainer);
                                            //var_dump($idContainer);
                                            
                                            
                                                
                                                $giochi = xmlPointer("XML/Giochi.xml");

                                                $count = 0;

                                                // infine si prendono da Giochi.xml i dati dei 4 giochi 
                                                    foreach($idContainer as $i){
                                                        for($j = ($giochi->length)-1 ; $j>=0; $j--){
                                                        $g=$giochi->item($j);
                                                            if($i == $g->getAttribute("id_gioco") && $count < 4){
                                                                $titolo=$g->getElementsByTagName('Titolo')->item(0)->textContent;
                                                                $idGioco=$g->getAttribute("id_gioco");
                                                                
                                                                echo "<div class=\"lastgame\"><img src=\"".$g->getElementsByTagName('Immagine')->item(0)->textContent."\" onclick=\"location.href='Gamepage.php?titoloGioco=$titolo&idGioco=$idGioco'\" alt=\"".$g->getElementsByTagName('Titolo')->item(0)->textContent."\"/></div>";
                                                                $count++;
                                                            }
                                                        }
                                                    }
                                                
                                            }
                                        }

                            
                                    ?>
                            </div>
                    
                        
                        </div>

                   
                    <!-- div di chiusura del profilo di base -->
                

                </div> <!-- div di chiusura del cardProfilo card1 -->

            <?php
                connectDB();

                if (mysqli_connect_errno()){
                    printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
                }
                
                $queryLogin = "SELECT * FROM $table_users WHERE (ID='$idUtenteEsterno')";
                $resultQ = mysqli_query(connectDB(), $queryLogin);
                $num = mysqli_num_rows($resultQ); 
                if($num == 1){
                    $flag=1;
                    
                    $row = mysqli_fetch_array($resultQ);

                    $username = $row['Username'];
                    $immagineProfilo = $row['imgProfiloPathPub'];
                } 
                ?> 
                <div class="cardProfilo" id="card2" >
                    <!-- div per il profilo publisher -->
                   
                            
                    <?php
                                 
                            echo "<div><h1>Presentazione Publisher: $username </h1></div>";
                            echo "<div id=\"Presentazione\">";
                            echo "<div class=\"publisherPropic\"> 
                                    <img src=\"".$immagineProfilo."\" alt=\"Immagine di Default\"/>
                                    </div>";
                        
                            $elemUtenti = xmlPointer("XML/utenti.xml");
                                // Caricamento file XML utente con le varie informazioni
                            foreach($elemUtenti as $i){
                                
                                if($i->getAttribute('id_user') == $idUtenteEsterno){
                                    $descP = $i->getElementsByTagName('DescrizionePublisher');
                                    if ($descP->length > 0){
                                    if($i->getElementsByTagName('DescrizionePublisher')->item(0)->textContent != ''){
                                    $descrizione = $i->getElementsByTagName('DescrizionePublisher')->item(0)->textContent;                    
                                    }
                                    }
                                
                                    else $descrizione = "Nessuna Descrizione";
                                }
                            }
                            echo "<div><p>La mia Descrizione: $descrizione</p></div>";
                            echo "</div>"; // chiusura div Presentazione
                            


                            echo "<div id=\"libreria\">";

                                                        
        
                            $elemGiochi = xmlPointer("XML/Giochi.xml");
                            foreach($elemGiochi as $gioco){
                                if($gioco->getElementsByTagName('Publisher')->item(0)->textContent == $username){
                                    $idGioco = $gioco->getAttribute('id_gioco');

                                    $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
                                    $genere = $gioco->getElementsByTagName("Generi")->item(0)->textContent;
                                    $voto = $gioco->getElementsByTagName("MediaRecensioniAdmin")->item(0)->textContent;
                                    $publisher = $gioco->getElementsByTagName("Publisher")->item(0)->textContent;
                                    $image = $gioco->getElementsByTagName("Immagine")->item(0)->textContent;
                                    echo "<div id=\"libreriaBody\"><a onclick=\"location.href='Gamepage.php?titoloGioco=$titolo&idGioco=$idGioco'\">
                                            <div class=\"imageGame\"><img src=\"$image\" alt=\"$titolo\"/></div>
                                            <div><p>Titolo:</p><p> $titolo</p></div>
                                            <div><p>Genere:</p><p> $genere</p></div>
                                            <div><p>Voto:</p><p> $voto</p></div>
                                            <div><p>Publisher: </p><p>$publisher</p></div>
                                        </a></div>";
                                }
                            }
                            
                            echo "</div>"; // chiusura div libreria
                    ?>
                </div> <!-- div di chiusura del cardProfilo card2 -->


            </div>  <!-- div di chiusura del wrapper--> 
            
        </div> <!-- div di chiusura del container -->
        
        <div id="footer">
            <ul>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
        
    </body>
    

</html>