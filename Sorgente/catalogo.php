<?php 
require 'serverUtility.php'; 
// Il catalogo usa un array per disporre i giochi in ordine alfabetico per praticita si è voluta creare una classe che definisce gli elementi presenti nell array in modo di disporli in ordine alfabetico 
class Game {
    public $idGioco;
    public $titolo;
    public $immagine;
    public $prezzo;
    public function __construct($idGioco, $titolo, $immagine, $prezzo)
    {
        $this->idGioco = $idGioco;
        $this->titolo = $titolo;
        $this->immagine = $immagine;
        $this->prezzo = $prezzo;           
    }
}

$service = 0;
$utente = "";

session_start();
if(isset($_SESSION['userId'])){
    
    $utente = $_SESSION['userName'];
    $service = 1;
}


?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Pixel Hub - Catalogo</title>

        <!-- " ?v=3 " serve a evitare che nel refresh della pagina vengano usate le vecchie versioni di queste regole -->
        <link rel="stylesheet" type="text/css" href="Stile/catalogo.css?v=3" />
        <link rel="stylesheet" type="text/css" href="Stile/base.css?v=3" /> 
        <script type="text/javascript" src="Script/Searchgame.js?v=3"> </script>

      
    </head>
    <body>    
        <div id="container">
            <div id="header"><!--header della pagina -->
                <div id="logo"><img src='Loghi/logo pixelhub slim.png' alt="Logo di Pixel Hub" id="logoimg"/></div>
                <h2>Il tuo shop preferito di videogiochi</h2>
            </div>


            <div id="navigation">
                <div class="dropMenu">
                    <button class="botMenu"><img src="Stile/iconamenu.png" alt=""></button>
                    <ul class ="submenu">
                        <?php
                        if($service == 0) echo "<li><a href=\"login.php\">Log in </a></li>";
                        else if($service == 1){

                            echo "<li><a href=\"login.php\">Log out </a></li>";
                        }
                        ?>
                        
                        <li><a href="Homepage.php">Home</a></li>
                        <li><a href="carrello.php">Carrello </a></li>
                        <li><a href="catalogo.php">Catalogo </a></li>
                        <?php 
                        if($service == 1) echo "<li><a href=\"Profilo.php\">Profilo di $utente </a></li>";
                        ?>
                    </ul>
                </div>

                    <form id="searchBar" onsubmit="return false;">
                        <input type="text" placeholder="Search" onkeyup="mostraRisultati(this.value)">
                        <div id="livesearch"></div>
                    </form>
            </div>
            <div id="wrapper">
                <div id="catalogoContainer">
                
                
                    <h1>Tutti i giochi:</h1>       
                    <?php
                    // si inizializza l'array catalogo e si carica l'XML giochi 

                        $catalogo = [];
                        $titolo = "";
                        $xmlString="";
                        
                        foreach(file("XML/Giochi.xml") as $node){ 
                            $xmlString .= trim($node);
                        }
                        
                        $doc= new DOMDocument();
                        $doc->loadXML($xmlString);
                        $root=$doc->documentElement;
                        $elem=$root->childNodes;

                        for($i=0; $i<$elem->length; $i++){
                            $gioco = $elem->item($i);
                            $idGioco = $gioco->getAttribute("id_gioco");
                            $immagine = $gioco->getElementsByTagName("Immagine")->item(0)->textContent;
                            $prezzo = $gioco->getElementsByTagName("Prezzo")->item(0)->textContent;
                            $titolo = $gioco->getElementsByTagName("Titolo")->item(0)->textContent;
                            $ref = new Game($idGioco, $titolo, $immagine, $prezzo);
                            array_push($catalogo, $ref);
                        }
                        //caricando nell'array tutti i giochi usando il formato definito all'inizio 
                        usort($catalogo, function($rA, $rB){ return $rA->titolo <=> $rB->titolo;});

                        // per ordinare l'array si usa usort per creare un ordine alfabetico mediante function che prende il titolo come valore da confrontare
                        

                        foreach($catalogo as $c){
                        if($titolo[0] !== $c->titolo[0]){
                            $titolo=$c->titolo;
                            echo "<div><h2>".strtoupper($titolo[0])."</h2><hr><pre>                                                          <pre></hr></div>";
                        }
                        // all'cambiare dell'iniziale di un titolo si usa strtoupper($titolo[0]) per prendere la prima lettera dell titolo e metterla in maiuscolo
                        // si usa per mostrare una linea che fa da separazione tra una lettera all'altra il <pre> che mostra lo spazio come una linea
                        
                        
                        
                        echo "
                        
                            <div class='GameCardCat'>
                                <div> <img src='$c->immagine' title='$c->titolo'onclick=\"location.href='Gamepage.php?titoloGioco=$c->titolo&idGioco=$c->idGioco'\"></div>
                                <div class=\"infoBox\">
                                    <div>
                                    <p>
                                    <a href=\"Gamepage.php?titoloGioco=$c->titolo&idGioco=$c->idGioco\">$c->titolo</a>
                                     </p>";

                                      if($service){                    
    
                                        $xmlString = "";
                                        foreach (file("XML/utenti.xml") as $node) {
                                            $xmlString .= trim($node);
                                        }

                                        $doc2 = new DOMDocument();
                                        $doc2->loadXML($xmlString);
                                        $root2=$doc2->documentElement;
                                        $elem2=$root2->childNodes;

                                            

                                        foreach ($elem2 as $utente) {
                                      $idUtente = $utente->getAttribute("id_user");
                                            //Verifico se l'utente loggato possiede già il gioco, in modo da mostrare il prezzo o la dicitura "Acquistato!"
                                            if ($idUtente == $_SESSION['userId']) {
                                                $giochi = $utente->getElementsByTagName("listaGiochi")[0]->getElementsByTagName("idGiocoPosseduto");
                                                $possiedeGioco = false;

                                                foreach ($giochi as $g) {
                                                    
                                                    $idGiocoP = $g->textContent;
                                                    
                                                    if ($idGiocoP == $c->idGioco) {
                                                        $possiedeGioco = true;
                                                        break;
                                                    }
                                                }
                                                if (!$possiedeGioco) echo "<div class=\"prezzo\"><p>  $c->prezzo € </p></div> ";
                                                else echo "<div class=\"acquistato\"><p>  Acquistato!  </p></div> ";
                                                
                                            }
                                        }
                                      }else echo "<div class=\"prezzo\"><p>  $c->prezzo € </p></div> ";

                                     echo "</div>
                                </div>
                            </div>";

                        }

                                
                            
                    ?> 
                
            </div>
            </div>
            
            
                    
                            

        </div>
        <div id="footer">
            <ul>
                <li><a href="">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li>
                <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>