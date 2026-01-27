<?php
function connectDB(){
    //nome del database ovviamente dovra essere uguale in crea database 
    $db_name = "Database_Pixel_Hub";
    $table_users = "Tabella_Utenti";
    // inserire per una nuova installazione il nome utente per il mariaDB e la password
    $usernameDB = "Alessandro";
    $passwordDB = "belandi";

    $mysqliConnection = new mysqli("localhost", $usernameDB, $passwordDB, $db_name);
    return $mysqliConnection;
}
?>