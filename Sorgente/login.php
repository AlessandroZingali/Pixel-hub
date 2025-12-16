<?php  
error_reporting(E_ALL &~E_NOTICE);

$db_name = "Database_Pixel_Hub";
$table_users = "Tabella_Utenti";
$mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

if (mysqli_connect_errno()){

    printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
}

if(isset($_POST['Accedi'])){

    $emailNickname = $_POST['EmailNickname'];
    $password = $_POST['Password'];

    $queryLogin = "SELECT * FROM $table_users WHERE (Email='$emailNickname' OR Username='$emailNickname') AND Password='$password'";

    $resultQ = mysqli_query($mysqliConnection, $queryLogin);

    $num_rows = mysqli_num_rows($resultQ);

    if($num_rows == 1){

        session_start();
        $_SESSION['userId']
        $_SESSION['user'] = $emailNickname;
        header("Location: home.php");
    }
    else{
        echo "Credenziali non valide. Riprova.";
    }
}
?>




<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Login - PixelHub</title>        
        <link rel="stylesheet" type="text/css" href="Stile/Login.css?v=1" /> 
        
    </head>
    <body>
        <div id="LoginCard">
            <div class="loginForm">
                <div id="logo">
                    <img src="Loghi/logo pixelhub slim.png" alt="logo pixelhub">
                </div>
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
                        <input type="submit" name="Accedi" value="Accedi" />
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