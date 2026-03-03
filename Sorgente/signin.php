<?php  
/*Questa pagina gestisce la registrazione degli utenti, permettendo di specificare diverse tipologie di account(normale, publisher, admin) 
attraverso parametri GET e cookie.La validazione dei dati inseriti avviene tramite 
espressioni regolari (tramite il preg_match) per email, password, partita IVA e data di nascita.
In caso di successo, i dati vengono inseriti nel database e viene creato un file XML associato all'utente.
In caso di errore, vengono visualizzati messaggi di notifica all'utente.
I publisher vengono inseriti nel sistema solo previa partita IVA; gli admin solo se possiedono una chiave di accesso specifica
fornita dalla gestione del Sito*/
error_reporting(E_ALL &~E_NOTICE);

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

//Quando si entra nella pagina di sign in/registrazione si unsetta la sessione attuale e si eliminano tutte le eventuali informazioni salvate in $session
if(isset($_SESSION)){
   session_unset($_SESSION);
   session_destroy(); 
}

$tipoSignIn="";
$service="";
$jumper=0;
$accessKey="1999";
$unlock = 0;

//in base al link preso dalla pagina di login si passera un valore Tipoutente che varierà il tipo di form che andremo ad compilare 
// questo verra settato nel cookie per garantire un uso corretto della pagina

if(isset($_GET['TipoUtente']) && !((isset($_POST['Iscriviti'])))){
    if ($_GET['TipoUtente'] == "0"){
        $tipoSignIn = 0;
        setcookie('tipoSignIn', "0");
    }
    else if($_GET['TipoUtente'] == "1"){
        $tipoSignIn = 1;
        setcookie('tipoSignIn', "1");
    }
    else if($_GET['TipoUtente'] == "2"){
        $tipoSignIn = 2;
        setcookie('tipoSignIn', "2");
    }

    else{
        header("Location: login.php");
    }
   
}
//il jumper vienere utilizzato come check finale dopo aver controllato che password email partita iva e data di nascita sono corretti
if(isset($_POST['signin']) && isset($_COOKIE['tipoSignIn'])){
    if($_COOKIE['tipoSignIn'] == 1){
        if(!(preg_match('/^[0-9]{12}+$/', $_POST['PIVA']))){
            $service=("Partita Iva non valida!");
            $jumper=1;
            $tipoSignIn = 1;

        }
    }
}
//se si vuole fare la registrazione come admin bisognera inserire una key di accesso speciale qui sotto si controlla usando la chiave d'accesso se la registrazione è regolare
if(isset($_POST['accessKey'])){
    if($_POST['accessKey'] == $accessKey) $unlock = 1;
}

