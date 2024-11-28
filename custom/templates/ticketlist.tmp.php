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
            
      
            <div class="ticketlist">
            <table class="list queue tickets font-reg" id="relatedticketlist" cellspacing="1" cellpadding="2" width="1200" border="0">
                <thead>
                    <tr>
                    <th class="osta_ticket" style="width: 12px; font-size: 14px;">#</th>
              
                    <th class="osta_lastupdated" style="width: 100px; font-size: 14px;text-align:center;">Last Update</th>
                    <th class="osta_subject" style="width: 300px; font-size: 14px;">Subject</th>
                    <th class="osta_username" style="width: 100px; font-size: 14px;">Status</th>
                    <th class="osta_username" style="width: 100px; font-size: 14px;">From</th>
                    <th class="osta_assignee" style="width: 100px; font-size: 14px;">Assign</th>
                    
            </tr>
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
                       
                        <td> 
                            
                            <a href="<?php echo $attach_link; ?>" target="_blank">
                                <?php echo $ticket_item['number'] ?> 
                            </a>
                        
                           
                             </td>
                        <td style="text-align:center"><?php echo $ticket_item['updated'] ?> </td>
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

<script>
$(document).ready(function() {
  $('#relatedticketlist').DataTable();
});

</script>