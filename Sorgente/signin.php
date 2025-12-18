<?php  
error_reporting(E_ALL &~E_NOTICE);


if(isset($_SESSION)){
   session_unset($_SESSION);
   session_destroy(); 
}

$tipoSignIn="";
$service="";
$jumper=0;


if(isset($_GET['TipoUtente']) && !((isset($_POST['Iscriviti'])))){
    if ($_GET['TipoUtente'] == "0"){
       // echo "<p>sono il tipo utente 0</p>";
        $tipoSignIn = 0;
        setcookie('tipoSignIn', "0");
    }
    else if($_GET['TipoUtente'] == "1"){
              //  echo "<p>sono il tipo utente 1</p>";
        $tipoSignIn = 1;
        setcookie('tipoSignIn', "1");
    }
    else if($_GET['TipoUtente'] == "2"){
             //   echo "<p>sono il tipo utente 2</p>";
        $tipoSignIn = 2;
        setcookie('tipoSignIn', "2");
    }
}

if(isset($_POST['signin']) && isset($_COOKIE['tipoSignIn'])){
    if($_COOKIE['tipoSignIn'] == 1){
        if(!(preg_match('/^[0-9]{12}+$/', $_POST['PIVA']))){
            $service=("Partita Iva non valida!");
            $jumper=1;
            $tipoSignIn = 1;

        }
    }
}



if(isset($_POST['signin']) && $jumper==0){
    if(preg_match('/^.*@.*$/', $_POST['Email']) &&
 preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['DataNascita']) && 
 preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['Password']) ){
        $db_name = "Database_Pixel_Hub";
        $table_users = "Tabella_Utenti";
        $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);
        $num=0;

        if (mysqli_connect_errno()){
            printf("problemi di connessione : %s\n", mysqli_connect_error($mysqliConnection));
        }

        $queryLogin = "SELECT * FROM $table_users WHERE (Email='$emailNickname' OR Username='$emailNickname') AND Password='$password'";
        $resultQ = mysqli_query($mysqliConnection, $queryLogin);
        $num = mysqli_num_rows($resultQ);

        if($num > 0){
            $service=("Utente già registrato!");
        }
        else{
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
                    
                }
        }  
    }
    else if(!(preg_match('/^.*@.*$/', $_POST['Email'])) && isset($_POST['signin']) && $jumper==0){
        $service=("Email non valida!");
    }
    else if(!(preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $_POST['DataNascita'])) && isset($_POST['signin']) && $jumper==0){
        $service=("Data di Nascita non valida!");
    }
    else if(!(preg_match('/^(?=.*[A-Z])(?=.*[!@=&])[A-Za-z0-9!@=&]{8,}$/', $_POST['Password'])) && isset($_POST['signin']) && $jumper==0){
        $service=("Password non valida! Deve contenere almeno una lettera maiuscola, un carattere speciale (!,@,=,&) ed essere lunga almeno 8 caratteri.");
    }
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
                    <?php
                    if($service != ""){
                        echo "<div id='service'><p >$service</p></div>";
                    }  
                    ?>
                    <div id="Nome">
                        <p>Nome</p>
                        <input type="text" placeholder="Mario" name="Nome" maxlength="50"/>
                    </div>
                    <div id="Cognome">
                        <p>Cognome</p>
                        <input type="text" placeholder="Rossi" name="Cognome" maxlength="50"/>
                    </div>
                    <div id="Email">
                        <p>Email</p>
                        <input type="text" placeholder="example@mail.com" name="Email" maxlength="100"/>   
                    </div>
                    <div id="Password">
                        <p>Password</p>
                        <input type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" name="Password"/>
                    </div>
                    <div id="Nickname">
                        <p>Nickname</p>
                        <input type="text" placeholder="SuperBazinga666" name="Nickname" maxlength="50"/>
                        
                    </div>
                    <div id="DataNascita">
                        <p>Data di Nascita</p>
                        <input type="text" placeholder="01-01-1980" name="DataNascita" maxlength="50"/>
                    </div>

                    <?php
                    if($tipoSignIn == 1){
                        echo "<div id=\"Partitaiva\"> <p>Partita Iva</p> <input type=\"text\" placeholder=\"\" name=\"PIVA\" maxlenght=\"12\"/></div>";
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