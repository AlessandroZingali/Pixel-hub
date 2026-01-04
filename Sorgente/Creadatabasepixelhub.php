<?php			
error_reporting(E_ALL &~E_NOTICE);

$db_name = "Database_Pixel_Hub";
$table_users = "Tabella_Utenti";


$mysqliConnection = new mysqli("localhost", "Alessandro", "belandi");
//$mysqliConnection= new mysqli("localhost","archer","archer");

if (mysqli_connect_errno()){

    printf("problemi di connessione : %s\n", mysqli_connect_error());
}
else{

    printf("Connessione avvenuta con successo ...\n");
    $queryCreazioneDatabase = "CREATE DATABASE $db_name";

    if ($resultQ = mysqli_query($mysqliConnection, $queryCreazioneDatabase)) {
        
        printf("Database creato ...\n");
        $sqlQuery = "CREATE TABLE $table_users ( 
        ID  INT  AUTO_INCREMENT, 
        Email  VARCHAR  (100), 
        Password  VARCHAR  (100), 
        Username  VARCHAR  (50), 
        Grado  INT, 
        Pixels  INT, 
        Saldo_attuale  FLOAT, 
        Data_di_Nascita  VARCHAR(50), 
        Nome  VARCHAR  (50), 
        Cognome VARCHAR (50), 
        Tipologia_utente INT,  
        PIVA  VARCHAR (12), 
        PRIMARY  KEY  (ID, Email),
        UNIQUE  KEY  Email_UNIQUE  (Email) 
        ); 
        ";
        $mysqliConnection->close();

        $mysqliConnection = new mysqli("localhost", "Alessandro", "belandi", $db_name);
//        $mysqliConnection = new mysqli("localhost","archer","archer",$db_name);
        if ($resultQ = mysqli_query($mysqliConnection, $sqlQuery)){

            printf("Ho creato la tabella Utenti ...\n");

            $sqlQuery = "INSERT INTO $table_users 
            (Email, Password, Username, Grado, Pixels, Saldo_attuale, Data_di_Nascita, Nome, Cognome, Tipologia_utente, PIVA) 
            VALUES 
            (\"marcorossi@gmail.com\", \"marcorossi123\", \"marcorossi\", 1, 1000, 50.0, \"15/04/1990\", \"Marco\", \"Rossi\", 1, \"231231240\"),
            (\"gabibbo@gmail.com\", \"gabibbo123\", \"gabibbo\", 2, 5000, 200.0, \"20/06/1985\", \"Gabriele\", \"Bianchi\", 2, \"12345678901\");
            ";

            if ($resultQ=mysqli_query($mysqliConnection, $sqlQuery)){
                printf("Installazione DB effettuata! :-)");
            }
            else{
                printf("Errore popolamento DB.");
            }
        }
        
        else {
            printf("Ho avuto un problema per creare la tabella utenti.\n");
        }
    }
    else {
        printf("Ho avuto un problema per creare il database.\n");
    }

    
}

if($mysqliConnection){
    $mysqliConnection->close();
}

?>


 



    