//Quando si passano i vari check si procede alla procedee alla connessione al database 
if(isset($_POST['signin']) && $jumper==0){
    if(preg_match('/^.*@.*$/', $_POST['Email']) &&
        preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['DataNascita']) && 
        preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['Password']) ){
        $table_users="Tabella_Utenti";
        connectDB();
        $num=0;

        if (mysqli_connect_errno()){
            printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
        }
        //Si controlla se l'utente non è gia presente sul database 
        $queryLogin = "SELECT * FROM $table_users WHERE (Email='".$_POST['Email']."' OR Username='{$_POST['Nickname']}') AND Password='{$_POST['Password']}'";
        $resultQ = mysqli_query(connectDB(), $queryLogin);
        $num = mysqli_num_rows($resultQ);
        // se il numero delle righe presente al controllo della tabella utenti è maggiore di zero vuol dire che c'è gia una riga corrispondente 
        //a l'utente che si sta registrando quindi imposta il messaggio d'alert come utente gia registrato
        if($num > 0){
            $service=("Utente già registrato!");
        }
       
        else{
                if($_COOKIE['tipoSignIn'] == "2" && $unlock == 0) $service="Chiave di registrazione errata! Contattare la segreteria al numero +3906061225587";
                
                else{
                        if($_COOKIE['tipoSignIn'] == "0"){ //Controllando il cookie tipo Signin partono all'occorrenza 3 query sql che inseriscono nella tabella i dati scritti nella form
                        //in questo caso si inserisce un utente normale 
                        $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username,Esperienza, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente,imgProfiloPath)
                        VALUES
                        ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}',0,'{$_POST['DataNascita']}', 3, 0, 0, 0,'ProfilePic/propicblank.png')";
                        setcookie('tipoSignIn', "", time() - 3600);
                        }
                        else if($_COOKIE['tipoSignIn'] == "1"){//Accesso come  publisher che possiede una partita iva con il campo PIVA non vuoto 
                            $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username,Esperienza, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente,imgProfiloPath,imgProfiloPathPub, PIVA)
                        VALUES
                        ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}',0,'{$_POST['DataNascita']}', 3, 0, 0, 1,'ProfilePic/propicblank.png','ProfilePic/propicblank.png','{$_POST['PIVA']}')";
                        setcookie('tipoSignIn', "", time() - 3600);
                        }
                        else if($_COOKIE['tipoSignIn'] == "2"){//Accesso come admin con tipologia utente settata a 2
                            $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username,Esperienza, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente, imgProfiloPath)
                        VALUES
                        ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}',0,'{$_POST['DataNascita']}', 3, 0, 0, 2,'ProfilePic/propicblank.png')";
                        setcookie('tipoSignIn', "", time() - 3600);
                        }

                        // se non si connette da messaggio di errore
                        if (!$resultQ = mysqli_query(connectDB(), $sql)) {
                        echo("Query non partita! \n");
                        exit();
                        }
                        else {

                        // una volta fatto questo si prende l'id dell'utente appena generato e va a creare la seconda parte delle informazioni sotto forma di file XML

                            connectDB();

                            if (mysqli_connect_errno()) printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
                            
                            $nickname = mysqli_real_escape_string(connectDB(), $_POST['Nickname']);
                            $sql = "SELECT ID FROM $table_users WHERE username = '$nickname'";

                            $resultQ = mysqli_query(connectDB(), $sql);

                            if ($resultQ){
                                $row = mysqli_fetch_array($resultQ);
                            
                                $idUtente=$row['ID'];
                                // si prende dal risultato della query di selezione id e lo passiamo come variabile
                                $doc = getDoc('XML/utenti.xml');
                                $root=$doc->documentElement;
                                $elem=$root->childNodes;
                                $utente = $doc->createElement("Utente");
                                $utente->setAttribute("id_user", $idUtente); 
                                $utente->appendChild($doc->createElement("linkEsterno", ""));
                                $utente->appendChild($doc->createElement("DataIscrizione", date("d-m-Y")));
                                $utente->appendChild($doc->createElement("CasaDiSviluppoPreferita", "$_POST[CasaDiSviluppo]"));
                                $utente->appendChild($doc->createElement("GenerePreferito", "$_POST[Genere]"));
                                $utente->appendChild($doc->createElement("Descrizione"));
                                if ($_COOKIE['tipoSignIn'] == "1") $utente->appendChild($doc->createElement("ToggleAgency", false));
                                if ($_COOKIE['tipoSignIn'] == "1") $utente->appendChild($doc->createElement("DescrizionePublisher"));
                                $utente->appendChild($doc->createElement("listaGiochi"));
                                
                                $utente->appendChild($doc->createElement("listaPropic"));
                                //all interno dei nodi figli di utente si inseriscono i dati secondari "meno importanti" e vengono parzialmente popolati

                                $root->appendChild($utente);
                                $doc->save("XML/utenti.xml");
                                // infine vengono appesi all nodo radice e vengono salvati al nodo utente

                                $doc = getDoc('XML/ScontiAssegnati.xml');
                                $root=$doc->documentElement;
                                $elem=$root->childNodes;
                                $utente = $doc->createElement("Utente");
                                $utente->setAttribute("id_user", $idUtente);
                                $scontoAssBase = $doc->createElement("scontiAssegnati");
                                $utente->appendChild($scontoAssBase);
                                $root->appendChild($utente);
                                $doc->save("XML/ScontiAssegnati.xml");

                        
                                header("Location: login.php");
                            }
                            else printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
                            
                                
                            
                        
                        }
                }  
            }
            //settaggio di messaggi di errore: in base all'inserimento errato dell'email al momento dell'iscrizione
    } 
    else if(!(preg_match('/^.*@.*$/', $_POST['Email'])) && isset($_POST['signin']) && $jumper==0 && $unlock == 0){
        $service=("Email non valida!");
    }
    else if(!(preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['DataNascita'])) && isset($_POST['signin']) && $jumper==0 && $unlock == 0){
        $service=("Data di Nascita non valida!");
    }
    else if(!(preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['Password'])) && isset($_POST['signin']) && $jumper==0 && $unlock == 0){
        $service=("Password non valida! Deve contenere almeno una lettera maiuscola, un carattere speciale (!,@,=,&) ed essere lunga almeno 8 caratteri.");
    }
    
    
    
}
    

    ?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Registrazione - PixelHub</title>        
        <link rel="stylesheet" type="text/css" href="Stile/Registrazione.css?v=3" />
        <script>document.addEventListener('copy', e => e.preventDefault());
                document.addEventListener('cut', e => e.preventDefault());
                document.addEventListener('paste', e => e.preventDefault());
        </script> 
        
    </head>
    <body>
        <div id="logo">
                    <a href="Homepage.php">
                    <img src="Loghi/logo pixelhub slim.png" alt="logo pixelhub">
                    </a>
        </div>
        <div id="SigninCard">
            <div class="SigninForm">
                <h3>Inserisci le informazioni nei campi</h3>
                <h5>I campi affiancati con i pallini sono obligatori</h5>
                <!-- nella form verranno inserite le varie informazioni hanno diversi formati  -->
                
                <form action="signin.php" method="post">
                    <?php
                    if($service != ""){
                        echo "<div id='service'><p >$service</p></div>";
                    }  
                    ?>
                    <div>
                        <p>Nome</p>
                        <input type="text" placeholder="Mario" name="Nome" maxlength="50" required/>
                        &#x2022;
                    </div>
                    <div>
                        <p>Cognome</p>
                        <input type="text" placeholder="Rossi" name="Cognome" maxlength="50" required/>
                        &#x2022;
                    </div>
                    <div>
                        <p>Email</p>
                        <input type="text" placeholder="example@mail.com" name="Email" maxlength="100" required/>
                        &#x2022;
                    </div>
                    <!-- la password ha come placeholder dei pallini a simboleggiare subito il tipo di dato-->
                     <div>
                        <p>Password</p>
                        <input type="text" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" name="Password" required/>
                        &#x2022;
                    </div>
                    <div>
                        <p>Nickname</p>
                        <input type="text" placeholder="SuperBazinga666" name="Nickname" maxlength="50" required/>
                        &#x2022;
                        
                    </div>
                    <div>
                        <p>Data di Nascita</p>
                        <input type="text" placeholder="01-01-1980" name="DataNascita" maxlength="50" required/>
                        &#x2022;
                    </div>

                    <?php
                    // Questi appaiono solo nel caso si fa l'accesso come publisher o come admin
                    if($tipoSignIn == 1 || (isset($_COOKIE['tipoSignIn']) && $_COOKIE['tipoSignIn'] == "1")){
                        echo "<div id=\"Partitaiva\"> <p>Partita Iva</p> <input type=\"text\" title =\"Deve essere di 12 cifre \" placeholder=\"\" name=\"PIVA\" maxlenght=\"12\" required/>&#x2022;</div>";
                    }
                    ?>

                    <?php
                    if($tipoSignIn == 2 || (isset($_COOKIE['tipoSignIn']) && $_COOKIE['tipoSignIn'] == "2")){
                        echo "<div id=\"ChiaveAccesso\"> <p>Chiave di registrazione</p> <input type=\"text\" placeholder=\"&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;\" name=\"accessKey\" required/>&#x2022;</div>";
                    }
                    ?>

                    <div id="casaSviluppo">
                        <p>Casa Sviluppo preferita</p>
                        <input type="text" placeholder="DICE" name="CasaDiSviluppo" maxlength="50"/>
                    </div>

                    <div id="generePreferito">
                        <p>Genere preferito</p>
                        <select name="Genere">
                            <option value=""></option>
                            <option value="Sparatutto">Sparatutto</option> 
                            <option value="RPG">RPG</option>
                            <option value="Avventura">Avventura</option>
                            <option value="Souls-like">Souls-like</option>
                            <option value="Strategia">Strategia</option>
                            <option value="Rouge-like">Rouge-like</option>
                        </select>
                    </div>
                    <!-- si possono inserire come opzioni diversi generi che andranno a dare eventuali sconti all'utente o comunque mostrano all'inizio dell' home page la categoria selezionata -->
                    <div>
                        <input type="submit" name="signin" value="Iscriviti" />
                    </div>
                    
                </form>

            </div>
                    
        </div>
        <div id="footer">
            <ul>
                <li><a href="Contact.php">Contact Us</a></li>
                <li><a href="Faq.php">F.A.Q</a></li><br/>
                <li>&copy; 2026 Pixel Hub. Tutti i diritti riservati.</li>
            </ul>
        </div>
    </body>
</html>