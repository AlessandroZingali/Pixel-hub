<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
    <head>
        <title>Registrazione - PixelHub</title>        
        <link rel="stylesheet" type="text/css" href="Stile/Registrazione.css?v=1" /> 
        
    </head>
    <body>
        <div id="LoginCard">
            <div class="loginForm">
                <div id="logo">
                    <img src="Loghi/logo pixelhub slim.png" alt="logo pixelhub">
                </div>
                <form action="signin.php" method="post">
                    <div id="Nome">
                        <p>Nome</p>
                        <input type="text" placeholder="Mario" name="Nome"/>
                    </div>
                    <div id="Cognome">
                        <p>Cognome</p>
                        <input type="text" placeholder="Rossi" name="Cognome"/>
                    </div>
                    <div id="Email">
                        <p>Email</p>
                        <input type="text" placeholder="example@mail.com" name="Email"/>   
                    </div>
                    <div id="Password">
                        <p>Password</p>
                        <input type="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" name="Password"/>
                    </div>
                    <div id="Nickname">
                        <p>Nickname</p>
                        <input type="text" placeholder="SuperBazinga666" name="Nickname"/>
                        
                    </div>
                    <div id="DataNascita">
                        <p>Data di Nascita</p>
                        <input type="text" placeholder="01-01-1980" name="DataNascita"/>
                    </div>

                    <div>
                        <input type="submit" name="singin" value="Iscriviti" />
                    </div>
                    
                   
                </form>
            </div>
            <div id="footer">
                <ul>
                    <li><a href="">Contact Us</a></li>
                    <li><a href="">F.A.Q</a></li><br>
                    <li>&copy; 2024 Pixel Hub. Tutti i diritti riservati.</li>
                 </ul>
            </div>
        </div>
    </body>
</html>