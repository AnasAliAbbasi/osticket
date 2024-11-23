<?php
include_once dirname(__FILE__) . '/includes/functions.php';

$thread_id = $ticket->getId();
renderTicketsSection($thread_id);

function renderTicketsSection($thread_id)
{
    $wo_arr = getWorKOrderNo($thread_id);
    if(!empty($wo_arr)){
        $ticket_list = getRelatedTickets($wo_arr[0]['wo_number']);
        include_once 'templates/ticketlist.tmp.php';
    }else{
        echo "No Work Order Found";
    }

    
}


function getWorKOrderNo ($thread_id) {
    $thread_id = intval($thread_id);

    if ($thread_id) {
        $query = sprintf('SELECT _wcl.wo_number from sem_ticket _st JOIN _wo_cron_logs _wcl 
            where _st.number = _wcl.ticket_id
            and _st.ticket_id = %1$d limit 1', $thread_id);
        $result = executeQuery($query);
        return getDataFromResultSet($result);
    }
}

function getRelatedTickets($wo_number)
{
    $wo_number = intval($wo_number);
    if ($wo_number) {
        $query = sprintf('SELECT * from _wo_cron_logs _wcl where _wcl.wo_number = %1$d ', $wo_number);
        $result = executeQuery($query);
        return getDataFromResultSet($result);
    }
}
