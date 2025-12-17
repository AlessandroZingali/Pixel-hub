<?php  
error_reporting(E_ALL &~E_NOTICE);



if(isset($_SESSION)){
   session_unset($_SESSION);
   session_destroy(); 
}

$tipoSignIn="";


if(isset($_GET['TipoUtente']) && !((isset($_POST['Iscriviti'])))){
    if ($_GET['TipoUtente'] == "0"){
       // echo "<p>sono il tipo utente 0</p>";
        $tipoSignIn = 0;
        setcookie('tipoSignIn', "0");
    }
    else if($_GET['TipoUtente'] == "1"){
                echo "<p>sono il tipo utente 1</p>";
        $tipoSignIn = 1;
        setcookie('tipoSignIn', "1");
    }
    else if($_GET['TipoUtente'] == "2"){
                echo "<p>sono il tipo utente 2</p>";
        $tipoSignIn = 2;
        setcookie('tipoSignIn', "2");
    }
}



if(isset($_POST['signin'])){

    $db_name = "Database_Pixel_Hub";
    $table_users = "Tabella_Utenti";
    $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);

    if (mysqli_connect_errno()){
        printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
    }

    if($_COOKIE['tipoSignIn'] == "0"){
        $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente)
    VALUES
    ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}','{$_POST['DataNascita']}', 2, 0, 0, 0)";
    setcookie('tipoSignIn', "", time() - 3600);
    }
    else if($_COOKIE['tipoSignIn'] == "1"){
        $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente, PIVA)
    VALUES
    ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}','{$_POST['DataNascita']}', 2, 0, 0, 1,'{$_POST['PIVA']}')";
    setcookie('tipoSignIn', "", time() - 3600);
    }
    else if($_COOKIE['tipoSignIn'] == "2"){
        $sql="INSERT INTO $table_users (Nome, Cognome, Email, Password, Username, Data_di_Nascita, Grado, Pixels, Saldo_attuale, Tipologia_utente)
    VALUES
    ('{$_POST['Nome']}','{$_POST['Cognome']}','{$_POST['Email']}','{$_POST['Password']}','{$_POST['Nickname']}','{$_POST['DataNascita']}', 2, 0, 0, 2)";
    setcookie('tipoSignIn', "", time() - 3600);
    }
    
    if (!$resultQ = mysqli_query($mysqliConnection, $sql)) {
    echo("Query non partita! \n");
    exit();
    }
    else {

        
        echo("Registrazione effettuata!");
        header("Location: login.php");
        
    };

    
}
    ?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Registrazione - PixelHub</title>        
        <link rel="stylesheet" type="text/css" href="Stile/Registrazione.css?v=1" /> 
        
    </head>
    <body>
        <div id="logo">
                    <img src="Loghi/logo pixelhub slim.png" alt="logo pixelhub">
                </div>
        <div id="LoginCard">
            <div class="loginForm">
                
                <form action="signin.php" method="post">
                    <div id="Nome">
                        <p>Nome</p>
                        <input type="text" placeholder="Mario" name="Nome"/>
                    </div>
                    <div id="Cognome">
                        <p>Cognome</p>
                        <input type="text" placeholder="Rossi" name="Cognome"/>
                    </div>
                    <div id="Email">
                        <p>Email</p>
                        <input type="text" placeholder="example@mail.com" name="Email"/>   
                    </div>
                    <div id="Password">
                        <p>Password</p>
                        <input type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" name="Password"/>
                    </div>
                    <div id="Nickname">
                        <p>Nickname</p>
                        <input type="text" placeholder="SuperBazinga666" name="Nickname"/>
                        
                    </div>
                    <div id="DataNascita">
                        <p>Data di Nascita</p>
                        <input type="text" placeholder="01-01-1980" name="DataNascita"/>
                    </div>

                    <?php
                    if($tipoSignIn == 1){
                        echo "<div id=\"Partitaiva\"> <p>Partita Iva</p> <input type=\"text\" placeholder=\"\" name=\"PIVA\" /></div>";
                    }
                    ?>

                    <div>
                        <input type="submit" name="signin" value="Iscriviti" />
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