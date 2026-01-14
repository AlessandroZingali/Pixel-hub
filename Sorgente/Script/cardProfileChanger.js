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
    console.log(localStorage.getItem("invalidFlag"));
    $flag = isNaN(localStorage.getItem("invalidFlag")) ? 0 : localStorage.getItem("invalidFlag");
    console.log($flag);
    if($flag == 1) alert('Password Errata');
    else if($flag == 2) alert('Email Errata');
    else if($flag == 3){ 
        swapperInStore();
        alert('fondi insufficenti');
        localStorage.removeItem("invalidFlag");
    }

}