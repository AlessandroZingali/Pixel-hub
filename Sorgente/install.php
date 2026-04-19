<?php
// Questo file php inizializza il database in localhost per il sito e 
// Mostra tutti gli errori tranne i NOTICE (come ad esempio variabili non inizializzate)
error_reporting(E_ALL & ~E_NOTICE);

include 'serverUtility.php'; // Includo il file di utilità per la connessione al database e altre funzioni utili

$pointDB = new connectionDB(); // Creo un oggetto per la connessione al database, se necessario in futuro

$pointDB->resetDB();


$mysqliConnection = $pointDB->installDB(); // Connessione al database, per l'installazione

// Controllo errori di connessione
if ($mysqliConnection->connect_error) printf("Problemi di connessione : %s\n", $mysqliConnection->connect_error);
else {

    // Connessione riuscita
    printf("Connessione avvenuta con successo ...\n");

    // Query per creare il database
    $queryCreazioneDatabase = "CREATE DATABASE {$pointDB->getDbName()}";

    // Esecuzione della query di creazione database
    if ($resultQ = mysqli_query($mysqliConnection, $queryCreazioneDatabase)) {

        printf("Database creato ...\n");

        // Chiudo la connessione senza database
        $pointDB->close($mysqliConnection);
        

        // Nuova connessione, questa volta al database appena creato
        $mysqliConnection = $pointDB->connectDB(); 

        // Query per creare la tabella utenti
        $sqlQuery = "CREATE TABLE {$pointDB->getTableUsers()} ( 
            ID INT AUTO_INCREMENT,                 -- ID univoco utente
            Email VARCHAR(100),                   -- Email utente
            Password VARCHAR(100),                -- Password utente
            Username VARCHAR(50),                 -- Username
            Esperienza INT,                       -- Esperienza utente
            Grado INT,                            -- Grado utente (1-6) di base parte a 3
            Pixels INT,                           -- Valuta virtuale
            Saldo_attuale FLOAT,                  -- Saldo reale
            Data_di_Nascita VARCHAR(10),          -- Data di nascita
            Nome VARCHAR(50),                     -- Nome
            Cognome VARCHAR(50),                  -- Cognome
            Tipologia_utente INT,                 -- 0=user 1=publisher 2=admin
            imgProfiloPath VARCHAR(250),          -- Percorso immagine profilo
            imgProfiloPathPub VARCHAR(250),       -- Percorso immagine profilo Publisher
            PIVA VARCHAR(12),                     -- Partita IVA
            PRIMARY KEY (ID),                     -- Chiave primaria
            UNIQUE (Email)                         -- Email unica
        );";

        // Creazione della tabella
        if ($resultQ = mysqli_query($mysqliConnection, $sqlQuery)) {

            printf("Ho creato la tabella Utenti ...\n");

            // Query di inserimento utenti di esempio
            $sqlQuery = "INSERT INTO {$pointDB->getTableUsers()} 
            (Email, Password, Username, Grado,Esperienza, Pixels, Saldo_attuale, Data_di_Nascita, Nome, Cognome, Tipologia_utente, imgProfiloPath,imgProfiloPathPub, PIVA) 
            VALUES 
            (
            -- i 3 utenti hanno rispettivamente grado uno due e tre 
            -- per test del sito e le varie sezioni che sono disponibibili o meno in base al comportamento dell'utente 
                \"marcorossi@gmail.com\",  
                \"Marcorossi123!\", 
                \"marcorossi\", 
                1, 
                0,
                0, 
                200.0, 
                \"12-03-1992\", 
                \"Marco\", 
                \"Rossi\", 
                0, 
                \"ProfilePic/propicblank.png\", 
                NULL,
                NULL
            ),(
                \"bandasoft@gmail.com\", 
                \"Banda1234!\", 
                \"Bandai Namco Entertainment\", 
                2, 
                500,
                0, 
                200.0, 
                \"23-10-2009\", 
                \"Luca\", 
                \"Verdi\", 
                1, 
                \"ProfilePic/propicblank.png\", 
                \"ProfilePic/propicblank.png\",
                \"123456789012\"
            ),(
                \"gabibbo@gmail.com\", 
                \"Gabibbo123!\", 
                \"MastroGabibbo\", 
                3, 
                1000,
                0, 
                200.0, 
                \"23-12-1989\", 
                \"Mauro\", 
                \"Bianchi\", 
                2, 
                \"ProfilePic/propicblank.png\", 
                NULL,
                NULL
            ),(
                \"virgilpot@libero.it\",
                \"GarndePot&11\",
                \"PotMaster8\",
                4,
                2309,
                608,
                438.27,
                \"14-07-1994\",
                \"Virgil\",
                \"Arco\",
                0,
                \"ProfilePic/Shadow.png\",
                NULL,
                NULL
            ),(
                \"gallochiatto11@libero.it\",
                \"ER!Gallito!Pancito3\",
                \"GalloSupremo67\",
                4,
                2728,
                1136,
                354.3,
                \"10-08-1991\",
                \"Gary\",
                \"Parco\",
                1,
                \"ProfilePic/Yoshi.png\",
                \"ProfilePic/propicblank.png\",
                453456889042
            ),(
                \"zingar00@gmail.com\",
                \"SuperMarco09!\",
                \"MarcoZ10\",
                6,
                4336,
                400,
                4359.0,
                \"10-10-1990\",
                \"Marco\",
                \"Zingaretti\",
                0,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            ),(
                \"maintheruler00@libero.it\",
                \"MainGagstar000&&\",
                \"MainGansta99\",
                5,
                3285,
                284,
                573.01,
                \"14-07-1999\",
                \"Roberto\",
                \"Manzoni\",
                2,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            ),(
                \"OsterNano_04@libero.it\",
                \"OsteriBoss01\",
                \"TheBoss11\",
                4,
                2830,
                284,
                200.0,
                \"01-01-1995\",
                \"Oster\",
                \"Nano\",
                0,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            ),(
                \"textbase22@protonmail.it\",
                \"SUperSU00@\",
                \"LukeSkyWalkerMiaMadre\",
                5,
                3535,
                2692,
                111.08,
                \"15-05-1985\",
                \"Luke\",
                \"Skywalker\",
                0,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            ),(
                \"BlizzardEnt_Ita@BlizzardInc.euCorp.it\",
                \"FuoriJeffKaplan01!\",
                \"Blizzard Ent.\",
                3,
                1780,
                779,
                200.0,
                \"01-12-2000\",
                \"Blizzard\",
                \"Entertainment\",
                1,
                \"ProfilePic/propicblank.png\",
                \"loghiPub/Blizzard Ent._logo.png\",
                \"123456782012\"
            ),(
                \"kinger@gmail.it\",
                \"Amazingcircus1!\",
                \"Kingler\",
                6,
                9000,
                0,
                500.0,
                \"01-01-1980\",
                \"Gran\",
                \"Royal\",
                2,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            ),(
                \"CircoDigitale67@virgilio.com\",
                \"Caineilpiu&sexydituttinoi69\",
                \"CaineRuler1\",
                3,
                1954,
                952,
                309.28,
                \"01-01-1996\",
                \"Caino\",
                \"Abel\",
                0,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            ),(
                \"ElFrancone@gmail.com\",
                \"CuttyFlan01!\",
                \"ColaDrinker1!\",
                5,
                3100,
                0,
                500.0,
                \"01-01-1990\",
                \"Gianfranco\",
                \"Franchini\",
                0,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            ),(
                \"Yotosbotox00@protonmail.it\",
                \"YYtob88ix&x11\",
                \"YotobiFake\",
                3,
                1000,
                0,
                500,
                \"03-03-1950\",
                \"Karim\",
                \"Musa\",
                0,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            ),(
                \"aaaa@libero.it\",
                \"Aaaaaaaa1!\",
                \"ARMine77\",
                3,
                1000,
                0,
                500,
                \"10-02-1990\",
                \"Gino\",
                \"Arnaldi\",
                0,
                \"ProfilePic/propicblank.png\",
                NULL,
                NULL
            )
            ;";

            // Esecuzione inserimento dati
            if ($resultQ = mysqli_query($mysqliConnection, $sqlQuery)) {
                printf("Installazione DB effettuata! :-)");
            }
            else printf("Errore popolamento DB.");
            
        }
        else printf("Ho avuto un problema per creare la tabella utenti.\n");
    }
    else printf("Ho avuto un problema per creare il database.\n");
    
}

// Chiusura connessione se ancora aperta
if ($mysqliConnection) {
    $pointDB->close($mysqliConnection);
}
?>



 



    


