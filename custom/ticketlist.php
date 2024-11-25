<?php
include_once dirname(__FILE__) . '/includes/functions.php';

$thread_id = $ticket->getId();
renderTicketsSection($thread_id);

function renderTicketsSection($thread_id)
{
    $wo_arr = getWorKOrderNo($thread_id);
    if(!empty($wo_arr)){
        $ticket_list = getRelatedTickets($wo_arr[0]['wo_number'] , $thread_id);
        include_once 'templates/ticketlist.tmp.php';
    }else{
        echo "No Work Order Found";
    }

    
}


function getWorKOrderNo ($thread_id) {
    $thread_id = intval($thread_id);

    if ($thread_id) {
        $query = sprintf('SELECT _wcl.wo_number FROM sem_ticket _st JOIN _wo_cron_logs _wcl 
            ON _st.number = CONCAT("00", _wcl.ticket_id)
            WHERE _st.ticket_id = %1$d LIMIT 1', $thread_id);
        $result = executeQuery($query);
        return getDataFromResultSet($result);
    }
}

function getRelatedTickets($wo_number , $thread_id )
{
    $wo_number = intval($wo_number);
    if ($wo_number) {
        $query = sprintf('SELECT _wcl.*, _st.ticket_id AS "ticket_no" 
            FROM _wo_cron_logs _wcl 
            JOIN sem_ticket _st
                ON CONCAT("00", _wcl.ticket_id) = _st.number
            WHERE _wcl.wo_number = %1$d and _st.ticket_id != %2$d', $wo_number , $thread_id);
        $result = executeQuery($query);
        return getDataFromResultSet($result);
    }
}
