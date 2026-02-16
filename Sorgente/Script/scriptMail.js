/* Questo script Js serve a gestire in modo dimanmico la logica dietro la riposta ai vari ticket degli utenti. */
function mailSender(idTicket, idUtente){
    console.log('Invio email di risposta per il ticket N°' + idTicket);
    // Creazione della prima richiesta XMLHttpRequest per inviare la richiesta al server, per inviare la mail di risposta all'utente.
    var xmlHttp = new XMLHttpRequest();
    var textArea = document.getElementById("RispostaTicketN"+idTicket);
    console.log('Testo della risposta: ' + textArea.value);
     xmlHttp.onreadystatechange = function() {    
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            console.log('Risposta ricevuta dal server: ' + xmlHttp.responseText);
            if(xmlHttp.responseText=='Email inviata!'){
                console.log('Email inviata!');
                var risposta = document.getElementById("ticketN"+idTicket);
                
                // Creazione della seconda richiesta XMLHttpRequest per inviare la richiesta al server, per eliminare il ticket a cui si è risposto.
                var xmlHttp2 = new XMLHttpRequest();
                xmlHttp2.onreadystatechange = function() {    
                    if (xmlHttp2.readyState === 4 && xmlHttp2.status === 200) {
                        risposta.style.display='none';
                    }
                    else alert('Errore invio Email');
                };
                xmlHttp2.open("POST", "ticketDelete.php", true);
                xmlHttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlHttp2.send("IDTicket=" + idTicket + "&IDUtente="+ idUtente);
            }
            else if(xmlHttp.getResponseHeader('Content-Type').includes('application/json')){
                console.log('Ricevuta risposta JSON di errore');
                var response = JSON.parse(xmlHttp.responseText);
                console.log('Errore nell\'invio dell\'email, stack trace:' + response);
            }
        }
    };
    xmlHttp.open("POST", "replyEmail.php", true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send("IDTicket=" + idTicket + "&IDUtente="+ idUtente + "&text=" + textArea.value);
    console.log('Richiesta di invio email inviata al server.');
}
