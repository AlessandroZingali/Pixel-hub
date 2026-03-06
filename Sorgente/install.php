<?php
// Questo file php inizializza il database in localhost per il sito e 
// Mostra tutti gli errori tranne i NOTICE (come ad esempio variabili non inizializzate)
error_reporting(E_ALL & ~E_NOTICE);

include 'serverUtility.php'; // Includo il file di utilità per la connessione al database e altre funzioni utili

$pointDB = new connectionDB(); // Creo un oggetto per la connessione al database, se necessario in futuro

$mysqliConnection = $pointDB->connectDB(); // Connessione al database


// Controllo errori di connessione
if (mysqli_connect_errno()) {

    // Stampa il messaggio di errore
    printf("Problemi di connessione : %s\n", mysqli_connect_error());
}
else {


    //Per debug/necessita di resettare il server per una modifica nella sua struttura
    $mysqliConnection = $pointDB->connectDB(); // Connessione al database

    $queryControllo ="DROP DATABASE IF EXISTS {$pointDB->getDbName()}"; // Query per eliminare il database se esiste già, in modo da poterlo ricreare da zero (utile per test o modifiche alla struttura del databa
    if ($resultQ = mysqli_query($mysqliConnection, $queryControllo)) {
	printf("Database eliminato...<br /> ");
  
    }
    else {
        printf("Database non esistente.");
    }

    // Connessione riuscita
    printf("Connessione avvenuta con successo ...\n");

    // Query per creare il database
    $queryCreazioneDatabase = "CREATE DATABASE {$pointDB->getDbName()}";

    // Esecuzione della query di creazione database
    if ($resultQ = mysqli_query($mysqliConnection, $queryCreazioneDatabase)) {

        printf("Database creato ...\n");

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
            Data_di_Nascita VARCHAR(50),          -- Data di nascita
            Nome VARCHAR(50),                     -- Nome
            Cognome VARCHAR(50),                  -- Cognome
            Tipologia_utente INT,                 -- 1=admin 2=user 3=publisher
            imgProfiloPath VARCHAR(250),          -- Percorso immagine profilo
            imgProfiloPathPub VARCHAR(250),       -- Percorso immagine profilo Publisher
            PIVA VARCHAR(12),                     -- Partita IVA
            PRIMARY KEY (ID, Email),              -- Chiave primaria composta
            UNIQUE KEY Email_UNIQUE (Email)       -- Email unica
        );";

        // Chiudo la connessione senza database
        $pointDB->close($mysqliConnection);
        

        // Nuova connessione, questa volta al database appena creato
        $mysqliConnection = $pointDB->connectDB(); 
        

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
                \"marcorossi123\", 
                \"marcorossi\", 
                1, 
                0,
                0, 
                200.0, 
                \"15-04-1990\", 
                \"Marco\", 
                \"Rossi\", 
                0, 
                \"ProfilePic/propicblank.png\", 
                NULL,
                NULL
            ),(
                \"bandasoft@gmail.com\", 
                \"banda1234!\", 
                \"Bandai Namco Entertainment\", 
                3, 
                0,
                0, 
                200.0, 
                \"30-02-1995\", 
                \"Luca\", 
                \"Verdi\", 
                1, 
                \"ProfilePic/propicblank.png\", 
                \"ProfilePic/propicblank.png\",
                \"123456789012\"
            ),(
                \"gabibbo@gmail.com\", 
                \"gabibbo123\", 
                \"MastroGabibbo\", 
                2, 
                0,
                0, 
                200.0, 
                \"20-06-1985\", 
                \"Mauro\", 
                \"Bianchi\", 
                2, 
                \"ProfilePic/propicblank.png\", 
                NULL,
                NULL
            );";

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



 



    


