window.addEventListener("load",loadColorLikeDislikeCommenti); //Gestione evento inziale per colorare i bottoni like e dislike commenti
window.addEventListener("load",loadColorLikeDislikeRecensioni); //Gestione evento inziale per colorare i bottoni like e dislike recensioni

//Funzione per caricare i colori dei bottoni like e dislike dei commenti in base ai dati salvati nel file XML LikeRecensioni.xml
//Richiama uno script PHP tramite una chiamata AJAX, che restituisce i dati in formato JSON
function loadColorLikeDislikeCommenti() {
//Creazione oggetto XMLHttpRequest per la chiamata AJAX
    var xmlHttp = new XMLHttpRequest();
    const params = new URLSearchParams(window.location.search); //Creazione oggetto per prendere l'id del gioco dalla URL

    xmlHttp.onreadystatechange = function() {    
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) { //Controllo dello stato della richiesta
        console.log("Risposta ricevuta: " + xmlHttp.responseText);
        
        //Se la risposta non è vuota, viene eseguito il parse del JSON e colorazione dei bottoni
        if(xmlHttp.responseText != ""){
            //Parsing della risposta JSON
            var tupleCom = JSON.parse(xmlHttp.responseText);
            console.log(tupleCom);
            //Ciclo per ogni tupla ricevuta, estrazione dei dati e colorazione dei bottoni
            for (var i = 0; i < tupleCom.length; i++) {
                var idCommento = tupleCom[i].idCommento;
                console.log("idCommento: " + idCommento);
                var flagLike = tupleCom[i].flagLike;
                var flagDislike = tupleCom[i].flagDislike;

                //Colorazione dei bottoni in base ai flag, ogni bottone ha in comune il nome likeButtomCom o dislikeButtonCom 
                //seguito dall'id del commento che lo identifica
                if (flagLike == 1) document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "green";
                else if (flagDislike == 1) document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "red";
            }
        }
        
            
    };
}
    //Invio della richiesta POST allo script PHP con l'id del gioco come parametro
    xmlHttp.open("POST", "coloreLoadLikeDislikeCommenti.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idGioco=" + params.get('idGioco'));
}

//Funzione per mostrare un alert in base al grado dell'utente, usato per gestire permessi di inserimento commenti o recensioni e relativi like e dislike
function userAlert(gradoUtente){
    if(gradoUtente == 2) alert("Oops, c'è stato un problema! Controlla se il tuo grado e' maggiore di 2");
    else if(gradoUtente == 1) alert ("Oops, c'è stato un problema! Controlla se il tuo grado e' maggiore di 1");
    else if(gradoUtente == 0) alert ("Oops, c'è stato un problema! Devi essere loggato!")
}

//Funzione per gestire l'inserimento e il deinserimento di un i like o dislike dei commenti, 
//verra effettuata la chiamata AJAX ad uno script PHP che aggiornera il file XML LikeCommenti.xmle Commenti.xml.
//In base alla risposta dello script PHP, verra aggiornato il contatore dei like e dislike e il colore dei bottoni

