//Dichiarazione struttura dati del selettore

/*All'interno del codice della home abbiamo usato uno script PHP per chiedere al server alcuni giochi
In base al gradimento da parte del publico o in base al genere. Abbiamo inserito poi questi giochi
in delle tabelle a singola riga, simulando delle enormi tuple. Con la seguente struttura dati abbiamo creato una mascherina,
un vero e proprio selettore che scorre questa tupla invisibile, rendendo di fatto i giochi visibili a scorrimento.*/
class selettore{
    tabella;
    start;
    end;                                  

    constructor(tabella){
        this.tabella=tabella;
        this.start=0;
        this.end=4;
    }
}
//Fire Dichiarazione struttura dati del selettore

var cellCount = 1;

var popolari = new selettore("GameTable");
//alert("2");
var contenitore = new Array();

contenitore[0]=popolari;

window.addEventListener("load", scaleTableOnLoad);
window.addEventListener("resize", scaleTableOnResize);


//alert("3");

//Resize automatico della tabella giochi in base alla larghezza della finestra
function scaleTableOnResize(){

    var width = window.innerWidth;
    //var base = "GameTable";


    var table = document.getElementById("GameTable");
    var gameRow = table.rows[1];

    //alert("larghezza schermo " +`${width}`);

    if(width<= 1200 && cellCount == 1){
        gameRow.cells[gameRow.cells.length - cellCount].style.display = "none";
        cellCount++;

    }
     else if(width<= 1000 && cellCount == 2){
        gameRow.cells[gameRow.cells.length - cellCount].style.display = "none";
        cellCount++;
    } 
    else if(width<=1000 && cellCount == 1){
        for(let i=0; i<2; i++){
            gameRow.cells[gameRow.cells.length - cellCount].style.display = "none";
            cellCount++;
        }

    }
    else if (width>1000 && cellCount == 3){
        gameRow.cells[gameRow.cells.length - (cellCount -1)].style.display = "table-cell";
        cellCount--;
    }
    else if (width>1200 && cellCount == 2){
        gameRow.cells[gameRow.cells.length - (cellCount -1)].style.display = "table-cell";
        cellCount--;
    }
    else if (width>1200 && cellCount == 3){
        for(let i=0; i<2; i++){
            gameRow.cells[gameRow.cells.length - (cellCount -1)].style.display = "table-cell";
            cellCount--;
        }
    }
}

//Formattazione iniziale della tabella giochi in base alla larghezza della finestra
function scaleTableOnLoad(){
    var width = window.innerWidth;
    var table = document.getElementById("GameTable");
    var gameRow = table.rows[1];
    var string;

    for(let i=0; i<1; i++){ //Qui la tabella viene impostata, nascondendo tutti gli elementi al momento superflui
        string="GameTable"/*+i*/;
        //alert("nome tabella: " +`${string}`);
        styleTableSettings(string);
    }

    

    if(width<=1000 && cellCount == 1){
        for(let i=0; i<2; i++){
            gameRow.cells[gameRow.cells.length - cellCount].style.display = "none";
            cellCount++;
        }
    }
    else if(width<= 1200 && cellCount == 1){
        gameRow.cells[gameRow.cells.length - cellCount].style.display = "none";
        cellCount++;
    }

}



function sliderTable(tabellaPassata){ //Funzione che fa scorrere in avanti la tabella giochi
    var table = document.getElementById("GameTable");
    var gameRow = table.rows[1];
    for(let i=0;i < 1; i++ ){
    if(contenitore[i].tabella==tabellaPassata) tabellaselezionata=contenitore[i];
    }

    if((gameRow.cells.length - 1) > tabellaselezionata.end) {
        gameRow.cells[tabellaselezionata.start].style.display = "none";
        tabellaselezionata.start = tabellaselezionata.start + 1;
        tabellaselezionata.end = tabellaselezionata.end + 1;
        gameRow.cells[tabellaselezionata.end].style.display = "table-cell";
        
    }
    

}

function styleTableSettings(passaTable){ //Funzione che imposta lo stile iniziale della tabella giochi
    var table = document.getElementById(passaTable);
    var gameRow = table.rows[1];
    //alert("funzione richiamata" + gameRow.cells.length);
    for(let i=5; i<gameRow.cells.length; i++){
        //alert("Nel for interazione "+i);
        gameRow.cells[i].style.display = "none";
    }

}