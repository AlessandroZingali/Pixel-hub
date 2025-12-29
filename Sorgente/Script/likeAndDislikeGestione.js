window.addEventListener("load",loadColorLikeDislike);

function loadColorLikeDislike() {
    var xmlHttp = new XMLHttpRequest();
    const params = new URLSearchParams(window.location.search);
    xmlHttp.onreadystatechange = function() {    
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
        if (xmlHttp.responseText !== "") {
            var tuple = JSON.parse(xmlHttp.responseText);
            console.log(tuple);
            for (var i = 0; i < tuple.length; i++) {
                var idCommento = tuple[i].idCommento;
                console.log("idCommento: " + idCommento);
                var flagLike = tuple[i].flagLike;
                var flagDislike = tuple[i].flagDislike;
                if (flagLike == 1) document.getElementById("likeButton"+idCommento).style.backgroundColor = "green";
                else if (flagDislike == 1) document.getElementById("dislikeButton"+idCommento).style.backgroundColor = "red";
            }
        }    
    };
}



    xmlHttp.open("POST", "coloreLoadLikeDislikeCommenti.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idGioco=" + params.get('idGioco'));
}


function LikeGestione(idUtente, idCommento, idGioco, tipo) {
    var xmlHttp = new XMLHttpRequest();
    console.log('Entry: '+tipo);


    xmlHttp.onreadystatechange = function() {    
   console.log(xmlHttp.status);
    console.log(xmlHttp.responseText);
    console.log(xmlHttp.responseXML);
        
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            var response = xmlHttp.responseText;
            console.log('In status: '+ tipo);
            console.log("Response: " + response);
         if(response === "like" ) {
                    
                    var likeText = document.getElementById("likeButtonText"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    document.getElementById("likeButton"+idCommento).style.backgroundColor = "green";
               
                }
                else if(response === "dislike") {
                
                    var dislikeText = document.getElementById("dislikeButtonText"+idCommento);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("dislikeButton"+idCommento).style.backgroundColor = "red";
                
                }
                else if(response === "cambioLikeDislike") {
                    var likeText = document.getElementById("likeButtonText"+idCommento);
                    var dislikeText = document.getElementById("dislikeButtonText"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("likeButton"+idCommento).style.backgroundColor = "";
                    document.getElementById("dislikeButton"+idCommento).style.backgroundColor = "red";
                }
                else if(response === "cambioDislikeLike") {
                    var likeText = document.getElementById("likeButtonText"+idCommento);
                    var dislikeText = document.getElementById("dislikeButtonText"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("likeButton"+idCommento).style.backgroundColor = "green";
                    document.getElementById("dislikeButton"+idCommento).style.backgroundColor = "";
                }
                else if (response === "rimozioneLike") {
                    var likeText = document.getElementById("likeButtonText"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    document.getElementById("likeButton"+idCommento).style.backgroundColor = "";
                }
                else if (response === "rimozioneDislike") {
                    var dislikeText = document.getElementById("dislikeButtonText"+idCommento);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("dislikeButton"+idCommento).style.backgroundColor = "";
                }
                
   
    }
            
        };
    xmlHttp.open("POST", "aggiornamentoLikeCommenti.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idUtente=" + idUtente + "&idCommento=" + idCommento + "&idGioco=" + idGioco + "&tipo=" + tipo);
}