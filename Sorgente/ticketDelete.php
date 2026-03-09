<?php 
/* Questo file gestisce il funzionamento della cancellazione del file XML per i ticket. Questo script 
viene chiamato da una doppia chiamata ajax indentata una dentro l'altra; cosi da gestire i casi di errore nell'invio */
    require_once 'serverUtility.php';

    $doc = getDoc('XML/Ticket.xml');
    $root = $doc->documentElement;
    $elem = $root->childNodes;
    $esito = 'false';

    foreach ($elem as $ticket) {
        if($ticket->getAttribute('id_ticket') == $_POST['IDTicket'] && $ticket->getAttribute('id_utente') == $_POST['IDUtente']) {
            $ticketToDelete = $ticket;
            break;
        }
    }
    if(isset($ticketToDelete)) {
        if($ticketToDelete !== null && $ticketToDelete->parentNode != null) {
            $ticketToDelete->parentNode->removeChild($ticketToDelete);
            $esito = 'true';
        } 
    }
    $doc->save('XML/Ticket.xml');
    echo $esito;
    

?>