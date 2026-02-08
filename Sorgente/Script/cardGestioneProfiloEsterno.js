function modificaGiocoPreset(){

    if(sessionStorage.getItem("activeChangeEst") == 'true' ){
        var card1 = document.getElementById("card1");
        card1.style.display = "none";
        card2.style.display = "block";
    }
    
    if(sessionStorage.getItem("activeChangeEst") == 'false' ){
        var card2 = document.getElementById("card2");
        card2.style.display = "none";
        card1.style.display = "block";
    }
    
    
}

document.addEventListener("DOMContentLoaded", function() {
    modificaGiocoPreset();
}
);