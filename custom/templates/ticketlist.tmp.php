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
            
            <h3 style="padding-left: 105px;
                margin-top: -10px;
                color: gray;
                font-family: sans-serif;
                text-align: left;
                text-decoration: underline;">Related Ticket List</h3>
            <div class="ticketlist">
                <?php
                $a = 1;
                foreach ($ticket_list as $ticket_item) {
                    $a++;
                    $attach_link = $base . '?id='.$ticket_item['ticket_no'];
                    $topic_name = $settings['topicId'][$ticket_item['topic_id']];
                ?>
                    <span class="ticketlist-info">
                    <a class="no-pjax truncate" style="text-decoration: none;" 
                            href="<?php echo $attach_link; ?>" 
                            target="_blank">
                            <?php echo "[ Auto ".  $topic_name  . "-". $ticket_item['wo_number'] . " " . $ticket_item['part_no'] . " " . $ticket_item['revision'] . " ]" ?>
                        </a>
                    </span>
                <?php
                }
                ?>
            
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