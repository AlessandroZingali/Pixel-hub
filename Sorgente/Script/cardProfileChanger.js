function swapperIn(){
    var cardProfilo = document.getElementsByClassName('cardProfilo');
    console.log("Ris: "+cardProfilo);
    var cardSettings = document.getElementsByClassName('cardSettings');
    
    cardProfilo.style.display = "none";
    cardSettings.style.display = "flex";
    
}