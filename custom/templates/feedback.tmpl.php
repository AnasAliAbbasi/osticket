<link rel="stylesheet" href="../custom/css/form.css?ver=<?php echo AXEVERSION; ?>" />
<form action="" method="POST" enctype="multipart/form-data">
    <?php
    if (isValidArray($customdata)) {
        foreach ($customdata as $k => $v) {
    ?>
            <label><span><?php echo $v['title']; ?>:</span><input type="text" name="<?php echo $k; ?>" value="<?php echo $v['val']; ?>" /></label>
    <?php
        }
    }
    ?>
    <label><span>Subject: </span><input type="text" name="subject" value="" /></label>
    <label><span>Message: </span><textarea name="message"></textarea></label>
    <input type="hidden" value="yes" name="submitted" />
    <input type="submit" value="Submit" name="submit" />
</form>