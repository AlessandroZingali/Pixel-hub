<?php
/*Qusto file si occupa della risposta via mail ai ticket, in modo rapido e veloce. Viene richisto un servizio SMTP. 
In serverUtility si possono trovare e impostare i recapiti, tra cui mittente, destinatario predefiniti (per vedere il funzionamento 
delo script) e la API key della mail che si vuole usare. Per il resto abbiamo usato PHPMailRoot come framework per mandare mail. */
    require 'PHPMailerRoot/src/Exception.php';
    require 'PHPMailerRoot/src/PHPMailer.php';
    require 'PHPMailerRoot/src/SMTP.php';
    require_once 'serverUtility.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $mail = new PHPMailer(true); // Passiamo 'true' per abilitare le eccezioni

    try {
        
        //Connessione al database per recuperare i dati dell'utente a cui inviare la mail
        $table_users="Tabella_Utenti";
        $baseDB = connectDB();

        if (mysqli_connect_errno()) {
            printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
        }

        if(!isset($_POST['IDUtente']) && isset($_POST['Email'])){
            $sql ="SELECT * FROM $table_users WHERE Email=\"".$_POST['Email']."\";";
        }
        else $sql ="SELECT * FROM $table_users WHERE ID=\"".$_POST['IDUtente']."\";";

        
        $resultQ = mysqli_query($baseDB, $sql);
        $num = mysqli_num_rows($resultQ);
        if($num == 1){
            $row=mysqli_fetch_array($resultQ);
            $emailManager = new emailPointer();
            $nomeUtente=$row["Nome"]." ".$row["Cognome"];

            //Configurazione del server SMTP e invio della mail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $emailManager->mittente;
            $mail->Password = $emailManager->emailKey;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            //Impostazione del mittente, destinatario, oggetto e corpo della mail
            $mail->setFrom($emailManager->mittente);
            $mail->addAddress($emailManager->destinatario);
            
            //Il corpo della mail è in HTML, con un'immagine incorporata (logo) e il testo del ticket
            $mail->isHTML(true);
            $mail->Subject = 'Risposta Costumer Service PixelHub';
            $mail->addEmbeddedImage('Loghi/logo pixelhub slim.png', 'logo');

            if(!isset($_POST['IDUtente']) && isset($_POST['Email'])){
                $mail->Body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
            <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"it\" lang=\"it\">
                <head>
                </head>
                <body style=\"background-color: rgb(53, 49, 49); \">
                    <img src=\"cid:logo\" alt=\"Logo\">
                    <h2 style=\"color: white\">Riposta la ticket:".$_POST['IDTicket']."</h2>
                    <p style=\"color: white\">Gentile ".$nomeUtente.",</p></br>
                    <p style=\"color: white\">Le credenziali sono: Email = ".$row['Email']." Password = ".$row['Password']."</p>
                    <p style=\"color: white\">La ringraziamo per averci scelto,</p></br>
                    <p style=\"color: white\">Cordiali saluti, Pixel Hub staff</p>
                </body>
            </html>
                
            ";
            }
            else{
                $mail->Body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
                <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"it\" lang=\"it\">
                    <head>
                    </head>
                    <body style=\"background-color: rgb(53, 49, 49); \">
                        <img src=\"cid:logo\" alt=\"Logo\">
                        <h2 style=\"color: white\">Riposta la ticket:".$_POST['IDTicket']."</h2>
                        <p style=\"color: white\">Gentile ".$nomeUtente.",</p></br>
                        <p style=\"color: white\">".$_POST['text']."</p>
                        <p style=\"color: white\">La ringraziamo per averci scelto,</p></br>
                        <p style=\"color: white\">Cordiali saluti, Pixel Hub staff</p>
                    </body>
                </html>
                    
                ";
            }

            $mail->send();

            if(!isset($_POST['IDUtente']) && isset($_POST['Email'])) header('Location: Homepage.php');

            
            echo 'Email inviata!';
        }
    } 
    catch (Exception $e) {

        echo json_encode($mail->ErrorInfo); // In caso di errore, restituisce l'errore in formato JSON
    }
?>