

function swapperInSettings(){ //Funzione per lo scambio di card tra ticket  e funzioni avanzate
    var cardticket = document.getElementById("card1"); //Prendo la card del ticket

    var cardModificaGioco = document.getElementById("card2"); //Prendo la card delle impostazioni tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardticket.classList.toggle("hideCard"); 
    cardModificaGioco.classList.toggle("hideCard");
}

function swapperInModificaGioco(){ 
    var cardticket = document.getElementById("card1"); //Prendo la card del profilo tramite il document

    var cardModificaGioco = document.getElementById("card3"); //Prendo la card dello store tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardticket.classList.toggle("hideCard");
    cardModificaGioco.classList.toggle("hideCard");
}
