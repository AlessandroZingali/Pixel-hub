function swapperIn(){
    var cardProfilo = document.getElementsById("card1");
    console.log("Ris: "+cardProfilo);
    var cardSettings = document.getElementsById("card2");
    
    cardProfilo.style.display = "none";
    cardSettings.style.display = "flex";
    
}