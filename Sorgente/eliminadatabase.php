<html>
   <head><title>Elimino DATABASE </title></head>
   <body>
      <?php
    //Effettua la connessione al database sul localhost per eliminarlo usata a scopo di debug
		$mysqliConnection = new mysqli("localhost", "Alessandro", "belandi");
         
         if(!(mysqli_connect_errno())){ {
       
            printf('connessione avvenuta con successo.<br />');
            $query="DROP DATABASE Database_Pixel_Hub";
            $resulQ=mysqli_query($mysqliConnection, $query);

            if ($resulQ) {
                printf("Database eliminato.<br />");
            }
            else {
                printf("Database non eliminato <br />");
            }

            $mysqliConnection->close();
            }

		}
        else {
            printf("connessione fallita: <br />");
        }     
        ;
		
      ?>
   </body>
</html>