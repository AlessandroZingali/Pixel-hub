var cellCount = 1;
var button = document.getElementById("vedialtrobutton");
window.addEventListener("load", scaleTableOnLoad);
window.addEventListener("resize", scaleTableOnResize);
button.addEventListener("onclick", sliderTable);

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
    
    //gameRow.cells[gameRow.cells.length-1].style.display = "none";

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
    alert("enter");

    gameRow.cells[0].display.style = "none";
}

