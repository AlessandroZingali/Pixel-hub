function swapperIn(){
    var cardProfilo = document.getElementById("card1");
    console.log("Ris: "+cardProfilo);
    var cardSettings = document.getElementById("card2");
    
    cardProfilo.classList.toggle("hideCard");
    cardSettings.classList.toggle("hideCard");
}