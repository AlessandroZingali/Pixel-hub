function modificaGiocoPreset(){
    console.log("activeStatusEst: " + sessionStorage.getItem("activeChangeEst"));
    if(sessionStorage.getItem("activeChangeEst") == 'true' ){
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
            if(!(card.classList.contains("hideCard")) && card.id !== "card2"){
                card.classList.add("hideCard");
            }
            else if(card.id === "card2" && card.classList.contains("hideCard")){
                card.classList.remove("hideCard");
            }
        }); 
        console.log("activeStatus2: " + sessionStorage.getItem("activeChangeEst"));
        sessionStorage.setItem("activeChangeEst", 'vuoto');
        console.log("activeStatus2: " + sessionStorage.getItem("activeChangeEst"));
    }
    
    
}

document.addEventListener("DOMContentLoaded", function() {
    modificaGiocoPreset();
}
);