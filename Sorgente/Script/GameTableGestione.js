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
 var base = "GameTable";

var popolari = new selettore("GameTable0");
//alert("2");
var contenitore = new Array();

contenitore[0]=popolari;

window.addEventListener("load", scaleTableOnLoad);
window.addEventListener("resize", scaleTableOnResize);


//alert("3");

//Resize automatico della tabella giochi in base alla larghezza della finestra
function scaleTableOnResize(){

    var width = window.innerWidth;
   

    for(let i=0; i<1; i++){

        var table = document.getElementById(base + i);
        var gameRow = table.rows[1];
        

        //alert("larghezza schermo " +`${width}`);

        if(width<= 1200 && cellCount == 1){
            //alert("condizione 1200 cellcount 1 " +`${width}` +"|| contenitore end:"+ `${contenitore[i].end}`+" || cellCount: " +`${cellCount}`);
            gameRow.cells[contenitore[i].end].style.display = "none";
            contenitore[i].end--;
            cellCount++;
            //alert("contenitore end dopo decremento (cond 1200): " +`${contenitore[i].end}`+" ||Cellcount++: " +`${cellCount}`);
            if(width<=1000 && cellCount == 2){

                gameRow.cells[contenitore[i].end].style.display = "none";
                contenitore[i].end--;
                
                cellCount++;
            

        }
            

        }
        else if(width<= 1000 && cellCount == 2){
            //alert("condizione 1000 cellcount 2 " +`${width}` +" || contenitore end: "+ `${contenitore[i].end}`+" || cellCount: " +`${cellCount}`);
            gameRow.cells[contenitore[i].end].style.display = "none";
            contenitore[i].end--;
            cellCount++;
           //alert("contenitore end dopo decremento (cond 1000): " +`${contenitore[i].end}`+" ||Cellcount++: " +`${cellCount}`);
            
        } 
        //else
        else if (width>1000 && cellCount == 3){
            if(gameRow.cells.length -1 == contenitore[i].end){
                gameRow.cells[contenitore[i].start - 1].style.display = "table-cell";
                gameRow.cells[contenitore[i].start -  2].style.display = "table-cell";
                cellCount-=2;
                contenitore[i].end-=2;
            } //controllare gli end e i -1/ -2 
            else{
                //alert("condizione maggiore 1000 cellcount 3 " +`${width}` +" || contenitore end: "+ `${contenitore[i].end}`+" || cellCount: " +`${cellCount}`); 
                gameRow.cells[contenitore[i].end + 1].style.display = "table-cell";
                gameRow.cells[contenitore[i].end +  2].style.display = "table-cell";
                cellCount-=2;
                contenitore[i].end+=2;
                //alert("contenitore end dopo incremento (cond >1000): " +`${contenitore[i].end}`+" ||Cellcount--: " +`${cellCount}`);
            }
            
        }
        else if (width>1200 && cellCount == 2){ 
            //alert("condizione maggiore 1200 cellcount 2 " +`${width}` +" || contenitore end: "+ `${contenitore[i].end}`+" || cellCount: " +`${cellCount}`);
            gameRow.cells[contenitore[i].end +1].style.display = "table-cell";
            contenitore[i].end++;
            cellCount--;
            //alert("contenitore end dopo incremento (cond >1200): " +`${contenitore[i].end}`+" ||Cellcount--: " +`${cellCount}`);
        }
        else if (width>1200 && cellCount == 3){
            for(let j=0; j<2; j++){
                gameRow.cells[contenitore[i].end + i + 1].style.display = "table-cell";
                contenitore[i].end++;
                cellCount--;

            }
        }
        
        }
}

//Formattazione iniziale della tabella giochi in base alla larghezza della finestra
function scaleTableOnLoad(){
    var width = window.innerWidth;
    

    for(let i=0; i<1; i++){ //Qui la tabella viene impostata, nascondendo tutti gli elementi al momento superflui
        //alert("nome tabella: " +`${string}`);
        styleTableSettings(base + i);
    }

    

    for(let i=0; i<1; i++){

        var table = document.getElementById(base + i);
        var gameRow = table.rows[1];
            
        if(width<= 1200 && cellCount == 1){
            
            gameRow.cells[contenitore[i].end].style.display = "none";
            contenitore[i].end--;
            cellCount++;

        }
        
        else if(width<=1000 && cellCount == 1){
            for(let j=0; j<2; j++){
                gameRow.cells[contenitore[i].end - i].style.display = "none";
                contenitore[i].end--;
                cellCount++;
            }

        }
       
    }

    


}



function sliderTableForward(tabellaPassata){ //Funzione che fa scorrere in avanti la tabella giochi
    var table = document.getElementById(tabellaPassata);
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

function sliderTableBack(tabellaPassata){ //Funzione che fa scorrere in indietro la tabella giochi
    var table = document.getElementById(tabellaPassata);
    var gameRow = table.rows[1];
    for(let i=0;i < 1; i++ ){
    if(contenitore[i].tabella==tabellaPassata) tabellaselezionata=contenitore[i];
    }

    if(tabellaselezionata.start!=0) {
       
        gameRow.cells[tabellaselezionata.end].style.display = "none";
        tabellaselezionata.end = tabellaselezionata.end - 1;

        tabellaselezionata.start = tabellaselezionata.start - 1;
        gameRow.cells[tabellaselezionata.start].style.display = "table-cell";        
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