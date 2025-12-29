function LikeGestione(idUtente, idCommento, idGioco, tipo) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("POST", "aggiornamentoLikeCommenti.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
    
    console.log("idUtente: " + idUtente + " idCommento: " + idCommento + " idGioco: " + idGioco + " tipo: " + tipo);




    xmlHttp.onreadystatechange = function() {    
   console.log(xmlHttp.status);
    console.log(xmlHttp.responseText);
    console.log(xmlHttp.responseXML);
        
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            var response = xmlHttp.responseText;
            console.log("Response: " + response);
         if(response === "like") {
                    
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
    xmlHttp.send("idUtente=" + idUtente + "&idCommento=" + idCommento + "&idGioco=" + idGioco + "&tipo=" + tipo);
}