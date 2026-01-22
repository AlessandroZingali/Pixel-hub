//Dichiarazione struttura dati del selettore e preparazione variabili Globali

/*All'interno del codice della home abbiamo usato uno script PHP per chiedere al server alcuni giochi
In base al gradimento da parte del publico o in base al genere. Abbiamo inserito poi questi giochi
in delle tabelle a singola riga, simulando delle enormi tuple. Con la seguente struttura dati abbiamo creato una mascherina,
un vero e proprio selettore che scorre questa tupla invisibile, rendendo di fatto i giochi visibili a scorrimento.*/

class selettore{ //Struttura dati del selettore
                             

    constructor(tabella){
        this.tabella=tabella; //Nome della tabella associata al selettore
        this.start=0; //Indice di inizio visibilità
        this.end=4; //Indice di fine visibilità
    }
}

/* In un certo senso abbiamo reso tutti quanti i giochi in Css come display:none per poi mostarli a coppie di minimo 1 massimo 5 celle
in base alla dimensione della finestra. Questo script gestisce sia la logica del selettore, sia la gestione del resize di qiest'ultimo
sia la logica dei bottoni per lo scorrimento */

var base = "GameTable"; //Base del nome delle tabelle giochi
var is_set_prefGen = 0; //Variabile di controllo per verificare la presenza nella Sessione del genere preferito
var is_set_id = 0;  //Variabile di controllo per verificare la presenza nella Sessione dell'id utente
var contenitore = []; //Array che conterrà tutte le tabelle e i rispettivi selettori
var numeroTabelle = 4; 

let push = 0; //Variabile di controllo per il push dell'array contenitore nel caso in cui ci sia un utente loggato

// console.log(sessionStorage.getItem('idUser'));
var is_set_id = parseInt(sessionStorage.getItem('idUser'));//Passaggio dell'id utente dalla sessione alla variabile di controllo, conversione in intero
//console.log(is_set_id); 
//console.log(sessionStorage.getItem('genPref'));
if(sessionStorage.getItem('genPref') != null && sessionStorage.getItem('genPref') != 'Nessuno') var is_set_prefGen = 1;//Controllo della presenza del genere preferito nella sessione
// console.log(is_set_prefGen);
var setAnswer = isNaN(is_set_id) ? 0 : 1; //Controllo della presenza dell'id utente nella sessione
var setPrefGen = (is_set_prefGen == 0) ? 0 : 1; //Controllo della presenza del genere preferito nella sessione

/*console.log("setAnswer: "+setAnswer);
console.log("setPrefGen: "+setPrefGen);
console.log('push '+push);*/

//Inizializzazione dell'array contenitore in base alla presenza o meno dell'id utente e del genere preferito
if((setAnswer == 0 && setPrefGen == 0) || (setAnswer == 1 && setPrefGen == 0)){
    
   push=1; 
//    console.log('push '+push);
//    console.log("Funzione base");
} 

//Se non è presente l'id utente e non è presente il genere preferito, significa che l'utente non è loggato, e le tabelle da mostrare sono 3
for(var i = 0 + push; i < numeroTabelle; i++){+
    console.log(base+i);
    contenitore.push(new selettore(base + i));
}

//Fine dichiarazione


//Aggiunta dell'event listener per il resize e il load
window.addEventListener("load", scaleTable);
window.addEventListener("resize", scaleTable);




//Resize automatico della tabella giochi in base alla larghezza della finestra
function scaleTable(){
    console.log("Funzione load");
   
    //Ciclo per ogni tabella nel contenitore
    for(var i = 0; i < contenitore.length; i++){
        var width = window.innerWidth; //Controllo dell larghezza della finestra

     var visibili = 5; //Inizialmente sono visibili 5 celle
     //Al resize della finestra vengono impostate le celle visibili in base alla larghezza
     //Da notare che la funzione è impostata per lavorare sia al resize nel load sia dinamicamente durante il ridimensionamento della finestra
     if (width <= 720) visibili = 1;

     else if (width <= 1000) visibili = 2;


       else if (width <= 1200) visibili = 3;
       else if (width <= 1500) visibili = 4;
       sessionStorage.setItem("NumCol", visibili); //Salvataggio del numero di celle visibili nella sessione per eventuali usi futuri
        //Recupero della tabella e della riga giochi
        var sel = contenitore[i];
        var table = document.getElementById(sel.tabella);
        var gameRow = table.rows[0]; //La riga è unica

        //Recupero dell'ultimo indice di inizio visibilità salvato nel local storage, in modo che se l'utente cambia pagina, sappiamo da dove ripartire
        var savedStart = parseInt(localStorage.getItem(sel.tabella));
        sel.start = isNaN(savedStart) ? 0 : savedStart;

        //Impostazione dell'indice di fine visibilità in base al numero di celle visibili, questo ci serve per riassestare il selettore in caso di resize
        sel.end = sel.start + visibili - 1;

        if(sel.end >= gameRow.cells.length){
            sel.end = gameRow.cells.length - 1; 

            sel.start = Math.max(0, sel.end - visibili + 1);
        }
   //Applicazione della visibilità in base ai nuovi indici calcolati
    applyVisibility(gameRow, sel);
    

    }
}


