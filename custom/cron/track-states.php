<?php

include_once '../includes/functions.php';

function showStates()
{
    list($moneteringData) = getStates();
    return $moneteringData;
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
            $monetringChunk['work_order_document_date'] = $response['work_order_docs'][0]['UpdatedUTC'];
            
        }else{
            $monetringChunk['work_order_document'] = '';
            $monetringChunk['work_order_document_date'] = '';

        }

        if(!empty($response['work_order_form_data'])){
            $monetringChunk['work_order_form_data_count'] = $response['work_order_form_data'][0]['form_data_count'];
        }else{
            $monetringChunk['work_order_form_data_count'] = 0;
        }

        if(!empty($response['work_order_ticket_without_cron'])){
            $monetringChunk['work_order_all_ticket'] = $response['work_order_ticket_without_cron'][0]['TotalTickets'];
            $monetringChunk['work_order_ticket_with_cron'] = $response['work_order_ticket_without_cron'][0]['TicketsWithCron'];
            $monetringChunk['work_order_ticket_without_cron'] = $response['work_order_ticket_without_cron'][0]['TicketsWithoutCron'];
            
        }else{
            $monetringChunk['work_order_all_ticket'] = 0;
            $monetringChunk['work_order_ticket_with_cron'] = 0;
            $monetringChunk['work_order_ticket_without_cron'] = 0;
            
        }

        array_push($moneteringData , $monetringChunk);
    }

    return array($moneteringData);
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

    $work_order_docs = sprintf('select a.Document_Folder , a.UpdatedUTC from manex_work_order_documents a where a.WONumber = "%1$s" ',  $orderNo);
    $work_order_docs = executeQuery($work_order_docs);
    $dataSequence['work_order_docs'] = getDataFromResultSet($work_order_docs);

    $work_order_form_data = sprintf('select count(*) as form_data_count from sem_form_entry_values a where a.value = "%1$s" ',  $orderNo);
    $work_order_form_data = executeQuery($work_order_form_data);
    $dataSequence['work_order_form_data'] = getDataFromResultSet($work_order_form_data);

    $work_order_ticket_without_cron = sprintf('SELECT 
        COUNT(DISTINCT c.number) AS TotalTickets, 
        COALESCE(SUM(CASE WHEN b.cron_date IS NOT NULL THEN 1 ELSE 0 END), 0) AS TicketsWithCron,
        COALESCE(SUM(CASE WHEN b.cron_date IS NULL THEN 1 ELSE 0 END), 0) AS TicketsWithoutCron
            FROM 
                manex_work_orders a
            LEFT JOIN 
                _wo_cron_logs b ON a.WONumber = b.wo_number
            INNER JOIN 
                sem_ticket c ON c.number = CONCAT("00", b.ticket_id)
            WHERE 
                a.WONumber = "%1$s" ',  $orderNo);
                
    $work_order_ticket_without_cron = executeQuery($work_order_ticket_without_cron);
    $dataSequence['work_order_ticket_without_cron'] = getDataFromResultSet($work_order_ticket_without_cron);

    return $dataSequence;

}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <style>
        /* Center the loader within the table */
      /* Loader Styles */
            .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Ensure it stays on top */
            }

            .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            }

            /* Animation for spinning effect */
            @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
            }

    </style>
</head>
  <body>
    
    <div class="row mt-5">
        <div class="col-md-12">
            <h3 style="text-align:center;text-decoration:underline;">Work Order States</h3>
        </div>
    </div>
    <hr />

    <!-- Loader Element -->
    <div id="loader" class="loader">
        <div class="spinner"></div>
    </div>


    <div class="row mt-5">
        <div class="col-md-12">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="wo-states">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">WO No</th>
                <th scope="col">WO Count</th>
                <th scope="col">WO Date</th>
                <th scope="col" width="100px">WO Doc</th>
                <th scope="col">Doc Date</th>
                <th scope="col">AllTickets</th>
                <th scope="col">WithCronTickets</th>
                <th scope="col">WithoutCronTickets</th>
                <th scope="col">Form Data</th>
                </tr>
            </thead>
            <tbody>
            <?php $monetringStates = showStates(); ?>
                <?php $a = 1; foreach ($monetringStates as $index => $states){ ?>

                    <tr>
                        <th scope="row"><?php echo $a++; ?></th>
                        <td><?php echo $states['work_order_no']; ?></td>
                        <td><?php echo $states['wo_count']; ?></td>
                        <td><?php echo $states['work_order_date']; ?></td>
                        <td><?php echo $states['work_order_document']; ?></td>
                        <td><?php echo $states['work_order_document_date']; ?></td>
                        <td><?php echo $states['work_order_all_ticket']; ?></td>
                        <td><?php echo $states['work_order_ticket_with_cron']; ?></td>
                        <td><?php echo $states['work_order_ticket_without_cron']; ?></td>
                        <td><?php echo $states['work_order_form_data_count']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>

    $(document).ready(function () {
        // Show the content and hide the loader after the page fully loads
        $("#loader").fadeOut(500, function () {
            $("#spinner").fadeIn(500);
        });

        // Show loader when the page is refreshed
        $(window).on('beforeunload', function () {
            $("#loader").fadeIn(500);
            $("#spinner").fadeOut(500);
        });
        $(document).ready(function() {
            $('#wo-states').DataTable();
        });
    });
    </script>






</body>
</html>


