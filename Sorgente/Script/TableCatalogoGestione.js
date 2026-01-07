var numTotRows=0;
var numTotGames=0;

function loadMoreGames(){
    lastGameId = parseInt(document.getElementById('ultimoGioco').innerHTML);
    var table = document.getElementById('GameTable')
    numRow = table.rows.length;
    var newRows = 3;
    var titolo;
    var immagine;
    var prezzo;
    var idGioco;
    if (window.innerWidth<=1500){
        numGame = 12;
        var counter = 4;
        var bound = 4;
    }
    else{
        numGame=15;
        var counter = 5;
        var bound = 5;
    }
    
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {    
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            var tupleGiochi = JSON.parse(xmlHttp.responseText);
            var textLastId=document.getElementById('ultimoGioco');
            textLastId.remove();
            if(tupleGiochi.length<numGame) limite=tupleGiochi.length;
              
            for(i=0; i<newRows; i++){
                table.innerHTML+="<tr>";
                for(j=cache; j<bound; j++){
                    titolo = tupleGiochi[j].titoloGioco;
                    immagine = tupleGiochi[j].immagineGioco;
                    prezzo = tupleGiochi[j].prezzoGioco;
                    idGioco = tupleGiochi[j].idGioco;
                    
                    table.innerHTML+="<td> <div class= \" GameCard \">";
                    if(j == limite) table.innerHTML+="<p id=\"ultimoGioco\"style=\"display: none\">"+idGioco+"</p>";
                    table.innerHTML+= "<img src=\""+immagine+"\" alt=\"GameImage\" title=\""+titolo+"\" class=\"productimage\" onclick=\"location.href='Gamepage.php?titoloGioco="+titolo+"&idGioco="+idGioco+"'\" > <div class=\"prezzo\"><p>"+prezzo+" €</p></div>";
                    table.innerHTML+="</div> </td>";
                }
                cache = j;
                table.innerHTML+="</tr>";
                if(bound == limite) break;
                if(counter+cache > limite) bound = limite;
                else bound = counter + cache;
            }
            
        }
    }
    
    xmlHttp.open("POST", "coloreLoadLikeDislikeCommenti.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("lastId="+(lastGameId+numGame)+"&numGame="+numGame);


}
function resizePage(){
    var table = document.getElementById('GameTable');
    
}
