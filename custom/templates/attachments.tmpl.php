<link rel="stylesheet" href="../custom/css/attachments.css?ver=<?php echo AXEVERSION; ?>" />
<div class="axeattachments">
    <?php
    $currenturl = substr_count($_SERVER['REQUEST_URI'], '&') ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '&')) : $_SERVER['REQUEST_URI'];
    if (isValidArray($attachments)) {
    ?>
        <div class="thread-body">
            <div class="attachments">
                <?php
                $count = sizeof($attachments);
                $gmnow =  Misc::gmtime();
                $expires = $gmnow + 86400 - ($gmnow % 86400);
                $base = str_replace(array('\\', 'custom/templates'), array('/', ''), substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT'])));
                $prev_obj_id = 0;
                foreach ($attachments as $attachment) {
                    $name = $attachment['name'];
                    $fid = $attachment['fid'];
                    $id = $attachment['id'];
                    $key = strtolower($attachment['key']);
                    $hash = $attachment['signature'];
                    $signature = AttachmentFile::_genUrlSignature($fid, $key, $hash, $expires);
                    $attach_link = $base . sprintf('file.php?key=%1$s&expires=%4$d&signature=%2$s&id=%3$d', $key, $signature, $id, $expires);
                    $size = Format::file_size($attachment['size']);
                    $obj_id = $attachment['object_id'];
                    $created = Format::datetime(strtotime($attachment['created']));
                    $reload = md5($name . time());
                    if ($prev_obj_id !== $obj_id) {
                ?>
                        <div class="attachthreadtitle clear"><small class="faded attcreated"><strong>From Thread: <a class="no-pjax" title="Thread #<?php echo $obj_id; ?>" href="<?php echo $currenturl; ?>&rel=<?php echo $reload; ?>#entry-<?php echo $obj_id; ?>">#<?php echo $obj_id; ?></a> | Created On: <?php echo $created; ?></strong></small></div>
                    <?php
                        $prev_obj_id = $obj_id;
                    } ?>

                    <span class="attachment-info">
                        <i class="icon-paperclip icon-flip-horizontal"></i>
                        <a class="no-pjax truncate filename" href="<?php echo $attach_link; ?>" download="Test 5.txt" target="_blank" title="Download <?php echo $name; ?>"><?php echo $name; ?></a><small class="filesize faded"><?php echo $size; ?></small></span>
                <?php
                }
                ?>
                <script>
                    document.getElementById('axeattachcount').innerText = '<?php echo $count; ?>';
                </script>
            </div>
        </div>
    <?php } else {
        $reload = md5('noattachmentfound' . time());
    ?>
        <div class="pull-left">This ticket does not have any attachments</div>
        <div class="pull-right">
            <a class="no-pjax green button action-button" href="<?php echo $currenturl; ?>&rel=<?php echo $reload; ?>#reply">
                <i class="icon-plus-sign"></i> Add New Attachment</a>
        </div>
        <div class="clear"></div>
    <?php
    } ?>
</div>