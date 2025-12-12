function scaleTable() {
    var width = screen.width;
    var table = document.getElementById("GameTable");
    var gameRow = table.row[0];

    if(width<=700){
        gameRow.deleteCell(gameRow.cells.length - 1);
    }
    
}

window.addEventListener("resize", scaleTable);
scaleTable();