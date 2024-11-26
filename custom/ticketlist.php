<?php
include_once dirname(__FILE__) . '/includes/functions.php';

$thread_id = $ticket->getId();
renderTicketsSection($thread_id);

function renderTicketsSection($thread_id)
{
    $wo_arr = getWorKOrderNo($thread_id);
    if(!empty($wo_arr)){
        $ticket_list = getRelatedTickets($wo_arr[0]['wo_number'] , $thread_id , $wo_arr);
        include_once 'templates/ticketlist.tmp.php';
    }else{
        echo "No Work Order Found";
    }

    
}


function getWorKOrderNo ($thread_id) {
    $thread_id = intval($thread_id);

    if ($thread_id) {
        $query = sprintf('SELECT _wcl.wo_number , _wcl.part_no , _wcl.revision FROM sem_ticket _st JOIN _wo_cron_logs _wcl 
            ON _st.number = CONCAT("00", _wcl.ticket_id)
            WHERE _st.ticket_id = %1$d LIMIT 1', $thread_id);
        $result = executeQuery($query);
        return getDataFromResultSet($result);
    }
}

function getRelatedTickets($wo_number , $thread_id , $arr )
{
    $wo_number = intval($wo_number);
    if ($wo_number) {
        $query = sprintf('SELECT 
            _wcl.*, 
            _st.*, 
            _st.ticket_id AS "ticket_no",
            sts.name, -- Assuming `status_name` is a column in `sem_ticket_status` that you want to retrieve
            _su.name as "holder_name",
            CONCAT(_ss.firstname , " " , _ss.lastname) as "assignee"
        FROM 
            _wo_cron_logs _wcl
        JOIN 
            sem_ticket _st
            ON CONCAT("00", _wcl.ticket_id) = _st.number
        JOIN 
            sem_ticket_status sts
            ON _st.status_id = sts.id
        JOIN 
            sem_user _su
            ON _su.id = _st.user_id
        LEFT JOIN 
            sem_staff _ss
            ON _ss.staff_id = _st.staff_id
        WHERE 
            _wcl.wo_number = %1$d 
            AND _st.ticket_id != %2$d OR _wcl.part_no like CONCAT("%%", "%3$s" , "%%")  OR _wcl.revision like CONCAT("%%", "%4$s" , "%%") ', $wo_number , $thread_id , (string) $arr[0]['part_no'] ,(string) $arr[0]['revision'] );
        
        $result = executeQuery($query);
        return getDataFromResultSet($result);
    }
}
