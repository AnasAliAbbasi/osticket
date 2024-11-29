<?php

include_once '../includes/functions.php';

deleteTickets();

function deleteTickets()
{
    list($subject, $msg, $customdata) = setCustomData();

    if (isValidArray($customdata)) {
        foreach ($customdata as $cs) {
            $updateTicket = updateTicketStatus($cs['wo_number'], $cs['number']);
            if ($updateTicket) { 
                updateEvents($cs);
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
    $fields = 'l.wo_number, l.ticket_id , m.WOStatus, s.status_id , s.number , s.dept_id , s.staff_id , s.topic_id , s.user_id' ;
    $query = sprintf('select %1$s from _wo_cron_logs l join manex_work_orders m on l.wo_number=m.WONumber join sem_ticket s on l.ticket_id=s.ticket_id
                    where m.WOStatus NOT in ("Closed","Cancel") and s.status_id<3', $fields);
    
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

function updateEvents($data)
{
    $query = sprintf(
        'INSERT INTO `sem_thread_event` (`thread_id`, `thread_type`, `event_id`, `staff_id`, `team_id`, `dept_id`, `topic_id`, `data`, `username`, `uid`, `uid_type`, `annulled`, `timestamp`) 
        VALUES (%1$d, \'%2$s\', %3$d, %4$d, %5$d, %6$d, %7$d, \'%8$s\', \'%9$s\', %10$d, \'%11$s\', %12$s, UTC_TIMESTAMP())',
        $data['ticket_id'],      // %1$d
        'T',    // %2$s
        '2',       // %3$d
        $data['staff_id'],       // %4$d
        0,        // %5$d
        $data['dept_id'],        // %6$d
        $data['topic_id'],       // %7$d
        "",           // %8$s
        'Auto',       // %9$s
        '',            // %10$d
        'S',       // %11$s
        0,      // %12$s
        date('Y-m-d H:i:s')
    );
    
    echo $query;exit;
    /* echo $query; */
    $result = executeQuery($query);
}

function updateTicketStatus ($wo_number, $ticket_id) {

    $query = sprintf('update sem_ticket set status_id=3 where number=%1$d', $ticket_id);
    $result = executeQuery($query);
    return $result;
}