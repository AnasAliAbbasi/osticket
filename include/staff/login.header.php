<?php
defined('OSTSCPINC') or die('Invalid path');
header("Content-Security-Policy: frame-ancestors ".$cfg->getAllowIframes()."; script-src 'self' 'unsafe-inline'; object-src 'none'");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <!-- Powered by osTicket -->
    <!-- Supercharged by osTicket Awesome -->	
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="refresh" content="7200" />
    <title>osTicket :: <?php echo __('Agent Login'); ?></title>
    <link rel="stylesheet" href="css/login.css" type="text/css" />
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?0375576"/>
    <meta name="robots" content="noindex" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--osta-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-3.7.0.min.js?0375576"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("input:not(.dp):visible:enabled:first").focus();
         });
    </script>
        <!--osta-->
	<?php include ROOT_DIR . '/osta/inc/staff-head.html'; ?>    
</head>
<body id="loginBody">

