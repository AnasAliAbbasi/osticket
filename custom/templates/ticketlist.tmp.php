<link rel="stylesheet" href="../custom/css/attachments.css?ver=<?php echo AXEVERSION; ?>" />
<div class="axeaticketlist">
    <?php
    if (isValidArray($ticket_list)) {
    ?>
        <div class="thread-body">
            <div class="ticketlist">
                <?php
                foreach ($ticket_list as $ticket_item) {
                    $attach_link = $base . 'ku';
                ?>
                    <span class="ticketlist-info">
                        <a class="no-pjax truncate filename" 
                            href="<?php echo $attach_link; ?>" 
                            target="_blank">
                            <?php echo "[ Auto ". $ticket_item['ticket_id']. " - " .$ticket_item['topic_id']  . "-". $ticket_item['wo_number'] . " " . $ticket_item['part_no'] . " " . $ticket_item['revision'] . " ]" ?>
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
        <div class="pull-left">This work order not have any tickets</div>

        <div class="clear"></div>
    <?php
    } ?>
</div>