window.addEventListener("load",loadColorLikeDislikeCommenti);
window.addEventListener("load",loadColorLikeDislikeRecensioni);

function loadColorLikeDislikeCommenti() {

    var xmlHttp = new XMLHttpRequest();
    const params = new URLSearchParams(window.location.search);

    xmlHttp.onreadystatechange = function() {    
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
        console.log("Risposta ricevuta: " + xmlHttp.responseText);
        
        var tupleCom = JSON.parse(xmlHttp.responseText);
        console.log(tupleCom);
        for (var i = 0; i < tupleCom.length; i++) {
            var idCommento = tupleCom[i].idCommento;
            console.log("idCommento: " + idCommento);
            var flagLike = tupleCom[i].flagLike;
            var flagDislike = tupleCom[i].flagDislike;
            if (flagLike == 1) document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "green";
            else if (flagDislike == 1) document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "red";
        }
            
    };
}



    xmlHttp.open("POST", "coloreLoadLikeDislikeCommenti.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idGioco=" + params.get('idGioco'));
}

function userAlert(gradoUtente){
    if(gradoUtente == 2) alert("Oops, c'è stato un problema! Controlla se il tuo grado e' maggiore di 2");
    else if(gradoUtente == 1) alert ("Oops, c'è stato un problema! Controlla se il tuo grado e' maggiore di 1");
    else if(gradoUtente == 0) alert ("Oops, c'è stato un problema! Devi essere loggato!")
}

function LikeGestioneCommenti(idUtente, idCommento, idGioco, tipo) {

    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {    
    
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {

            var response = xmlHttp.responseText;

         if(response === "like" ) {
                    
                    var likeText = document.getElementById("likeButtonTextCom"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "green";
               
                }
                else if(response === "dislike") {
                
                    var dislikeText = document.getElementById("dislikeButtonTextCom"+idCommento);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "red";
                
                }
                else if(response === "cambioLikeDislike") {
                    var likeText = document.getElementById("likeButtonTextCom"+idCommento);
                    var dislikeText = document.getElementById("dislikeButtonTextCom"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "";
                    document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "red";
                }
                else if(response === "cambioDislikeLike") {
                    var likeText = document.getElementById("likeButtonTextCom"+idCommento);
                    var dislikeText = document.getElementById("dislikeButtonTextCom"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "green";
                    document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "";
                }
                else if (response === "rimozioneLike") {
                    var likeText = document.getElementById("likeButtonTextCom"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "";
                }
                else if (response === "rimozioneDislike") {
                    var dislikeText = document.getElementById("dislikeButtonTextCom"+idCommento);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "";
                }
                
   
    }
            
        };

    xmlHttp.open("POST", "aggiornamentoLikeCommenti.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idUtente=" + idUtente + "&idCommento=" + idCommento + "&idGioco=" + idGioco + "&tipo=" + tipo);
}

function loadColorLikeDislikeRecensioni() {

    var xmlHttp = new XMLHttpRequest();
    const params = new URLSearchParams(window.location.search);
    
    xmlHttp.onreadystatechange = function() {    
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {

        var tupleRec = JSON.parse(xmlHttp.responseText);
        console.log(tupleRec);
        for (var i = 0; i < tupleRec.length; i++) {
            var idRecensione = tupleRec[i].idRecensione;
            var flagLike = tupleRec[i].flagLike;
            var flagDislike = tupleRec[i].flagDislike;
            if (flagLike == 1) document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "green";
            else if (flagDislike == 1) document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "red";
        }
            
    };
}



    xmlHttp.open("POST", "coloreLoadLikeDislikeRecensioni.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idGioco=" + params.get('idGioco'));
}


function LikeGestioneRecensioni(idUtente, idRecensione, idGioco, tipo) {
    console.log("Entrato in LikeGestioneRecensioni");
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {    
        
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
        console.log("idRecensione: " + idRecensione);
        console.log("Risposta ricevuta: " + xmlHttp.responseText);
            var response = xmlHttp.responseText;

         if(response === "like" ) {
                    
                    var likeText = document.getElementById("likeButtonTextRec"+idRecensione);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "green";
               
                }
                else if(response === "dislike") {
                
                    var dislikeText = document.getElementById("dislikeButtonTextRec"+idRecensione);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "red";
                
                }
                else if(response === "cambioLikeDislike") {
                    var likeText = document.getElementById("likeButtonTextRec"+idRecensione);
                    var dislikeText = document.getElementById("dislikeButtonTextRec"+idRecensione);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "";
                    document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "red";
                }
                else if(response === "cambioDislikeLike") {
                    var likeText = document.getElementById("likeButtonTextRec"+idRecensione);
                    var dislikeText = document.getElementById("dislikeButtonTextRec"+idRecensione);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "green";
                    document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "";
                }
                else if (response === "rimozioneLike") {
                    var likeText = document.getElementById("likeButtonTextRec"+idRecensione);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "";
                }
                else if (response === "rimozioneDislike") {
                    var dislikeText = document.getElementById("dislikeButtonTextRec"+idRecensione);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "";
                }
                
   
    }
            
        };

    xmlHttp.open("POST", "aggiornamentoLikeRecensioni.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idUtente=" + idUtente + "&idRecensione=" + idRecensione + "&idGioco=" + idGioco + "&tipo=" + tipo);
}