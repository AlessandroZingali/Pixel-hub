/*Questo file gestice lo swapp tra le card della pagina del profilo. Le card riquandano le impostazioni utente e lo shop delle immagini profilo. 
Inoltre questo stesso script gestisce i messaggi di errore nel caso di impostazioni errate oppure impossibilita dell'acquisto di una nuova Profile pic */

//Gestione evento al caricamento, nel caso la logica iniziale alla pagina profilo hanno restituito un errore sul cambio impostazioni
window.addEventListener("load", invalidation);

function swapperInSettings(){ //Funzione per lo scambio di card tra profilo e impostazioni
    var cardProfilo = document.getElementById("card1"); //Prendo la card del profilo tramite il document
    console.log("Ris: "+cardProfilo);
    var cardSettings = document.getElementById("card2"); //Prendo la card delle impostazioni tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardProfilo.classList.toggle("hideCard"); 
    cardSettings.classList.toggle("hideCard");
}

function swapperInStore(){ //Funzione per lo scambio di card tra profilo e store
    var cardProfilo = document.getElementById("card1"); //Prendo la card del profilo tramite il document
    console.log("Ris: "+cardProfilo);
    var cardStore = document.getElementById("card3"); //Prendo la card dello store tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardProfilo.classList.toggle("hideCard");
    cardStore.classList.toggle("hideCard");
}

//Funzione per la gestione degli errori restituiti dalle logiche iniziali alla pagina profilo
function invalidation(){
    //Prendo il flag dal localStorage, salvato precederentemente 
    //dalla logica iniziali della pagina profilo. Essendo salvato come stringa,
    //controllo se è NaN per settare il valore a 0 (nessun errore)
    $flag = isNaN(localStorage.getItem("invalidFlag")) ? 0 : localStorage.getItem("invalidFlag"); 

    if($flag == 1){ //Caso Errore inserimento nuova email
        swapperInSettings(); //Richiamo la funzione per lo scambio di card tra profilo e impostazioni

        //Mostro l'alert dopo mezzo secondo per permettere il caricamento della pagina
        setTimeout(function () {
         alert('Email Errata'); }, 500);
        
        localStorage.removeItem("invalidFlag"); //Rimuovo il flag dal localStorage

    } 
    else if($flag == 2){ //Caso Errore inserimento nuova password
        swapperInSettings();

        setTimeout(function () {
         alert('Password Errata'); }, 500);
        
        localStorage.removeItem("invalidFlag");
    } 
    else if($flag == 3){  //Caso Fondi insufficienti per l'acquisto dell'immagine profilo
        swapperInStore();

        setTimeout(function () {
        alert('fondi insufficenti'); }, 500);
        
        localStorage.removeItem("invalidFlag");
    }
    else if($flag == 4){ //Caso Immagine profilo gia acquistata
        swapperInStore();
    
        setTimeout(function () {
        alert('Immagine profilo gia acquistata!'); }, 500);
        
        localStorage.removeItem("invalidFlag");
    }

}