//Funzione per lo scorrimento della tabella giochi
function sliderTable(tabellaPassata, service){
    //Recupero del selettore associato alla tabella passata come parametro, usaimo il find per cercare nell'array contenitore
    const sel = contenitore.find(t => t.tabella === tabellaPassata); //Il comando restituira il selettore (il riferimento) in cui il nome della tabella è uguale a quello passato come parametro
    const table = document.getElementById(tabellaPassata); //Recupero della tabella
    const gameRow = table.rows[0]; //Recupero della riga giochi (unica riga)

    //In base al servizio richiesto (avanti o indietro) modifichiamo gli indici di visibilità del selettore
    if(service === "forward"){
        //Controllo che l'indice di fine visibilità non superi il numero di celle totali
        if(sel.end < gameRow.cells.length - 1){
            sel.start++;
            sel.end++;
            applyVisibility(gameRow, sel); //Applichiamo la visibilità in base ai nuovi indici
        }
      //  else boundControl(sel, gameRow);
    }
    else if(service === "back"){ //Se il servizio richiesto è indietro
        //Controllo che l'indice di inizio visibilità non sia minore di 0
        if(sel.start > 0){
            sel.start--;
            sel.end--;
            applyVisibility(gameRow, sel); //Applichiamo la visibilità in base ai nuovi indici
        }
       // else boundControl(sel, gameRow);
    }

    
    //Salviamo l'indice di inizio visibilità nel local storage per poterlo recuperare in caso di cambio pagina
    localStorage.setItem(tabellaPassata, sel.start); 

}


//Tramite questa funzione impostiamo lo stile iniziale della tabella giochi, nascondendo tutte le celle tranne le prime 5
function styleTableSettings(tabellaPassata){ 
    var table = document.getElementById(tabellaPassata);
    var gameRow = table.rows[0];
    //alert("funzione richiamata" + gameRow.cells.length);
    for(let i=5; i<gameRow.cells.length; i++){
        //alert("Nel for interazione "+i);
        gameRow.cells[i].style.display = "none";
    }

}

//Funzione per applicare la visibilità alle celle della tabella giochi in base agli indici del selettore
function applyVisibility(gameRow, sel){
    
    for(let i = 0; i < gameRow.cells.length; i++){
        gameRow.cells[i].style.display =
            (i >= sel.start && i <= sel.end) ? "table-cell" : "none"; //Mostriamo solo le celle comprese tra gli indici di inizio e fine visibilità
    }
    boundControl(sel, gameRow);
}

//Funzione per gestire l'opacità dei bottoni di scorrimento in base alla posizione del selettore
function boundControl(sel, gameRow){
    baseInput="scorri";//Base del nome degli input di scorrimento
    numInput = sel.tabella.charAt(9); //Recupero del numero della tabella dal nome della tabella (es. GameTable0 -> 0)
    console.log(numInput);
    console.log("start: "+sel.start+" end: "+sel.end);
    
   //Ogni nome del pulsante è composto da una base + il servizio + il numero della tabella (es. scorrimento(base) + avanti(servizio) + 0(numero tabella) -> scorriavanti0)
    if (sel.start == 0){ //Gestione del bottone indietro se siamo al bound sinistro (inizio tabella)
        input = document.querySelector('.GameSlider #'+baseInput+"ndietro"+numInput); //Usiamo il quarrySelector per selezionare l'input in base al suo id all'interno dell'html
        input.style.opacity="0.5";
    }
    else if(sel.start > 0){ //Gestione del bottone indietro se non siamo al bound sinistro
        input = document.querySelector('.GameSlider #'+baseInput+"ndietro"+numInput);
        input.style.opacity="1";
    }

    if(sel.end == gameRow.cells.length - 1){ //Gestione del bottone avanti se siamo al bound destro (fine tabella)
        input = document.querySelector('.GameSlider #'+baseInput+"avanti"+numInput);
        input.style.opacity="0.5";
    }
    else if(sel.end < gameRow.cells.length - 1){ //Gestione del bottone avanti se non siamo al bound destro
        input = document.querySelector('.GameSlider #'+baseInput+"avanti"+numInput);
        input.style.opacity="1";
    }
    
}