function LikeGestioneCommenti(idUtente, idCommento, idGioco, tipo) {

    var xmlHttp = new XMLHttpRequest(); //Creazione oggetto XMLHttpRequest per la chiamata AJAX

    xmlHttp.onreadystatechange = function() {    //Gestione della risposta della chiamata AJAX
    
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) { //Controllo dello stato della richiesta

            var response = xmlHttp.responseText; //Salvataggio della risposta in una variabile

         if(response === "like" ) { //Gestione della risposta "like"
                    //Aggiornamento del contatore dei like e cambio colore del bottone da grigio a verde
                    var likeText = document.getElementById("likeButtonTextCom"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "green";
               
                }
                else if(response === "dislike") { //Gestione della risposta "dislike"
                    //Aggiornamento del contatore dei dislike e cambio colore del bottone da grigio a rosso
                
                    var dislikeText = document.getElementById("dislikeButtonTextCom"+idCommento);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "red";
                
                }
                else if(response === "cambioLikeDislike") { //Gestione della risposta "cambioLikeDislike"
                    //Aggiornamento dei contatori dei like e dislike e cambio colore dei bottoni da verde a rosso
                    var likeText = document.getElementById("likeButtonTextCom"+idCommento);
                    var dislikeText = document.getElementById("dislikeButtonTextCom"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "";
                    document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "red";
                }
                else if(response === "cambioDislikeLike") { //Gestione della risposta "cambioDislikeLike"
                    //Aggiornamento dei contatori dei like e dislike e cambio colore dei bottoni da rosso a verde
                    var likeText = document.getElementById("likeButtonTextCom"+idCommento);
                    var dislikeText = document.getElementById("dislikeButtonTextCom"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "green";
                    document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "";
                }
                else if (response === "rimozioneLike") { //Gestione della risposta "rimozioneLike"
                    //Aggiornamento del contatore dei like e cambio colore del bottone da verde a grigio
                    var likeText = document.getElementById("likeButtonTextCom"+idCommento);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    document.getElementById("likeButtonCom"+idCommento).style.backgroundColor = "";
                }
                else if (response === "rimozioneDislike") { //Gestione della risposta "rimozioneDislike"
                    //Aggiornamento del contatore dei dislike e cambio colore del bottone da rosso a grigio
                    var dislikeText = document.getElementById("dislikeButtonTextCom"+idCommento);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("dislikeButtonCom"+idCommento).style.backgroundColor = "";
                }
                
   
    }
            
        };
    //Invio della richiesta POST allo script PHP con i parametri necessari
    xmlHttp.open("POST", "aggiornamentoLikeCommenti.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idUtente=" + idUtente + "&idCommento=" + idCommento + "&idGioco=" + idGioco + "&tipo=" + tipo);
}

//Funzione per caricare i colori dei bottoni like e dislike delle recensioni in base ai dati salvati nel file XML LikeRecensioni.xml
//Richiama uno script PHP tramite una chiamata AJAX, che restituisce i dati in formato JSON
function loadColorLikeDislikeRecensioni() {

    var xmlHttp = new XMLHttpRequest(); //Creazione oggetto XMLHttpRequest per la chiamata AJAX
    const params = new URLSearchParams(window.location.search); //Creazione oggetto per prendere l'id del gioco dalla URL
    
    xmlHttp.onreadystatechange = function() {    
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) { //Controllo dello stato della richiesta
         console.log("Risposta ricevuta: " + xmlHttp.responseText);
        
       if (xmlHttp.responseText != ""){ //Se la risposta non è vuota, viene eseguito il parse del JSON e colorazione dei bottoni
            var tupleRec = JSON.parse(xmlHttp.responseText);
            console.log(tupleRec);
            //Ciclo per ogni tupla ricevuta, estrazione dei dati e colorazione dei bottoni
            for (var i = 0; i < tupleRec.length; i++) {
                var idRecensione = tupleRec[i].idRecensione;
                console.log("idRecensione: " + idRecensione);
                var flagLike = tupleRec[i].flagLike;
                var flagDislike = tupleRec[i].flagDislike;
                //Colorazione dei bottoni in base ai flag, ogni bottone ha in comune il nome likeButtomRec o dislikeButtonRec 
                //seguito dall'id della recensione che lo identifica
                if (flagLike == 1) document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "green";
                else if (flagDislike == 1) document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "red";
            }
       }
        
            
    };
}


    //Invio della richiesta POST allo script PHP con l'id del gioco come parametro
    xmlHttp.open("POST", "coloreLoadLikeDislikeRecensioni.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idGioco=" + params.get('idGioco'));
}

//Funzione per gestire l'inserimento e il deinserimento di un i like o dislike delle recensioni, 
//verra effettuata la chiamata AJAX ad uno script PHP che aggiornera il file XML LikeRecensioni.xml e Recensioni.xml.
//In base alla risposta dello script PHP, verra aggiornato il contatore dei like e dislike e il colore dei bottoni
function LikeGestioneRecensioni(idUtente, idRecensione, idGioco, tipo) {
    console.log("Entrato in LikeGestioneRecensioni");
    var xmlHttp = new XMLHttpRequest(); //Creazione oggetto XMLHttpRequest per la chiamata AJAX

    xmlHttp.onreadystatechange = function() {     //Gestione della risposta della chiamata AJAX
    if (xmlHttp.readyState === 4 && xmlHttp.status === 200) { //Controllo dello stato della richiesta
        console.log("idRecensione: " + idRecensione);
        console.log("Risposta ricevuta: " + xmlHttp.responseText);
        var response = xmlHttp.responseText; //Salvataggio della risposta in una variabile

         if(response === "like" ) { //Gestione della risposta "like"
                    //Aggiornamento del contatore dei like e cambio colore del bottone da grigio a verde
                    
                    var likeText = document.getElementById("likeButtonTextRec"+idRecensione);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "green";
               
                }
                else if(response === "dislike") { //Gestione della risposta "dislike"
                    //Aggiornamento del contatore dei dislike e cambio colore del bottone da grigio a rosso
                
                    var dislikeText = document.getElementById("dislikeButtonTextRec"+idRecensione);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "red";
                
                }
                else if(response === "cambioLikeDislike") { //Gestione della risposta "cambioLikeDislike"
                    //Aggiornamento dei contatori dei like e dislike e cambio colore dei bottoni da verde a rosso
                    var likeText = document.getElementById("likeButtonTextRec"+idRecensione);
                    var dislikeText = document.getElementById("dislikeButtonTextRec"+idRecensione);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) + 1;
                    document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "";
                    document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "red";
                }
                else if(response === "cambioDislikeLike") { //Gestione della risposta "cambioDislikeLike"
                    //Aggiornamento dei contatori dei like e dislike e cambio colore dei bottoni da rosso a verde
                    var likeText = document.getElementById("likeButtonTextRec"+idRecensione);
                    var dislikeText = document.getElementById("dislikeButtonTextRec"+idRecensione);
                    likeText.innerHTML = parseInt(likeText.innerHTML) + 1;
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "green";
                    document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "";
                }
                else if (response === "rimozioneLike") { //Gestione della risposta "rimozioneLike"
                    //Aggiornamento del contatore dei like e cambio colore del bottone da verde a grigio
                    var likeText = document.getElementById("likeButtonTextRec"+idRecensione);
                    likeText.innerHTML = parseInt(likeText.innerHTML) - 1;
                    document.getElementById("likeButtonRec"+idRecensione).style.backgroundColor = "";
                }
                else if (response === "rimozioneDislike") { //Gestione della risposta "rimozioneDislike"
                    //Aggiornamento del contatore dei dislike e cambio colore del bottone da rosso a grigio
                    var dislikeText = document.getElementById("dislikeButtonTextRec"+idRecensione);
                    dislikeText.innerHTML = parseInt(dislikeText.innerHTML) - 1;
                    document.getElementById("dislikeButtonRec"+idRecensione).style.backgroundColor = "";
                }
                
   
    }
            
        };
    //Invio della richiesta POST allo script PHP con i parametri necessari
    xmlHttp.open("POST", "aggiornamentoLikeRecensioni.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("idUtente=" + idUtente + "&idRecensione=" + idRecensione + "&idGioco=" + idGioco + "&tipo=" + tipo);
}