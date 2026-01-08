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
    xmlhttp.open("GET","livesearch.php?q="+str,true);
  xmlhttp.send();
}