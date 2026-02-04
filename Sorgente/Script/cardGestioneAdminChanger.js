function swapperInTickets(){ //Funzione per lo scambio di card tra ticket  e funzioni avanzate
    var cardPrincipale = document.getElementById("card0"); //Prendo la card del ticket

    var cardTicket = document.getElementById("card1"); //Prendo la card delle impostazioni tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardPrincipale.classList.toggle("hideCard"); 
    cardTicket.classList.toggle("hideCard");
}
         

function swapperInSettings(){ //Funzione per lo scambio di card tra ticket  e funzioni avanzate
    var cardPrincipale = document.getElementById("card0"); //Prendo la card del ticket

    var cardModificaGioco = document.getElementById("card2"); //Prendo la card delle impostazioni tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardPrincipale.classList.toggle("hideCard"); 
    cardModificaGioco.classList.toggle("hideCard");
}



function swapperInModificaGioco(){ 
    var cardPrincipale = document.getElementById("card0"); //Prendo la card del profilo tramite il document

    var cardModificaGioco = document.getElementById("card3"); //Prendo la card dello store tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardPrincipale.classList.toggle("hideCard");
    cardModificaGioco.classList.toggle("hideCard");
}
function swapperInRicerca(){ 
    var cardPrincipale = document.getElementById("card0"); //Prendo la card del profilo tramite il document

    var cardRicerca = document.getElementById("card4"); //Prendo la card dello store tramite il document

    //Uso classList.toggle per aggiungere/rimuovere la classe hideCard
    cardPrincipale.classList.toggle("hideCard");
    cardRicerca.classList.toggle("hideCard");
}



function swapperInSospensione(){
    var cardPrincipale =document.getElementById("card0");
    var cardSospensione = document.getElementById("card5");

    cardPrincipale.classList.toggle("hideCard");
    cardSospensione.classList.toggle("hideCard");
    
}
function swapperSearchUtente(){
    var cardPrincipale =document.getElementById("card0");
    var cardSearchUtente = document.getElementById("card6");

    cardPrincipale.classList.toggle("hideCard");
    cardSearchUtente.classList.toggle("hideCard");
    
}
function swapperInUserManagement(){
    var cardPrincipale =document.getElementById("card0");
    var cardSearchUtente = document.getElementById("card7");

    cardPrincipale.classList.toggle("hideCard");
    cardSearchUtente.classList.toggle("hideCard");
    
}

function swapperInSearchUtenteRim(){
    var cardPrincipale =document.getElementById("card0");
    var cardCercaUtenteRim = document.getElementById("card8");

    cardPrincipale.classList.toggle("hideCard");
    cardCercaUtenteRim.classList.toggle("hideCard");
    
}

function swapperInGestioneRimborsi(){
    var cardPrincipale =document.getElementById("card0");
    var cardRimborsi = document.getElementById("card9");

    cardPrincipale.classList.toggle("hideCard");
    cardRimborsi.classList.toggle("hideCard");
    
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

    else if(sessionStorage.getItem("activeChange") == "gestioneUtente"){
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
            if(!(card.classList.contains("hideCard")) && card.id !== "card7"){
                card.classList.add("hideCard");
            }
            else if(card.id === "card7" && card.classList.contains("hideCard")){
                card.classList.remove("hideCard");
            }
            
        }); 
        console.log("activeStatus: " + sessionStorage.getItem("activeChange"));
        sessionStorage.setItem("activeChange", "vuoto");

    }
    if(sessionStorage.getItem("activeChange") == "gestioneRimborso"){
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
            if(!(card.classList.contains("hideCard")) && card.id !== "card9"){
                card.classList.add("hideCard");
            }
            else if(card.id === "card9" && card.classList.contains("hideCard")){
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


