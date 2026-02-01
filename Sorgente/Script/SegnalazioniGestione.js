function segnala(idOggetto, idGioco, type){
    console.log('entrato');
    if(type == 'com') var check = confirm("Sei sicuro di voler segnalare questo commento come inappropriato?");
    else if(type == 'rec') var check = confirm("Sei sicuro di voler segnalare questa recensione come inappropriata?");
    
    if (check) {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (type == 'com') alert("Commento segnalato con successo.");
                else if (type == 'rec') alert("Recensione segnalata con successo.");
            }
        };
        httpRequest.open("POST", "APISegnalazioni.php", true);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send("idOggetto=" + (idOggetto) + "&idGioco=" + (idGioco) + "&type=" + (type));
    }
    
}