<?php

include_once '../includes/functions.php';

showStates();

function showStates()
{
    list($totalWorkOrder , $totalWorkOrderWithWithoutCron) = getStates();

    // Start table for $totalWorkOrder
    echo "<h2>Total Work Order</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Total Work Orders</th><th>Total Tickets Created </th><th>Total Ticket With Cron</th><th>Total Tickets Without Cron</th></tr>"; // Add table headers as needed

    // Loop through $totalWorkOrder and display each item in a row
    echo "<tr>";
    echo "<td>" . htmlspecialchars($totalWorkOrder[0]['TotalWorkOrders']) . "</td>";
    echo "<td>" . htmlspecialchars($totalWorkOrderWithWithoutCron[0]['TotalTickets']) . "</td>";
    echo "<td>" . htmlspecialchars($totalWorkOrderWithWithoutCron[0]['TicketsWithCron']) . "</td>";
    echo "<td>" . htmlspecialchars($totalWorkOrderWithWithoutCron[0]['TicketsWithoutCron']) . "</td>";
    echo "</tr>";

    echo "</table>";


    exit;
}

function getStates()
{

    $totalWorkOrder = getTotalWorkOrders();
    $totalWorkOrderWithWithoutCron = getTotalWorkOrderWithWithoutCron();
    return array($totalWorkOrder , $totalWorkOrderWithWithoutCron );
}


function getTotalWorkOrders()
{   
    $today = date('Y-m-d');
    $fields = 'COUNT(a.WONumber) AS TotalWorkOrders' ;
    $query = sprintf('SELECT %1$s FROM manex_work_orders a WHERE 
        a.WorkOrderDate = "%2$s"', $fields , $today);
    $result = executeQuery($query);
    return getDataFromResultSet($result);
}

function getTotalWorkOrderWithWithoutCron()
{   
    $today = date('Y-m-d');
    $fields = 'COUNT(DISTINCT c.number) AS TotalTickets, 
    SUM(CASE WHEN b.cron_date IS NOT NULL THEN 1 ELSE 0 END) AS TicketsWithCron,
    SUM(CASE WHEN b.cron_date IS NULL THEN 1 ELSE 0 END) AS TicketsWithoutCron' ;

    $query = sprintf('SELECT %1$s FROM manex_work_orders a LEFT JOIN 
    _wo_cron_logs b ON a.WONumber = b.wo_number INNER JOIN 
    sem_ticket c ON c.number = CONCAT("00", b.ticket_id) WHERE a.WorkOrderDate = "%2$s" ', $fields , $today);
    $result = executeQuery($query);
    return getDataFromResultSet($result);
}





