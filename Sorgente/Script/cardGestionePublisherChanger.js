function swapperInAggiungiGioco(){ //Funzione per lo scambio di card menu iniziale e la card aggiungi gioco
    var cardPrincipale = document.getElementById("card0"); //Prendo la card del ticket

    var cardAggiungi = document.getElementById("card1"); //Prendo la card delle impostazioni tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardPrincipale.classList.toggle("hideCard"); 
    cardAggiungi.classList.toggle("hideCard");
}

function swapperInCercaGioco(){ //Funzione per lo scambio di card menu iniziale e la card ricerca giochi aggiunti
    var cardPrincipale = document.getElementById("card0"); //Prendo la card del ticket

    var cardRicerca = document.getElementById("card2"); //Prendo la card delle impostazioni tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardPrincipale.classList.toggle("hideCard"); 
    cardRicerca.classList.toggle("hideCard");
}


function swapperInModificaGioco(){ //Funzione per lo scambio di card menu iniziale e la card aggiungi gioco
    var cardPrincipale = document.getElementById("card0"); //Prendo la card del ticket

    var cardTicket = document.getElementById("card3"); //Prendo la card delle impostazioni tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardPrincipale.classList.toggle("hideCard"); 
    cardTicket.classList.toggle("hideCard");
}

function swapperInGestioneSconti(){
    var cardPrincipale =document.getElementById("card0");
    var cardSospensione = document.getElementById("card4");

    cardPrincipale.classList.toggle("hideCard");
    cardSospensione.classList.toggle("hideCard");
    
}

function swapperInGestioneSconti(){
    var cardPrincipale =document.getElementById("card0");
    var cardSospensione = document.getElementById("card4");

    cardPrincipale.classList.toggle("hideCard");
    cardSospensione.classList.toggle("hideCard");
    
}

function swapperInAggiornamentoSconti(){
    var cardPrincipale =document.getElementById("card0");
    var cardSospensione = document.getElementById("card5");

    cardPrincipale.classList.toggle("hideCard");
    cardSospensione.classList.toggle("hideCard");
    
}

function modificaGiocoPreset(){
    console.log("activeStatus: " + sessionStorage.getItem("activeChange"));
    if(sessionStorage.getItem("activeChange") == "ricercaGioco"){
        var cards = document.querySelectorAll("div[id^='card']");
        console.log(cards);

        var cardsArray = Array.from(cards).filter(div => /^card\d+$/.test(div.id)).sort((a, b) => {
                // Estrai i numeri dagli id e ordina
                var numA = parseInt(a.id.replace("card", ""));
                var numB = parseInt(b.id.replace("card", ""));
                return numA - numB;
            });
        console.log(cardsArray);

        cardsArray.forEach(card => {
            if(!(card.classList.contains("hideCard")) && card.id !== "card3"){
                card.classList.add("hideCard");
            }
            else if(card.id === "card3" && card.classList.contains("hideCard")){
                card.classList.remove("hideCard");
            }
        }); 
        console.log("activeStatus: " + sessionStorage.getItem("activeChange"));
        sessionStorage.setItem("activeChange", "vuoto");
    }
    else if(sessionStorage.getItem("activeChange") == "gestioneSconti"){
        var cards = document.querySelectorAll("div[id^='card']");
        console.log(cards);

        var cardsArray = Array.from(cards).filter(div => /^card\d+$/.test(div.id)).sort((a, b) => {
                // Estrai i numeri dagli id e ordina
                var numA = parseInt(a.id.replace("card", ""));
                var numB = parseInt(b.id.replace("card", ""));
                return numA - numB;
            });
        console.log(cardsArray);

        cardsArray.forEach(card => {
            if(!(card.classList.contains("hideCard")) && card.id !== "card5"){
                card.classList.add("hideCard");
            }
            else if(card.id === "card5" && card.classList.contains("hideCard")){
                card.classList.remove("hideCard");
            }
            
        }); 
        console.log("activeStatus: " + sessionStorage.getItem("activeChange"));
        sessionStorage.setItem("activeChange", "vuoto");

    }
    else if(sessionStorage.getItem("activeChange") == "noBoundSconti"){
        var cards = document.querySelectorAll("div[id^='card']");
        console.log(cards);

        var cardsArray = Array.from(cards).filter(div => /^card\d+$/.test(div.id)).sort((a, b) => {
                // Estrai i numeri dagli id e ordina
                var numA = parseInt(a.id.replace("card", ""));
                var numB = parseInt(b.id.replace("card", ""));
                return numA - numB;
            });
        console.log(cardsArray);

        cardsArray.forEach(card => {
            if(!(card.classList.contains("hideCard")) && card.id !== "card5"){
                card.classList.add("hideCard");
            }
            else if(card.id === "card5" && card.classList.contains("hideCard")){
                card.classList.remove("hideCard");
            }
            
        }); 
        console.log("activeStatus: " + sessionStorage.getItem("activeChange"));
        sessionStorage.setItem("activeChange", "vuoto");
        setTimeout(function() {
                alert("La somma totale degli sconti non può superare il 70%. Rivedi i valori inseriti.");
                console.log("È passato 1 secondo");
            }, 1000);
        

    }
    else if(sessionStorage.getItem("activeChange") == "campiVuotiSconti"){
        var cards = document.querySelectorAll("div[id^='card']");
        console.log(cards);

        var cardsArray = Array.from(cards).filter(div => /^card\d+$/.test(div.id)).sort((a, b) => {
                // Estrai i numeri dagli id e ordina
                var numA = parseInt(a.id.replace("card", ""));
                var numB = parseInt(b.id.replace("card", ""));
                return numA - numB;
            });
        console.log(cardsArray);

        cardsArray.forEach(card => {
            if(!(card.classList.contains("hideCard")) && card.id !== "card4"){
                card.classList.add("hideCard");
            }
            else if(card.id === "card4" && card.classList.contains("hideCard")){
                card.classList.remove("hideCard");
            }
            
        }); 
        console.log("activeStatus: " + sessionStorage.getItem("activeChange"));
        sessionStorage.setItem("activeChange", "vuoto");

    }
}

document.addEventListener("DOMContentLoaded", function() {
    modificaGiocoPreset();
}
);