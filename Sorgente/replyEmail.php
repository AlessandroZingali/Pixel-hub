<?php
    require 'PHPMailerRoot/src/Exception.php';
    require 'PHPMailerRoot/src/PHPMailer.php';
    require 'PHPMailerRoot/src/SMTP.php';
    require_once 'serverUtility.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $mail = new PHPMailer(true);

    try {

        $table_users='Tabella_Utenti';
        $baseDB = connectDB();

        if (mysqli_connect_errno()) {
            printf("problemi di connessione : %s\n", mysqli_connect_error(connectDB()));
        }


        $sql ="SELECT * FROM $table_users WHERE ID=\"".$_POST['IDUtente']."\";";
        
        $resultQ = mysqli_query($baseDB, $sql);
        $num = mysqli_num_rows($resultQ);
        if($num == 1){
            $row=mysqli_fetch_array($resultQ);
            $nomeUtente=$row["Nome"]." ".$row["Cognome"];
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'justfree609@gmail.com';
            $mail->Password = 'wmtb kgff vkfl acnj';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('justfree609@gmail.com');
            $mail->addAddress('tuliniriccardo99@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = 'Test';
            $mail->addEmbeddedImage('Loghi/logo pixelhub slim.png', 'logo');

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

            $mail->send();
            echo 'Email inviata!';
        }
    } 
    catch (Exception $e) {

        echo json_encode($mail->ErrorInfo);
    }
?>