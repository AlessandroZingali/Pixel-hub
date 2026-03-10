function mostraRisultatiGiochi(str){ //Funzione per mostrare i risultati della ricerca in tempo reale
    if(str.length==0){
        var list=document.querySelectorAll("#liveSearchInputGiochi");
        for(var i=0; i<list.length; i++){
        list[i].innerHTML=this.innerHTML="";
        list[i].style.border="0px";
         //Aggiorno il contenuto di ciascun elemento con la risposta ricevuta

      }
         //Se la stringa è vuota, pulisco i risultati
        
        return;
    }
    var xmlhttp=new XMLHttpRequest(); //Creo un oggetto XMLHttpRequest per la comunicazione asincrona
    xmlhttp.onreadystatechange=function() { //Definisco la funzione di callback per gestire la risposta
    if (this.readyState==4 && this.status==200) { //Controllo se la richiesta è completata e ha avuto successo
      var list = document.querySelectorAll(".liveSearchGiochi"); //Prendo tutti gli elementi con la classe "liveSearchUtenti"
      for(var i=0; i<list.length; i++){
        list[i].innerHTML=this.responseText; //Aggiorno il contenuto di ciascun elemento con la risposta ricevuta
      }
    }
  }

  //Invio la richiesta GET al file liveSearchGiochiAdmin.php dando come parametro la stringa di ricerca
  xmlhttp.open("GET","liveSearchGiochiAdmin.php?Giochi="+str, true);
  xmlhttp.send();
}

function insertIdGiochi(element, id){ //Funzione per inserire l'ID dell'utente selezionato nella barra di ricerca
  var listOne = document.querySelectorAll('#liveSearchInputGiochi');
  for(var i=0; i<listOne.length; i++){
        listOne[i].value = element.innerHTML; //Aggiorno il contenuto di ciascun elemento con la risposta ricevuta
      }
 //Inserisco l'ID dell'utente selezionato nell'input della barra di ricerca
  var listTwo = document.querySelectorAll('#id_gioco_modifica');
    for(var i=0; i<listTwo.length; i++){
        listTwo[i].value = id; 
    }
}