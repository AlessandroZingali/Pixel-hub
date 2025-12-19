//Dichiarazione struttura dati del selettore e preparazione variabili Globali

/*All'interno del codice della home abbiamo usato uno script PHP per chiedere al server alcuni giochi
In base al gradimento da parte del publico o in base al genere. Abbiamo inserito poi questi giochi
in delle tabelle a singola riga, simulando delle enormi tuple. Con la seguente struttura dati abbiamo creato una mascherina,
un vero e proprio selettore che scorre questa tupla invisibile, rendendo di fatto i giochi visibili a scorrimento.*/
class selettore{
                             

    constructor(tabella){
        this.tabella=tabella;
        this.start=0;
        this.end=4;
    }
}

var base = "GameTable"; 
var is_set_id = 0; 
var contenitore = []; 
var numeroTabelle = 4; 

let push = 0;

var is_set_id = parseInt(sessionStorage.getItem('userId'));
var is_set_prefGen = parseInt(sessionStorage.getItem('generePreferito'));
var setAnswer = isNaN(is_set_id) ? 0 : 1;
var setPrefGen = isNaN(is_set_prefGen) ? 0 : 1;

if(setAnswer == 0 && setPrefGen == 0) push=1;

for(let i = 0 + push; i < numeroTabelle; i++){
    contenitore.push(new selettore(base + i));
}

//Fine dichiarazione


//Aggiunta dell'event listener per il resize e il load
window.addEventListener("load", scaleTable);
window.addEventListener("resize", scaleTable);





//Resize automatico della tabella giochi in base alla larghezza della finestra
function scaleTable(){
    const width = window.innerWidth;

    let visibili = 5;

    if (width <= 1000) visibili = 2;
    else if (width <= 1200) visibili = 3;
    else if (width <= 1500) visibili = 4;

    for(let i = 0; i < contenitore.length; i++){
        const sel = contenitore[i];
        const table = document.getElementById(sel.tabella);
        const gameRow = table.rows[1];
        
        const savedStart = parseInt(localStorage.getItem(sel.tabella));
        sel.start = isNaN(savedStart) ? 0 : savedStart;

        sel.end = sel.start + visibili - 1;

        if(sel.end >= gameRow.cells.length){
            sel.end = gameRow.cells.length - 1; 

            sel.start = Math.max(0, sel.end - visibili + 1);
        }

        applyVisibility(gameRow, sel);
    }
}


    
function sliderTable(tabellaPassata, service){
    const sel = contenitore.find(t => t.tabella === tabellaPassata);
    const table = document.getElementById(tabellaPassata);
    const gameRow = table.rows[1];

    if(service === "forward"){

        if(sel.end < gameRow.cells.length - 1){
            sel.start++;
            sel.end++;
            applyVisibility(gameRow, sel);
        }
    }
    else if(service === "back"){
        if(sel.start > 0){
            sel.start--;
            sel.end--;
            applyVisibility(gameRow, sel);
        }
    }

    

    localStorage.setItem(tabellaPassata, sel.start);

}



function styleTableSettings(tabellaPassata){ //Funzione che imposta lo stile iniziale della tabella giochi
    var table = document.getElementById(tabellaPassata);
    var gameRow = table.rows[1];
    //alert("funzione richiamata" + gameRow.cells.length);
    for(let i=5; i<gameRow.cells.length; i++){
        //alert("Nel for interazione "+i);
        gameRow.cells[i].style.display = "none";
    }

}

function applyVisibility(gameRow, sel){
    for(let i = 0; i < gameRow.cells.length; i++){
        gameRow.cells[i].style.display =
            (i >= sel.start && i <= sel.end) ? "table-cell" : "none";
    }
}

