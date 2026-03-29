<?php
error_reporting(E_ALL &~E_NOTICE);
/*File che presenta al suo interno funzioni utili al sito, come ad esempio ad evitare di riscrivere lo stasso codice più volte 
come la funzione che carica un file XML e lo restituisce come oggetto DOMDocument, o la funzione che restituisce la root di un file XML,
la funzione che restituisce il primo livello di nodi figli della root di un file XML o la funzione che setta 
il limite massimo di giochi da mostrare in ogni slider.*/




class connectionDB{
    private $host_name;
    private $db_name;
    private $table_users;
    private $usernameDB;
    private $passwordDB;

    function __construct()
    {        
        $this->host_name = "localhost";
        $this->db_name = "dabello";
        $this->table_users = "Tabella_Utenti";
        $this->usernameDB = "archer";
        $this->passwordDB = "archer";
    }
    function connectDB(){
        $mysqliConnection = new mysqli($this->host_name, $this->usernameDB, $this->passwordDB, $this->db_name);
        return $mysqliConnection;
    }

    function installDB(){
        $mysqliConnection = new mysqli($this->host_name, $this->usernameDB, $this->passwordDB);
        return $mysqliConnection;
    }

    function getDbName(){
        return $this->db_name;
    }

    function getTableUsers(){
        return $this->table_users;
    }
    function close($mysqliConnection){
        $mysqliConnection->close();
    }

}

/*La seguente classe contiene le variabili di base per la risposta dei ticket tramite Email. Se si vuole provare lo script 
per la risposta via mail, basta cambiare il mittente insieme alla key del servizio SMTP, e cambiare anche il destinatario.
Consigliamo di cambiare solo il destinatario, mettendo la propria mail*/ 
class emailPointer{
    public $mittente;
    public $destinatario;
    public $emailKey;
   

    function __construct()
    {
        $this->mittente = 'justfree609@gmail.com';
        $this->destinatario = 'tuliniriccardo99@gmail.com'; //CAMBAIRE SOLO QUESTA PER VERIFICARE IL FUNZIONAMENTO
        $this->emailKey = 'wmtb kgff vkfl acnj';
    }
}
function getDoc($file){
    $xmlString="";
                                
    foreach(file($file) as $node){ 
        $xmlString .= trim($node);
    }
    
    $doc= new DOMDocument();
    $doc->loadXML($xmlString);
    $doc->formatOutput = true;
    return $doc;
}
// funzione che associa direttamente la root di un documento XML 
function getRoot($file){
    $doc = getDoc($file);
    $root=$doc->documentElement;
    return $root;
}
// funzione semplice che restituisce il primo elemento figlio della radice di un file XML
function xmlPointer($file){
    $root = getRoot($file);
    $elem = $root->childNodes;

    return $elem;
}

//Imposto un limite massimo di giochi da mostrare in ogni slider
function setLimiteSlider($elem){
    $limite=$elem->length;
    return $limite;
}




?>