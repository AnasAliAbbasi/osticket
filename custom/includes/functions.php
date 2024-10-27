<?php
include_once dirname(__FILE__) . '/settings.php';

function createTicket($subject, $message = null, $topicID = null, $additional_data = null)
{
    global $settings;

    $topicId = $topicID ? $topicID : $settings['topicId'];
    $reporterEmail = $settings['reporterEmail'];
    $reporterName = $settings['reporterName'];
    $reporterIP = gethostbyname(gethostname());

    if (empty($subject)) {
        echo ("No Subject provided. Not creating ticket.\n");
        return false;
    };

    if (empty($message)) {
        $message = $subject;
    }

    $subject = $settings['subjectPrefix'] . $subject . $settings['subjectSuffix'];

    $data = array(
        'name'      =>      $reporterName,
        'email'     =>      $reporterEmail,
        'phone'   =>      '',
        'subject'   =>      $subject,
        'message'   =>      $message,
        'ip'        =>      $reporterIP,
        'topicId'   =>      $topicId
    );

    if (isValidArray($additional_data)) {
        $data = array_merge($data, $additional_data);
    }

    function_exists('curl_version') or die('CURL support required');
    function_exists('json_encode') or die('JSON support required');

    set_time_limit(120);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $settings['apiURL']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.8');
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'X-API-Key: ' . $settings['apiKey']));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code != 201) {
        echo "Unable to create ticket with subject $subject: " . $result . "\n";
        return false;
    }

    $ticketId = (int)$result;

    echo "Ticket '$subject' created with id $ticketId\n";

    return $ticketId;
}

function sanitizeString($string)
{
    return decode(br2nl($string));
}

function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

function decode($s)
{
    if (empty($s)) {
        return $s;
    }

    $s = str_ireplace("&nbsp;", " ", $s);
    return trim($s);
}

function axeDump($val)
{
    echo '<pre>';
    var_dump($val);
    echo '</pre>';
}

function isValidArray($arr)
{
    $res = false;
    if (is_array($arr)) {
        if (sizeof($arr)) {
            $res = true;
        }
    }
    return $res;
}

function getFormVals($key, $type)
{
    $val = '';
    if ($type == 'get') {
        $val = getGetVals($key);
    } else if ($type == 'post') {
        $val = getPostVals($key);
    }

    return $val;
}

function getGetVals($key)
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : '';
}

function getPostVals($key)
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

function executeQuery($query)
{
    $link = getConnection();
    $sql_result = mysqli_query($link, $query);
    return $sql_result;
}

function getDataFromResultSet($sql_result)
{
    $rowsFound = false;
    $results = array();
    while ($row = mysqli_fetch_assoc($sql_result)) {
        $rowsFound = true;
        $results[] = $row;
    }
    $results = $rowsFound === false ? null : $results;
    return $results;
}

function getConnection()
{
    global $settings;
    $link = mysqli_connect($settings['dbHost'], $settings['dbUser'], $settings['dbPass'],  $settings['database']);
    if (!$link) {
        die('Could not connect.');
    } else {
        return $link;
    }
}

function closeConnection($link)
{
    mysqli_close($link);
}
