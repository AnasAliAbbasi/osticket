<?php

include_once '../includes/functions.php';

deleteTickets();

function deleteTickets()
{
    list($subject, $msg, $customdata) = setCustomData();

    if (isValidArray($customdata)) {
        foreach ($customdata as $cs) {
            $updateTicket = updateTicketStatus($cs['wo_number'], $cs['ticket_id']);
            if ($updateTicket) { 
                generateWOLog($cs);
            }
            echo  "Ticket ID: ".$cs['ticket_id']." is updated to closed.\n";
        }
    }else{
        echo "No OPen Tickets Found";
    }
}



function setCustomData()
{
    $subject = 'Ticket No: ';
    $msg = '';
    $arr = getDataFromDB();
    return array($subject, $msg, $arr);
}


function getDataFromDB($wo_no = '')
{   
    $fields = 'l.wo_number, l.ticket_id , m.WOStatus, s.status_id';
    $query = sprintf('select %1$s from _wo_cron_logs l join manex_work_orders m on l.wo_number=m.WONumber join sem_ticket s on l.ticket_id=s.ticket_id
                    where m.WOStatus in ("Closed","Cancel") and s.status_id<3', $fields);
    
    $result = executeQuery($query);
    return getDataFromResultSet($result);
}

function getTechReviewTicketsOrders() {

}


function generateWOLog($data)
{
    $query = sprintf('INSERT INTO `_wo_cron_ticket_close_logs` values (NULL, %1$d, %2$d, \'%3$s\', \'%4$s\', UTC_TIMESTAMP())', $data['ticket_id'], $data['wo_number'], ''.$data['WOStatus'].'', $data['status_id']);
    /* echo $query; */
    $result = executeQuery($query);
}


function updateTicketStatus ($wo_number, $ticket_id) {

    $query = sprintf('update sem_ticket set status_id=3 where ticket_id=%1$d', $ticket_id);
    $result = executeQuery($query);
    return $result;
}