/* Questo file js gestisce la barra di ricerca dei giochi nella home page.
  La barra di ricerca utilizza AJAX per mostrare i risultati in tempo reale mentre l'utente digita il nome del gioco desiderato.
  La ricerca sara effettuata tramite una chiamata ad una API in PHP che restituirà i risultati corrispondenti.
  Inoltre, gestisce il reset della barra di ricerca quando l'utente clicca al di fuori della barra stessa o dei risultati mostrati. */

//Gestione evento al click per resettare la barra di ricerca
window.addEventListener('click', resetTesto); 


function mostraRisultati(str){ //Funzione per mostrare i risultati della ricerca in tempo reale
    if(str.length==0){
        document.getElementById("livesearch").innerHTML=""; //Se la stringa è vuota, pulisco i risultati
        document.getElementById("livesearch").style.border="0px"; //Rimuovo il bordo
        return;
    }
      var xmlhttp=new XMLHttpRequest(); //Creo un oggetto XMLHttpRequest per la comunicazione asincrona
  xmlhttp.onreadystatechange=function() { //Definisco la funzione di callback per gestire la risposta
    if (this.readyState==4 && this.status==200) { //Controllo se la richiesta è completata e ha avuto successo
      document.getElementById("livesearch").innerHTML=this.responseText; //Aggiorno il contenuto della sezione dei risultati con la risposta ricevuta
    }
  }

  //Invio la richiesta GET al file livesearch.php dando come parametro la stringa di ricerca
  xmlhttp.open("GET","livesearch.php?game="+str, true);
  xmlhttp.send();
}

//Funzione per resettare la barra di ricerca quando si clicca al di fuori di essa o dei risultati
function resetTesto(){
  var searchBar = document.getElementById('livesearch');//Prendo la sezione dei risultati
  var listOutput = document.getElementById('searchBarInput');//Prendo l'input della barra di ricerca
  var clickonList = (event.target === searchBar); //Controllo se il click è avvenuto sulla sezione dei risultati tramite event.target
  var clickonInput = (event.target === listOutput); //Controllo se il click è avvenuto sull'input della barra di ricerca tramite event.target

  //Se il click non è avvenuto nè sulla sezione dei risultati nè sull'input della barra di ricerca, resetto la barra di ricerca
  if(!clickonList && !clickonInput){
    document.getElementById('livesearch').innerHTML = "";
    document.getElementById('searchBarInput').value = "";
  }
}