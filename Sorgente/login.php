<?php
error_reporting(E_ALL &~E_NOTICE);
session_start();
if(isset($_COOKIE{'tipoSignIn'})) setcookie('tipoSignIn', "", time() - 3600);;

if(isset($_SESSION['userId'])){
    session_unset();
    session_destroy(); 
}


$esitovuoto="I campi sono vuoti";
$esitoerrore="Email e/o password errati";
$flag=1;
if(isset($_POST['Accedi']) ){
session_start();
    $db_name = "Database_Pixel_Hub";
    $table_users = "Tabella_Utenti";
    $mysqliConnection = new mysqli("localhost", "archer", "archer", $db_name);

    if (mysqli_connect_errno()){

        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }
    $emailNickname = $_POST['EmailNickname'];
    $password = $_POST['Password'];

    $queryLogin = "SELECT * FROM $table_users WHERE (Email='$emailNickname' OR Username='$emailNickname') AND Password='$password'";
    $resultQ = mysqli_query($mysqliConnection, $queryLogin);
    $num = mysqli_num_rows($resultQ);

    if($num == 1){
        $flag=1;
        session_start();
        $row=mysqli_fetch_array($resultQ);

        $xmlString="";
                                
        foreach(file("XML/utenti.xml") as $node){ 
            $xmlString .= trim($node);
        }
        
        $doc= new DOMDocument();
        $doc->loadXML($xmlString);
        $root=$doc->documentElement;
        $elem=$root->childNodes;

        foreach($elem as $i){
            if($i->getAttribute('id_user') == $row['ID']){
                if($i->getElementsByTagName('GenerePreferito')->item(0)->textContent != '') $_SESSION['generePreferito'] = $i->getElementsByTagName('GenerePreferito')->item(0)->textContent;
                }
            }
    

        $_SESSION['userId'] = $row['ID'];
        $_SESSION['user'] = $emailNickname;
        $_SESSION['userName']=$row['Username'];
        $_SESSION['tipoUtente'] = $row['Tipologia_utente'];
        
        
    
        
        header("Location: Home.php");
    }

    else if($emailNickname==="" ?? $password ===""){
         $flag=2;
    }

    else if($num<1){
        $flag=3;
    }
}
    

?>




<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Login - PixelHub</title>        
        <link rel="stylesheet" type="text/css" href="Stile/Login.css?v=1" />
        <script type="text/javascript" src="Script/estensioneLink.js"></script>
        
    </head>
    <body>
        <div id="logo">
                    <img src="Loghi/logo pixelhub slim.png" alt="logo pixelhub" onclick="location.href='Home.php'"/>
                </div>
        <div id="LoginCard">
            <div class="loginForm">
                
                <?php

                    if($flag == 2){
                    echo "<div id=\"esito\"> <p>$esitovuoto</p> </div>"; 
                    }
                    if($flag == 3){
                    echo "<div id=\"esito\"> <p>$esitoerrore</p> </div>"; 
                    }
                    
                    ?>
                
                <form action="login.php" method="post">
                    
                    <div id="Email">
                        <p>Email or Nickname</p>
                        <input type="text" placeholder="example@mail.com" name="EmailNickname"/>
                        
                    </div>
                    <div id="Password">
                        <p>Password</p>
                        <input type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" name="Password"/>
                    </div>
                    <div class="recovery">
                        <p>Hai dimeticato le tue <a href="recuperoCredenziali.php">credenziali</a>?</p>
                        <p>Sei nuovo?   <a href="signin.php?TipoUtente=0">Iscriviti!</a></p>
                        <p>Sei un nuovo    <a href="signin.php?TipoUtente=1">publisher</a>?</p>     
                        <p>Sei un nuovo    <a href="signin.php?TipoUtente=2">admin</a>?</p>


                    </div>

                    <div>
                        <input id="submit" type="submit" name="Accedi" value="Accedi"  />
                    </div>
                    
                   
                </form>
            </div>
            <div id="footer">
                <ul>
                    <li><a href="">Contact Us</a></li>
                    <li><a href="">F.A.Q</a></li><br>
                    <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
                 </ul>
            </div>
        </div>
    </body>
</html>