<?php

require 'serverUtility.php'; //Inclusione del file per la gestione del puntatore XML, il quale restituira la lista dei nodi figli della root all'interno del file XML stesso

 $pointDB = new connectionDB();

if (mysqli_connect_errno()) {
    printf("problemi di connessione : %s\n", mysqli_connect_error());
}
$utente = $_GET["User"] ?? "";
$sql = "SELECT * FROM ".$pointDB->getTableUsers()." WHERE Username LIKE \"%$utente%\";";
$resultQ = mysqli_query($pointDB->connectDB(), $sql);
$rows = mysqli_fetch_all($resultQ);




$output = "";

// Inizializza una stringa vuota per contenere i risultati della ricerca

if (strlen($utente) > 0) {
    // Cicla attraverso tutti i giochi nel file XML
    foreach ($rows as $row) {
        $username = (string)$row[3];
        if (stripos($username, $utente) !== false) {
            $output .= "<p class=\"risultatoSearchUtenti\" onclick='insertId(this, {$row[0]})'>{$row[3]}</p>";
        }
    }
}
//var_dump($rows);
// se non trova nulla mostra semplicemente nella finestra nessun risultato
echo $output === "" ? "Nessun risultato" : $output;
?>