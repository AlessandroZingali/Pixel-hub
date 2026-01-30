function segnalaCommento(idCommento, idGioco){
    var check = confirm("Sei sicuro di voler segnalare questo commento come inappropriato?");
    if (check) {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                alert("Commento segnalato con successo.");
            }
        };
        httpRequest.open("POST", "APISegnalaCommento.php", true);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send("idCommento=" + (idCommento) + "&idGioco=" + (idGioco));
    }
    
}