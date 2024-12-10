<?php

include_once '../includes/functions.php';

showStates();

function showStates()
{
    list($getDisticntWorkOrders , $totalWorkOrderWithWithoutCron) = getStates();

    // echo "<pre>";print_R($getDisticntWorkOrders);exit;
   

    exit;
}

function getStates()
{
    $moneteringData = [];
    $totalWorkOrder = getDisticntWorkOrders();
    $distinctOrder = makeDistinctWO($totalWorkOrder);

    foreach ($distinctOrder as $orderNo) {
        $monetringChunk = [];
        $response = getMoniteringData($orderNo);

        if(!empty($response['work_order'])) {
            $monetringChunk['work_order_no'] = $response['work_order'][0]['WONumber'];
            $monetringChunk['wo_count'] = $response['work_order'][0]['wo_count'];
            $monetringChunk['work_order_date'] = $response['work_order'][0]['WorkOrderDate'];
        }else{
            $monetringChunk['work_order_no'] = $orderNo;
            $monetringChunk['wo_count'] = '';
            $monetringChunk['work_order_date'] = '';
        }

        if(!empty($response['work_order_docs'])){
            $monetringChunk['work_order_document'] = $response['work_order_docs'][0]['Document_Folder'];
        }else{
            $monetringChunk['work_order_document'] = '';
        }
        array_push($moneteringData , $monetringChunk);
    }
    

    echo "<pre>";print_R($moneteringData);exit;


    // return array($totalWorkOrder , $totalWorkOrderWithWithoutCron );
}


function makeDistinctWO ($totalWorkOrder) {
    $distinctOrder = array();
    foreach ($totalWorkOrder as $key => $value) {
        $distinctOrder[] = $value['WONumber'];
    }
    return array_unique($distinctOrder);
}

function getDisticntWorkOrders()
{   
    $today = new DateTime('2024-12-10'); 
    $today->modify('-7 days');
    $previousDateMinusSevenDays = $today->format('Y-m-d');  

    $query = sprintf('select a.`WONumber` as WONumber
                from manex_work_orders a 
                where DATE(a.`WorkOrderDate`) > "%1$s"

                UNION ALL

                select b.WONumber as WONumber
                from manex_work_order_documents b 
                where DATE(b.`UpdatedUTC`) > "%1$s"

                UNION ALL

                select a.wo_number as WONumber
                from _wo_cron_logs a 
                where DATE(a.`cron_date`) > "%1$s"

                UNION ALL

                select DISTINCT b.value as WONumber
                from sem_form_entry a 
                inner join sem_form_entry_values b ON a.id = b.entry_id
                inner join sem_form_field c ON a.form_id = 12
                where DATE(a.created) > "%1$s"
                and b.field_id = 76', $previousDateMinusSevenDays);
    $result = executeQuery($query);
    return getDataFromResultSet($result);
}

function getMoniteringData($orderNo)
{   
    $dataSequence = [];

    $work_order = sprintf('select count(*) as "wo_count" , a.WONumber , a.WorkOrderDate from manex_work_orders a where a.WONumber = "%1$s" ',  $orderNo);
    $work_order = executeQuery($work_order);
    $dataSequence['work_order'] = getDataFromResultSet($work_order);

    $work_order_docs = sprintf('select a.Document_Folder from manex_work_order_documents a where a.WONumber = "%1$s" ',  $orderNo);
    $work_order_docs = executeQuery($work_order_docs);
    $dataSequence['work_order_docs'] = getDataFromResultSet($work_order_docs);

    return $dataSequence;

}





