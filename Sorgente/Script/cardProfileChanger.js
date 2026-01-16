window.addEventListener("load", invalidation);

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

function invalidation(){

    $flag = isNaN(localStorage.getItem("invalidFlag")) ? 0 : localStorage.getItem("invalidFlag");

    if($flag == 1){ 
        swapperInSettings();

        setTimeout(function () {
         alert('Email Errata'); }, 500);
        
        localStorage.removeItem("invalidFlag");

    } 
    else if($flag == 2){
        swapperInSettings();

        setTimeout(function () {
         alert('Password Errata'); }, 500);
        
        localStorage.removeItem("invalidFlag");
    } 
    else if($flag == 3){ 
        swapperInStore();

        setTimeout(function () {
        alert('fondi insufficenti'); }, 500);
        
        localStorage.removeItem("invalidFlag");
    }
    else if($flag == 4){
        swapperInStore();
    
        setTimeout(function () {
        alert('Immagine profilo gia acquistata!'); }, 500);
        
        localStorage.removeItem("invalidFlag");
    }

}