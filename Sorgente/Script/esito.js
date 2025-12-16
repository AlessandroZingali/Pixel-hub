

/*window.addEventListener('load', start);
window.addEventListener('beforeunload', end);

function start(){
    var button = document.getElementById("submit");
    button.addEventListener('click', clicked);

    esitoDisplayer();
}

function end(){
    localStorage.setItem('uscito', "true");
}


function esitoDisplayer(){

    
    var esitoContainer = document.getElementById("esito");
    var esitoText = esitoContainer.textContent;
    
    var cont1 = localStorage.getItem('uscito');

    var esegui = isNaN(cont1) ? false : true;

    if(esegui){
        localStorage.removeItem('uscito');
    }
    else{}

    var cont2 = parseInt(localStorage.getItem('clicked'));
    var clicked = isNaN(cont2) ? 0 : cont2;

    //alert("entrato nel load");

    if(esitoText != "none" && clicked === 1){
       // alert("entrato nell'if");
        esitoContainer.style.display="inline";
        localStorage.setItem('clicked', "0");
    }

}

function clicked(){

    localStorage.setItem('clicked', "1");
}*/