<?php

include_once '../includes/functions.php';
$settings['topicId'] = array(
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
                20,
            ),
            'Yes' => array( /* Revision */
                20,
            )
        ),
        'Yes' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                20,
            ),
        )
    ),
    'Consignmnt' => array( /* SaleType */
        'No' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                20,
            ),
            'Yes' => array( /* Revision */
                20,
            )
        ),
        'Yes' => array( /* RepeatOrderFlag */
            'No' => array( /* Revision */
                20,
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
            $revision = 'No';
            $topicIds = $woticketcondition[$cs['_wo_saletype']][$cs['_repeat_flag']][$revision];
            foreach ($topicIds as $topicId) {
                $document = getDocumentAvailable($cs['won']);
                $cs['_utc_time'] = (!empty($document) ? $document[0]['Document_Folder'] : 'Docment Not Available Now' ); 
                $topicTitle = $settings['topicId'][$topicId];
                $ticketId = createTicket( 'Auto '.' (' . $topicTitle . ') '. $cs['won'] .' '. $cs['_custpn_revision'] , $msg, $topicId, $cs);
                /* Insert In Log Table For Reference */
                if ($ticketId) {
                    generateWOLog($ticketId, $topicId, $cs);
                }
            }
        }
    }else{
        echo "No Work Orders Found";
    }
}



function setCustomData()
{
    $subject = 'Automated WO: ';
    $msg = '';
    $arr = getDataFromDB();
    return array($subject, $msg, $arr);
}


function getDataFromDB()
{

    $today = date('Y-m-d');
    $fields = '_wo.WONumber  as won, _wo.UNIQ_KEY as uniq_key, _wo.SaleType as _wo_saletype, _wo.WOStatus as _wo_status, if(_wo.RepeatOrderFlag = \'Repeat\', "Yes", "No") as _repeat_flag, _wo.WorkOrderDate as _wo_create_date, _wo.StartDate as _wo_start_date, _wo.DueDate as _wo_due_date, _wo.ScheduledCompleteDate as _scheduled_complete_date, _wo.PlannedCompleteDate as _wo_complete_planned_date, _wo.ReleaseDate as _release_date, _wo.CompleteDate as _wo_complete_date, _wo.WOQty as _wo_quantity, _wo.WOCompleteQty as _wo_complete_quantity, _wo.WORemainingQty as _wo_balanace_quantity, _wo.Customer as _cus_name, _wo.CustomerPONumber as _cus_po, _mi.ItemPartNo as _cus_pn, _mi.ItemRevision as _cus_pn_rev , CONCAT(_mi.ItemPartNo , " " , _mi.ItemRevision) as _custpn_revision , if(_wo.SaleType = \'Consignmnt\', "Yes", "No") as _is_consigned , if(_wo.TestRequiredFalg = \'Yes\', "Yes", "No") as _test_flag , _wo.Lead_Requirement as _lead_requirement , _wo.Clean_Processing as _clean_processing';
    $query = sprintf('SELECT %1$s FROM manex_work_orders AS _wo INNER JOIN manex_items AS _mi ON _wo.UNIQ_KEY = _mi.UNIQ_KEY LEFT JOIN _wo_cron_logs AS _log ON _wo.WONumber = _log.wo_number WHERE _wo.SaleType IS NOT NULL AND _log.wo_number IS NULL AND DATE(_wo.WorkOrderDate) = "%2$s" ORDER BY _wo.WONumber DESC', $fields , $today);
    $result = executeQuery($query);
    return getDataFromResultSet($result);

}



function generateWOLog($ticketId, $topicId, $data)
{
    $query = sprintf('INSERT INTO `_wo_cron_logs` values (NULL, %1$d, %2$d, %3$s, \'%4$s\', \'%5$s\', \'%6$s\', \'%7$s\', UTC_TIMESTAMP())', $ticketId, $topicId, $data['won'], $data['uniq_key'], $data['_cus_pn'], $data['_cus_pn_rev'], json_encode($data));
    /* echo $query; */
    $result = executeQuery($query);
}

function getDocumentAvailable ($wo_number){
    $fields = '_wd.Document_Folder';
    $query = sprintf('select %1$s from manex_work_order_documents _wd where _wd.WONumber = '.$wo_number.' limit 1', $fields);
    $result = executeQuery($query);
    return getDataFromResultSet($result);
}