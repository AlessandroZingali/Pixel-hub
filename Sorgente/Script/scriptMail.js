function mailSender(idTicket, idUtente){
    console.log('Invio email di risposta per il ticket N°' + idTicket);
    var xmlHttp = new XMLHttpRequest();
    var textArea = document.getElementById("RispostaTicketN"+idTicket);
    console.log('Testo della risposta: ' + textArea.value);
     xmlHttp.onreadystatechange = function() {    
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            console.log('Risposta ricevuta dal server: ' + xmlHttp.responseText);
            if(xmlHttp.responseText=='Email inviata!'){
                console.log('Email inviata!');
                var risposta = document.getElementById("ticketN"+idTicket);
                risposta.style.display='none';
                var xmlHttp2 = new XMLHttpRequest();
                xmlHttp2.onreadystatechange = function() {    
                    if (xmlHttp2.readyState === 4 && xmlHttp2.status === 200) {
                    }
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
