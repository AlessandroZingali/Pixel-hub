function swapperInSettings(){
    var cardProfilo = document.getElementById("card1");
    console.log("Ris: "+cardProfilo);
    var cardSettings = document.getElementById("card2");
    
    cardProfilo.classList.toggle("hideCard");
    cardSettings.classList.toggle("hideCard");
}

function swapperInStore(){
    var cardProfilo = document.getElementById("card1");
    console.log("Ris: "+cardProfilo);
    var cardStore = document.getElementById("card3");
    
    cardProfilo.classList.toggle("hideCard");
    cardStore.classList.toggle("hideCard");
}