

function LikeGestione(idUtente, idCommento, idGioco, tipo) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", "LikeCommenti.xml", true);

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            var response = xmlHttp.responseXML;
            var radice = response.getElementsByTagName("Associazione_Like_Commenti");
             var found = false;
            var flagLike = "";
            var flagDislike = "";
            for (var i = 0; i < radice.length; i++) {
                var utente = radice[i].getElementsByTagName("Id_utente")[0].textContent;
                var commento = radice[i].getElementsByTagName("Id_Commento")[0].textContent;
                var gioco = radice[i].getElementsByTagName("Id_Gioco")[0].textContent;
                if (parseInt(utente) === idUtente && parseInt(commento) === idCommento && parseInt(gioco) == idGioco){
                    found = true;
                    var flagLike = radice[i].getAttribute("flagLike");
                    var flagDislike = radice[i].getAttribute("flagDislike");

                    alert("Hai già messo like o dislike a questo commento!");
                } 
            }
        }
            
       


    if (!found) {
        // non ha mai messo like o dislike e lo vuole aggiungere
        var xmlHttp2 = new XMLHttpRequest();
        xmlHttp2.open("POST", "aggiornamentoLikeCommenti.php", true);
        xmlHttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttp2.send("idUtente=" + idUtente + "&idCommento=" + idCommento + "&idGioco=" + idGioco + "&tipo="+ tipo + "&route=0");
        xmlHttp2.onreadystatechange = function() {
            if (xmlHttp2.readyState === 4 && xmlHttp2.status === 200) {
                if(tipo === "like") {
                    
                    let likeText = document.getElementById("likeButtonText"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    document.getElementById("likeButton"+idCommento).style.backgroundColor = "green";
                }
                else if(tipo === "dislike") {
                    let dislikeText = document.getElementById("dislikeButtonText"+idCommento);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("dislikeButton"+idCommento).style.backgroundColor = "red";
                }

            }
        }

    }
    else if (found && ((tipo === "like" && flagLike === "0") || (tipo === "dislike" && flagDislike === "0"))) {
        // ha già messo like e vuole cambiare in dislike 
        if(tipo === "like") {
            var xmlHttp3 = new XMLHttpRequest();
        xmlHttp3.open("POST", "aggiornamentoLikeCommenti.php", true);
        xmlHttp3.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttp3.send("idUtente=" + idUtente + "&idCommento=" + idCommento + "&idGioco=" + idGioco + "&tipo="+ tipo+ "&route=1");
        xmlHttp3.onreadystatechange = function() {
            if (xmlHttp3.readyState === 4 && xmlHttp3.status === 200) {
                let likeText = document.getElementById("likeButtonText"+idCommento);
                likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                document.getElementById("likeButton"+idCommento).style.backgroundColor = "";
                let dislikeText = document.getElementById("dislikeButtonText"+idCommento);
                dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                document.getElementById("dislikeButton"+idCommento).style.backgroundColor = "red";

                alert("Cambio effettuato con successo! like -> dislike");
            }
        }
            
    }
    else if(tipo === "dislike") {
        // ha già messo dislike e vuole cambiare in like
        var xmlHttp4 = new XMLHttpRequest();
        xmlHttp4.open("POST", "aggiornamentoLikeCommenti.php", true);
        xmlHttp4.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttp4.send("idUtente=" + idUtente + "&idCommento=" + idCommento + "&idGioco=" + idGioco + "&tipo="+ tipo + "&route=2");
        xmlHttp4.onreadystatechange = function() {
            if (xmlHttp4.readyState === 4 && xmlHttp4.status === 200) {

                let dislikeText = document.getElementById("dislikeButtonText"+idCommento);
                dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;    
                document.getElementById("dislikeButton"+idCommento).style.backgroundColor = "";
                let likeText = document.getElementById("likeButtonText"+idCommento);
                alert("Cambio effettuato con successo! dislike -> like");
            }
        }
    }
    else {
        alert("Hai già messo like o dislike a questo commento!");
    } 
}
}

    xmlHttp.send(null);
}