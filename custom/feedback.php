<?php

include_once 'includes/functions.php';
$settings['topicId'] = 2; /* Feedback Form */
$issubmitted = isset($_POST['submitted']) ? true : false;

list($subject, $msg, $customdata) = setCustomData($issubmitted);
if ($issubmitted) {
    createTicket($subject, $msg, $settings['topicId'], $customdata);
} else {
    renderCustomForm($customdata);
}
function renderCustomForm($customdata)
{
    include_once 'templates/feedback.tmpl.php';
}

function setCustomData($submitted)
{
    $type = $submitted === true ? 'post' : 'get';
    $subject = '';
    $msg = '';
    $arr = array(
        'kw' => $type == 'post' ?  getFormVals('kw', $type) : array('title' => 'Keywords', 'val' => getFormVals('kw', $type)),
        'won' => $type == 'post' ?  getFormVals('won', $type) : array('title' => 'Work Order Number', 'val' => getFormVals('won', $type)),
        'pn' => $type == 'post' ?  getFormVals('pn', $type) : array('title' => 'Part Number', 'val' => getFormVals('pn', $type)),
        'an' => $type == 'post' ?  getFormVals('an', $type) : array('title' => 'Assembly Number', 'val' => getFormVals('an', $type)),
        'rr' => $type == 'post' ?  getFormVals('rr', $type) : array('title' => 'Revision Reference', 'val' => getFormVals('rr', $type)),
    );

    if ($type === 'post') {
        $subject = getFormVals('subject', $type);
        $msg = getFormVals('message', $type);
    }
    return array($subject, $msg, $arr);
}
