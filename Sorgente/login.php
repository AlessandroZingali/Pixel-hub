<?php  
error_reporting(E_ALL &~E_NOTICE);

if(isset($_SESSION)){
   session_unset($_SESSION);
   session_destroy(); 
}


$esitovuoto="I campi sono vuoti";
$esitoerrore="Email e/o password errati";
$flag=1;
setcookie("userConnect", "false");
if(isset($_POST['Accedi']) && (!isset($_SESSION))){

    $db_name = "Database_Pixel_Hub";
    $table_users = "Tabella_Utenti";
    $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

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
        $_SESSION['userId']  =$row['ID'];
        $_SESSION['user'] = $emailNickname;
        $_COOKIE['userConnect'] = "true";
    
        
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
        <script type="text/javascript" src="Script/esito.js"></script>
        
    </head>
    <body>
        <div id="LoginCard">
            <div class="loginForm">
                <div id="logo">
                    <img src="Loghi/logo pixelhub slim.png" alt="logo pixelhub">
                </div>
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
                        <p>Sei nuovo? <a href="signin.php">Sign In</a></p>
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