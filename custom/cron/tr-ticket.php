<?php

include_once '../includes/functions.php';
$settings['topicId'] = array(
    15 => 'MPI Ticket', /* MPI Ticket */
    20 => ' WO Form', /* WO Form */
    21 => 'Technical Review', /* Technical Review  */
    22 => 'Material Review', /* Material Review */
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
                22,
                23,
                15,
                25
            ),
            'Yes' => array( /* Revision */
                // 20,
                21,
                22,
                23,
                15
            )
        ),
        'Yes' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                // 20,
                21,
                22,
                23
            ),
        )
    )
    ,
    'Consignmnt' => array( /* SaleType */
        'No' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                // 20,
                23,
                21
            ),
            'Yes' => array( /* Revision */
                // 20,
                21,
                15,
                23
            )
        ),
        'Yes' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                // 20,
                23,
                15,
                21
            ),
        )
    )
);


processWOTickets($settings, $woticketcondition);

function processWOTickets($settings, $woticketcondition)
{
    list($subject, $msg, $customdata) = setCustomData();

    if (isValidArray($customdata)) {
        foreach ($customdata as $cs) {
            echo "<pre>";print_r($cs);exit;
            
            $revision = 'No';
            $topicIds = $woticketcondition[$cs['_wo_saletype']][$cs['_repeat_flag']][$revision];
            foreach ($topicIds as $topicId) {
                if($topicId == 23 && empty($cs['_wo_test_flag'])){
                    echo "test flag is empty" . $cs['won'];
                }else{
                    $topicTitle = $settings['topicId'][$topicId];
                    $ticketId = createTicket($subject . $cs['won'] . ' (' . $topicTitle . ')', $msg, $topicId, $cs);
                    /* Insert In Log Table For Reference */
                    if ($ticketId) {
                        generateWOLog($ticketId, $topicId, $cs);
                    }
                }
         
            }
        }
    }else{
        echo "no data found";
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


function getDataFromDB($wo_no = '')
{
    $fields = '_wo.WONumber  as won, _wo.UNIQ_KEY as uniq_key, _wo.SaleType as _wo_saletype, _wo.WOStatus as _wo_status, if(_wo.RepeatOrderFlag = \'Repeat\', "Yes", "No") as _repeat_flag, _wo.WorkOrderDate as _wo_create_date, _wo.StartDate as _wo_start_date, _wo.DueDate as _wo_due_date, _wo.ScheduledCompleteDate as _scheduled_complete_date, _wo.PlannedCompleteDate as _wo_complete_planned_date, _wo.ReleaseDate as _release_date, _wo.CompleteDate as _wo_complete_date, _wo.WOQty as _wo_quantity, _wo.WOCompleteQty as _wo_complete_quantity, _wo.WORemainingQty as _wo_balanace_quantity, _wd.Document_Folder as _utc_time, _wo.Customer as _cus_name, _wo.CustomerPONumber as _cus_po, _mi.ItemPartNo as _cus_pn, _mi.ItemRevision as _cus_pn_rev , _wo.TestRequiredFalg as _wo_test_flag';
    $query = sprintf('SELECT %1$s
                FROM manex_work_orders AS _wo
                INNER JOIN manex_items AS _mi ON _wo.UNIQ_KEY = _mi.UNIQ_KEY
                INNER JOIN manex_work_order_documents AS _wd ON _wo.WONumber = _wd.WONumber
                INNER JOIN (
                    SELECT wo_number
                    FROM _wo_cron_logs
                    WHERE topic_id = 20
                    GROUP BY wo_number
                    HAVING COUNT(*) = 1  -- Exclude work orders with more than one log
                ) AS _wcl ON _wo.WONumber = _wcl.wo_number
                WHERE _wo.WOStatus NOT IN ("Cancel", "Closed")
                AND _mi.ItemPartNo REGEXP "^(910|R910|940)";', $fields);
    
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
