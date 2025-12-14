alert("1");
import { selettore } from "./selettore.js";
var cellCount = 1;

var popolari = new selettore("GameTable");
alert("2");
var contenitore = new Array();

contenitore[0]=popolari;

window.addEventListener("load", scaleTableOnLoad);
window.addEventListener("resize", scaleTableOnResize);


alert("3");
function scaleTableOnResize(){

    var width = window.innerWidth;
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

function scaleTableOnLoad(){
    var width = window.innerWidth;
    var table = document.getElementById("GameTable");
    var gameRow = table.rows[1];
    var string;
    //gameRow.cells[gameRow.cells.length-1].style.display = "none";
    //alert(" helo");
    for(let i=0; i<1; i++){
        string="GameTable"/*+i*/;
        alert("nome tabella: " +`${string}`);
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



function sliderTable(){
    
    var table = document.getElementById("GameTable");
    var gameRow = table.rows[1];
    for(let i=0;i < 1; i++ ){
    if(contenitore[i].tabella==tabellaPassata) tabellaselezionata=contenitore[i];
    }

    if((gameRow.cells.length - 1) > tabellaselezionata.end) {
        gameRow.cells[tabellaselezionata.start].style.display = "none";
        tabellaselezionata.start = tabellaselezionata.start + 1;
        gameRow.cells[tabellaselezionata.end].style.display = "table-cell";
        tabellaselezionata.end = tabellaselezionata.end + 1;
    }
    

}

function styleTableSettings(passaTable){
    var table = document.getElementById(passaTable);
    var gameRow = table.rows[1];
    alert("funzione richiamata" + gameRow.cells.length);
    for(let i=5; i<gameRow.cells.length; i++){
        alert("Nel for interazione "+i);
        gameRow.cells[i].style.display = "none";
    }

}