<?php

include_once '../includes/functions.php';
$settings['topicId'] = array(
    15 => 'MPI Ticket', /* MPI Ticket */
    20 => ' WO Form', /* WO Form */
    21 => 'Technical Review', /* Technical Review  */
    22 => 'Material Review Consigned', /* Material Review Consigned*/
    26 => 'Turnkey Material Management', /* Material Review Turnkey*/
    23 => 'Test Flag', /* Test Flag */
    24 => 'CS Planning', /* CS Planning */
    25 => 'BOM Load', /* BOM Load */
);

$woticketcondition = array(
    'Turnkey' => array( /* SaleType */
        'No' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                // 20,
                21,
                26,
                23,
                15,
                30
            ),
            'Yes' => array( /* Revision */
                // 20,
                21,
                26,
                23,
                15,
                30
            )
        ),
        'Yes' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                // 20,
                21,
                26,
                23,
                15,
                30
            ),
        )
    )
    ,
    'Consignmnt' => array( /* SaleType */
        'No' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                // 20,
                23,
                21,
                15,
                22,
                30
            ),
            'Yes' => array( /* Revision */
                // 20,
                21,
                15,
                23,
                22,
                30
                
            )
        ),
        'Yes' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                // 20,
                23,
                15,
                21,
                22,
                30
            ),
        )
    )
);


processWOTickets($settings, $woticketcondition);

function processWOTickets($settings, $woticketcondition)
{
    try{
        list($subject, $msg, $customdata) = setCustomData();

        if (isValidArray($customdata)) {
            foreach ($customdata as $cs) {
                $revision = 'No';
                $topicIds = $woticketcondition[$cs['_wo_saletype']][$cs['_repeat_flag']][$revision];
                foreach ($topicIds as $topicId) {
                    if($topicId == 23 && empty($cs['_wo_test_flag'])){
                        echo "test flag is empty" . $cs['won'];
                    }else{
                        $topicTitle = $settings['topicId'][$topicId];
                        $response = checkAlreadyCreated($topicId , $cs['won']);
                        if(empty($response)) {
                            $ticketId = createTicket( 'Auto '.' (' . $topicTitle . ') '. $cs['won'] .' '. $cs['_custpn_revision'] , $msg, $topicId, $cs);
                            /* Insert In Log Table For Reference */
                            if ($ticketId) {
                                generateWOLog($ticketId, $topicId, $cs);
                            }
                        }else{
                            echo "ticket already created with given topic and wo number".$topicId .'-'.$cs['won'].'---';
                        }
                   
                    }
             
                }
            }
        }else{
            echo "No Work Orders Found";
        }
    }catch(Exception $e) {
        echo "ERROR: ". $e;

    }
    
}



function setCustomData()
{
    $subject = 'Automated WO: ';
    $msg = '';
    $tech_review_ticket_arr = getTechReviewTicketsOrders();
    $arr = getDataFromDB();
    return array($subject, $msg, $arr);
}

function checkAlreadyCreated ($topicId , $wo_number) { 
    $fields = 'a.ticket_id , a.topic_id , b.object_id ,  b.form_id ,  c.value ';
    $query = sprintf('select %1$s from sem_ticket a
                    inner join sem_form_entry b ON a.ticket_id = b.object_id
                    inner join sem_form_entry_values c ON b.id = c.entry_id
                    where a.topic_id = "%2$s"
                    and c.value = "%3$s" ', $fields, $topicId , $wo_number);
    $result = executeQuery($query);
    return getDataFromResultSet($result);
}

function getDataFromDB($wo_no = '')
{  
    $today = date('Y-m-d');
    $fields = '_wo.WONumber  as won, _wo.UNIQ_KEY as uniq_key, _wo.SaleType as _wo_saletype, _wo.WOStatus as _wo_status, if(_wo.RepeatOrderFlag = \'Repeat\', "Yes", "No") as _repeat_flag, DATE_FORMAT(_wo.WorkOrderDate, "%d/%m/%y")  as _wo_create_date , DATE_FORMAT(_wo.StartDate, "%d/%m/%y")  as _wo_start_date , DATE_FORMAT(_wo.DueDate, "%d/%m/%y")  as _wo_due_date, DATE_FORMAT(_wo.ScheduledCompleteDate, "%d/%m/%y")  as _scheduled_complete_date, DATE_FORMAT(_wo.PlannedCompleteDate, "%d/%m/%y")  as _wo_complete_planned_date, DATE_FORMAT(_wo.ReleaseDate, "%d/%m/%y")  as _release_date, DATE_FORMAT(_wo.CompleteDate, "%d/%m/%y") as _wo_complete_date, _wo.WOQty as _wo_quantity, _wo.WOCompleteQty as _wo_complete_quantity, _wo.WORemainingQty as _wo_balanace_quantity, _wd.Document_Folder as _utc_time, _wo.Customer as _cus_name, _wo.CustomerPONumber as _cus_po, _mi.ItemPartNo as _cus_pn, _mi.ItemRevision as _cus_pn_rev , CONCAT(_mi.ItemPartNo , " " , _mi.ItemRevision) as _custpn_revision , if(_wo.TestRequiredFlag = \'Test\', "Yes", "No") as _wo_test_flag , if(_wo.SaleType = \'Consignmnt\', "Yes", "No") as _is_consigned , if(_wo.TestRequiredFlag = \'Yes\', "Yes", "No") as _test_flag , _wo.Lead_Requirement as _lead_requirement , _wo.Clean_Processing as _clean_processing , _wo.RMAFlagCode as _rma_flag';
    $query = sprintf('SELECT %1$s FROM manex_work_orders AS _wo
            INNER JOIN manex_items AS _mi ON _wo.UNIQ_KEY = _mi.UNIQ_KEY
            INNER JOIN manex_work_order_documents AS _wd ON _wo.WONumber = _wd.WONumber
            INNER JOIN _wo_cron_logs AS b ON _wo.WONumber = b.wo_number
            INNER JOIN sem_ticket AS c ON b.topic_id = c.topic_id
            WHERE _wo.WONumber > 17959 
            and c.topic_id = 27 
            AND c.status_id = 3;', $fields , $today);

    $result = executeQuery($query);
    return getDataFromResultSet($result);

}

function getTechReviewTicketsOrders() {

}


function generateWOLog($ticketId, $topicId, $data)
{
    $query = sprintf('INSERT INTO `_wo_cron_logs` values (NULL, %1$d, %2$d, %3$s, \'%4$s\', \'%5$s\', \'%6$s\', \'%7$s\', UTC_TIMESTAMP())', $ticketId, $topicId, $data['won'], $data['uniq_key'], $data['_cus_pn'], $data['_cus_pn_rev'], json_encode($data));
    /* echo $query; */
    $result = executeQuery($query);
}
