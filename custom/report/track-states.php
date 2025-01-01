<?php

include_once '../includes/functions.php';

$days = 3;
if(isset($_POST['days'])) {
    $days = $_POST['days'];
}

function showStates($days)
{
    list($moneteringData) = getStates($days);
    return $moneteringData;
}

function getStates($days)
{
    $moneteringData = [];
    $totalWorkOrder = getDisticntWorkOrders($days);
    $distinctOrder = makeDistinctWO($totalWorkOrder);

    if(!empty($distinctOrder)){
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
                $monetringChunk['work_order_document'] = 'Yes';
                $monetringChunk['work_order_document_date'] = $response['work_order_docs'][0]['UpdatedUTC'];
                
            }else{
                $monetringChunk['work_order_document'] = 'No';
                $monetringChunk['work_order_document_date'] = '00-00-00';
    
            }
    
            if(!empty($response['work_order_form_data'])){
                $monetringChunk['work_order_form_data_count'] = $response['work_order_form_data'][0]['form_data_count'];
            }else{
                $monetringChunk['work_order_form_data_count'] = 0;
            }
    
            if(!empty($response['work_order_and_other_ticket_count'])){
                $monetringChunk['work_order_ticket_count'] = $response['work_order_and_other_ticket_count'][0]['wo_ticket_count'];
                $monetringChunk['other_ticket_count'] = $response['work_order_and_other_ticket_count'][0]['other_wo_ticket_count'];
                
            }else{
                $monetringChunk['work_order_all_ticket'] = 0;
                $monetringChunk['work_order_ticket_with_cron'] = 0;
                $monetringChunk['work_order_ticket_without_cron'] = 0;
                
            }
    
            array_push($moneteringData , $monetringChunk);
        }
    }


    return array($moneteringData);
}


function makeDistinctWO ($totalWorkOrder) {
    $distinctOrder = array();
    if(!empty($totalWorkOrder)){
        foreach ($totalWorkOrder as $key => $value) {
            $distinctOrder[] = $value['WONumber'];
        }
    }
   
    return array_unique($distinctOrder);
}

function getDisticntWorkOrders($days)
{  
    $today = new DateTime(date('Y-m-d')); 
    // $today = new DateTime('2024-12-12'); 
    $today->modify('-'.$days.' days');
    $previousDateMinusSevenDays = $today->format('Y-m-d');  

    $query = sprintf('select a.`WONumber` as WONumber
                from manex_work_orders a 
                where DATE(a.`WorkOrderDate`) > "%1$s" and WONumber > 17959

                UNION ALL

                select b.WONumber as WONumber
                from manex_work_order_documents b 
                where DATE(b.`UpdatedUTC`) > "%1$s" and b.WONumber > 17959

                UNION ALL

                select a.wo_number as WONumber
                from _wo_cron_logs a 
                where DATE(a.`cron_date`) > "%1$s" and a.wo_number > 17959

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
        COUNT(CASE WHEN a.topic_id = 20 THEN 1 END) AS wo_ticket_count,
        COUNT(CASE WHEN a.topic_id != 20 THEN 1 END) AS other_wo_ticket_count
        FROM _wo_cron_logs a
        WHERE a.wo_number = "%1$s"',  $orderNo);
                
    $work_order_ticket_without_cron = executeQuery($work_order_ticket_without_cron);
    $dataSequence['work_order_and_other_ticket_count'] = getDataFromResultSet($work_order_ticket_without_cron);

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
   

    <!-- Loader Element -->
    <div id="loader" class="loader">
        <div class="spinner"></div>
    </div>

    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-4">
            <h6>Filter</h6>
            <form action="track-states.php" method="post">
                <select name="days" id="" class="form-control">
                    <option value="7">7 Days</option>
                    <option value="6">6 Days</option>
                    <option value="5">5 Days</option>
                    <option value="4">4 Days</option>
                    <option value="3">3 Days</option>
                </select>

        
        </div>
        <div class="col-md-2 mt-4">
            <button style="margin-top: 3px;" type="submit" name="submit" class="btn btn-primary">Apply</button>
        </div>
        </form>
    </div>

    <div class="row mt-3 mb-3">
        <div class="col-md-12">
            <h3>Report Days : <?php echo $days.' Days'; ?></h3>
        </div>
    </div>

    <hr />
    <div class="row mt-5">
        <div class="col-md-12">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="wo-states">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">WO Number</th>
                <th scope="col">Manex WO Count</th>
                <th scope="col">Manex WO Date</th>
                <th scope="col">Log WO Ticket Count</th>
                <th scope="col">Manex WO Doc</th>
                <th scope="col">Manex WO Date</th>
                <th scope="col">Log WO Other Ticket Count</th>
               <th scope="col">WO Form Data Count</th>
                </tr>
            </thead>
            <tbody>
            <?php $monetringStates = showStates($days); ?>
                <?php $a = 1; if(!empty($monetringStates)){
                     foreach ($monetringStates as $index => $states){ ?>

                    <tr>
                        <th scope="row"><?php echo $a++; ?></th>
                        <td><?php echo $states['work_order_no']; ?></td>
                        <td><?php echo $states['wo_count']; ?></td>
                        <td><?php echo $states['work_order_date']; ?></td>
                        <td><?php echo $states['work_order_ticket_count']; ?></td>
                        <td><?php echo $states['work_order_document']; ?></td>
                        <td><?php echo $states['work_order_date']; ?></td>
                        <td><?php echo $states['other_ticket_count']; ?></td>
                        <td><?php echo $states['work_order_form_data_count']; ?></td>
                    </tr>
                <?php }
            }else{
                echo "<h3 style='color:red;'>NO Logs Found</h3>";
            } ?>
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
      
    });
    </script>






</body>
</html>


