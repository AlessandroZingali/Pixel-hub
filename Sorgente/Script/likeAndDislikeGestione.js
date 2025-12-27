

function Likegestione(idUtente, idCommento, idGioco) {
    var xmlHttp = new XMLHttpRequest();
    var found = false;
    xmlHttp.open("GET", "LikeCommenti.xml", true);
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
            var response = xmlHttp.responseXML;
            var radice = response.getElementsByTagName("Associazione_Like_Commenti");

            for (var i = 0; i < radice.length; i++) {
                var utente = radice[i].getElementsByTagName("idUtente")[0].textContent;
                var commento = radice[i].getElementsByTagName("idCommento")[0].textContent;
                if (utente === idUtente && commento === idCommento) found = true;
            }
        }
            
        }
    xmlHttp.send(null);

    if (!found) {
        var xmlHttp2 = new XMLHttpRequest();
        xmlHttp2.open("GET", "Commenti.xml", true);
        xmlHttp2.onreadystatechange = function() {
            if (xmlHttp2.readyState === 4 && xmlHttp2.status === 200) {
                var response2 = xmlHttp2.responseXML;
                var radice2 = response2.getElementsByTagName("listaCommenti");
                for (var j = 0; j < radice2.length; j++) {
                    if(radice2[j].getAttribute("id_gioco") === idGioco) {
                        var gioco = radice2[j];
                        for (var k = 0; k < gioco.length; k++) {
                            var id_commento = gioco[k].getAttribute("id_commento");
                            if (id_commento === idCommento) {
                                var likeCount = parseInt(gioco[k].getAttribute("like"));
                                likeCount += 1;
                                gioco[k].getAttribute("like") = likeCount.toString();
                                
                            }
                        }
                    }
                }
            }
        };
        xmlHttp2.send(null);
    }
    else {
        alert("Hai già messo like o dislike a questo commento!");
    }
};