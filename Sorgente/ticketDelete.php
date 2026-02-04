<?php 
    require_once 'serverUtility.php';

    $doc = xmlPointer('XML/Ticket.xml');
    $root = $doc->documentElement;
    $elem = $root->childNodes;

    foreach ($elem as $ticket) {
        if($ticket->getAttribute('id_ticket') == $_POST['IDTicket'] && $ticket->getAttribute('id_utente') == $_POST['IDUtente']) {
            $ticket->parentNode->removeChild($ticket);
            break;
        }
    }
    $doc->save('XML/Ticket.xml');

?>