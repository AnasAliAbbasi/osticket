<link rel="stylesheet" href="../custom/css/attachments.css?ver=<?php echo AXEVERSION; ?>" />
<div class="axeaticketlist">
    
    <?php

    $settings['topicId'] = array(
        15 => 'MPI Ticket', /* MPI Ticket */
        20 => ' WO Form', /* WO Form */
        21 => 'Technical Review', /* Technical Review  */
        22 => 'Material Review Consigned', /* Material Review Consigned*/
        26 => 'Material Review Turnkey', /* Material Review Turnkey*/
        23 => 'Test Flag', /* Test Flag */
        24 => 'CS Planning', /* CS Planning */
        25 => 'BOM Load', /* BOM Load */
    );

    if (isValidArray($ticket_list)) {
    ?>
    <div class="thread-header">
    </div>
        <div class="thread-body">
            
            <h3 style="    text-align: center;
                font-weight: bold;
                margin-bottom: 25px;
                text-decoration: underline;
                color: gray;">Related Ticket List</h3>
            <div class="ticketlist">
            <table class="ticket_info custom-data" cellspacing="0" cellpadding="0" width="940" border="0">
                <thead>
                    <th colspan="">Ticket No</th>
                    <th colspan="">Last Updte</th>
                    <th colspan="">Subject</th>
                    <th colspan="">Status</th>
                    <th colspan="">From</th>
                    <th colspan="">Assign</th>
                    
                </thead>
                <tbody>
                <?php
                    $a = 1;
                    foreach ($ticket_list as $ticket_item) {
                        $a++;
                        $attach_link = $base . '?id='.$ticket_item['ticket_no'];
                        $topic_name = $settings['topicId'][$ticket_item['topic_id']];
                    ?>
                    <tr>
                        <td> <a href="<?php echo $attach_link; ?>" target="_blank"> <?php echo $ticket_item['number'] ?> </a>  </td>
                        <td><?php echo $ticket_item['updated'] ?> </td>
                        <td>
                            <a href="<?php echo $attach_link; ?>" target="_blank">
                              <?php echo "[ Auto ".  $topic_name  . "-". $ticket_item['wo_number'] . " " . $ticket_item['part_no'] . " " . $ticket_item['revision'] . " ]" ?>
                            </a>
                        </td>
                        <td> <?php echo $ticket_item['name']; ?> </td>
                        <td> <?php echo $ticket_item['holder_name']; ?> </td>
                        <td> <?php echo $ticket_item['assignee']; ?> </td>
                        
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
          
            
            </div>
        </div>
        <?php } else {
        $reload = md5('noattachmentfound' . time());
    ?>
        <div class="pull-left">This work order no more related ticket</div>

        <div class="clear"></div>
    <?php
    } ?>
</div>