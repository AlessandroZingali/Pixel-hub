
/*function scaleTable() {
    //alert("cazzo");
    var width = window.innerWidth;
    var table = document.getElementById("GameTable");
    var gameRow = table.row[0];
    alert("${width}");
    if(width<=700){
        gameRow.deleteCell(gameRow.cells.length - 1);
    }
    
}*/


window.addEventListener("resize", scaleTable);

function scaleTable(){
    var width = window.innerWidth;
    var table = document.getElementById("GameTable");
    var gameRow = table.rows[1];
    var cellCount = gameRow.cells.length;
    var i;

    alert("larghezza schermo "+`${width}`);

    if(width<= 1200 && cellCount > 4){
        //gameRow.deleteCell(gameRow.cells.length - 1);
        gameRow.cells[cellCount - 1].style.display = "none";
        cellCount=
        //alert("lunghezza celle "+`${gameRow.cells.length}`+" and Contenuto cella "+`${gameRow.cells[gameRow.cells.length - 1].innerText}`); 
        //alert("rimosso");
        //alert(`${gameRow.cells.length}`);

    }
        else if(width<= 1000 && cellCount > 3){
        //gameRow.deleteCell(gameRow.cells.length - 1);
        gameRow.cells[cellCount - 1].style.display = "none";
    
    } else 
}