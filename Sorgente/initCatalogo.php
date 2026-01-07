<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Caricamento catalogo...</title>
        <script>
            var correct = 0;
            var xmlHttp = new XMLHttpRequest();
            var width = window.innerWidth;

            var visibili = 5;

            if (width <= 1000) visibili = 2;


            else if (width <= 1200) visibili = 3;
            else if (width <= 1500) visibili = 4;
            xmlHttp.onreadystatechange = function() {    
            if (xmlHttp.readyState === 4 && xmlHttp.status === 200) {
                
                console.log(xmlHttp.responseText);
                window.location.replace("prova.php")

            
                }
            };
            xmlHttp.open("POST", "gestioneCatalogo.php", true);
            xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlHttp.send("visibili=" +visibili);
            

        </script>
    </head>
<body>
</body>
</html>