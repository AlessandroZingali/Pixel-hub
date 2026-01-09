window.addEventListener('click', resetTesto);


function mostraRisultati(str){
    if(str.length==0){
        document.getElementById("livesearch").innerHTML="";
        document.getElementById("livesearch").style.border="0px";
        return;
    }
      var xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("livesearch").innerHTML=this.responseText;
    }
  }
    xmlhttp.open("GET","livesearch.php?game="+str,true);
  xmlhttp.send();
}

function resetTesto(){
  var searchBar = document.getElementById('livesearch');
  var listOutput = document.getElementById('searchBarInput');
  var clickonList = event.target === searchBar;
  var clickonInput = event.target === listOutput;

  if(!clickonList && !clickonInput){
    document.getElementById('livesearch').innerHTML = "";
    document.getElementById('searchBarInput').value = "";
  }
}