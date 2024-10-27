<!-- !/usr/bin/php -q -->
<?php
/*
The MIT License (MIT)

Copyright (c) 2015 Gary Smart www.smart-itc.com.au

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

Parts of this script were inspired from jared@osTicket.com / ntozier@osTicket / tmib.net (http://tmib.net/using-osticket-1812-api)
*/

$settings = array(
  'dbHost' => 'localhost',
  'dbTable' => 'sem_pmo.sem_faq', // Database.Table where FAQs are stored.
  'database' => 'sem_pmo',
  'dbUser' => 'root',
  'dbPass' => 'anas1234',
  'categoryId' => 5, // The Category ID where Automator tickete FAQs are kept
  'topicId' => 18, // Created tickets are assigned to this topic.
  'subjectPrefix' => '[',
  'subjectSuffix' => ']',
  'reporterEmail' => 'pmo@sem-inc.com',
  'reporterName' => 'Ahmar',
  'apiURL' => 'http://127.0.0.1/osticket/api/http.php/tickets.json',
  'apiKey' => '65417B8798F62B5697D13FC54C593414'
);

if (!isset($settings)) {
  die('$settings is not set. Aborting.');
}

$period = isset($argv[1]) ? $argv[1] : "daily";

$tks = findTicketsToCreate($period);

/* foreach ($tks as $t) {
  createTicket($t->subject, $t->message);
} */



// $period to match FAQ Question field.
function findTicketsToCreate($period = "daily")
{
  global $settings;

  $link = mysqli_connect($settings['dbHost'], $settings['dbUser'], $settings['dbPass'],  $settings['database']);

  if (!$link) {
    die('Could not connect.');
  } else {
    echo 'Link is found!!';
  }

  //$sql_query = "select faq_id, answer from " . $settings['dbTable'] . " WHERE category_id=" . $settings['categoryId'] . " AND UPPER(question) LIKE UPPER('%$period%')";

  /* $sql_query = "select sem_ticket_info.topic answer ,sem_ticket_info.subject,sem_ticket_info.title,sem_ticket_info.body, `r_tkt_date`.`ticket_id` AS `ticket_id`,`r_tkt_date`.`ticket_number` AS `ticket_number`,`r_tkt_date`.`topic_id` AS `topic_id`,`r_tkt_date`.`topic` AS `topic`,`r_tkt_date`.`Next_Due_Date` AS `Next_Due_Date`,`r_tkt_interval`.`Interval` AS `interval`
from 
    (select distinct `a`.`object_id` AS `ticket_id`,`d`.`number` AS `ticket_number`,`d`.`topic_id` AS `topic_id`,`f`.`topic` AS `topic`,`b`.`value` AS `Next_Due_Date` 
     from `sem_pmo`.`sem_form_entry` `a` join `sem_pmo`.`sem_form_entry_values` `b` join `sem_pmo`.`sem_form_field` `c` join `sem_pmo`.`sem_ticket` `d` join `sem_pmo`.`sem_form` `e` join `sem_pmo`.`sem_help_topic` `f`
     where `a`.`form_id` = 12 and `a`.`id` = `b`.`entry_id` and `b`.`field_id` = `c`.`id` and `c`.`form_id` = `a`.`form_id` and `c`.`label` = 'Next Due Date' and cast(`b`.`value` as date) = curdate() and `a`.`object_type` = 'T' and `a`.`object_id` = `d`.`ticket_id` and `e`.`id` = `a`.`form_id` and `d`.`topic_id` = `f`.`topic_id`) `r_tkt_date` 
     join (select distinct `a`.`object_id` AS `ticket_id`,`d`.`number` AS `ticket_number`,`d`.`topic_id` AS `topic_id`,`f`.`topic` AS `topic`,`b`.`value` AS `Interval` 
           from `sem_pmo`.`sem_form_entry` `a` join `sem_pmo`.`sem_form_entry_values` `b` join `sem_pmo`.`sem_form_field` `c` join `sem_pmo`.`sem_ticket` `d` join `sem_pmo`.`sem_form` `e` join `sem_pmo`.`sem_help_topic` `f` where `a`.`form_id` = 12 and `a`.`id` = `b`.`entry_id` and `b`.`field_id` = `c`.`id` and `c`.`form_id` = `a`.`form_id` and `c`.`label` = 'Interval' and `b`.`value` <> 1 and `a`.`object_type` = 'T' and `a`.`object_id` = `d`.`ticket_id` and `e`.`id` = `a`.`form_id` and `d`.`topic_id` = `f`.`topic_id`) `r_tkt_interval`
    join (select distinct `a`.`object_id` AS `ticket_id`,`d`.`number` AS `ticket_number`,`d`.`topic_id` AS `topic_id`,`f`.`topic` AS `topic`,`b`.`value` AS `Recurring Ticket` from `sem_pmo`.`sem_form_entry` `a` join `sem_pmo`.`sem_form_entry_values` `b` join `sem_pmo`.`sem_form_field` `c` join `sem_pmo`.`sem_ticket` `d` join `sem_pmo`.`sem_form` `e` join `sem_pmo`.`sem_help_topic` `f` where `a`.`form_id` = 12 and `a`.`id` = `b`.`entry_id` and `b`.`field_id` = `c`.`id` and `c`.`form_id` = `a`.`form_id` and `c`.`label` = 'Recurring Ticket' and `b`.`value` = 1 and `a`.`object_type` = 'T' and `a`.`object_id` = `d`.`ticket_id` and `e`.`id` = `a`.`form_id` and `d`.`topic_id` = `f`.`topic_id`) `r_tkt`
    join sem_ticket_info
where `r_tkt`.`ticket_id` = `r_tkt_date`.`ticket_id` and `r_tkt_interval`.`ticket_id` = `r_tkt_date`.`ticket_id`
and sem_ticket_info.ticket_id=`r_tkt_date`.`ticket_id`"; */

  /*$sql_query = 'SELECT * FROM `sem_ticket_info`';*/
  $sql_query = 'SELECT semti.*,vrtkt.* FROM `sem_ticket_info` semti left join `vw_recurring_tkt` vrtkt on semti.ticket_id = vrtkt.ticket_id';

  $sql_result = mysqli_query($link, $sql_query);
  $rowsFound = false;
  $results = array();

  while ($row = mysqli_fetch_assoc($sql_result)) {
    $rowsFound = true;
    $newdate = date('Y-m-d H:i:s', strtotime($row['Next_Due_Date'] . ' +' . intval($row['interval']) . ' day'));
    $adata = array('staffId' => intval($row['Assignee']), '_is_recurring_tkt' => 'true', '_nxt_due_date' => $newdate, '_interval' => $row['interval']);
    /*   axeDump($newdate, $row);
    exit(); */
    $newticket_id = createTicket($row['subject'], sanitizeString($row['body'] . '<br>Ticket URL: ' . $row['ticket_url']), $row['topic_id'], $adata);

    /* $lines = explode("\n", trim(br2nl($row['answer']))) ;
    
      foreach($lines as $line) {

        $line = trim($line);
   
        // Skip empty lines or lines starting with a comment (-- or #) 
        if (empty($line) || (strpos($line, "--") === 0) || (strpos($line, "#") === 0)) {
          continue;
        }

        $subject = $row['subject'];
        $message = $row['body'];

        if (strpos($line, "|") !== FALSE) {
          list($subject, $message) = explode("|", $line);
        }

        $subject = decode($subject);
        $message = decode($message);

        if (empty($message)) {
          $message = $subject;
        }

        $tmp = new stdClass();
        $tmp->faqId = $row['faq_id'];
        $tmp->subject = $subject;
        $tmp->message = $message . "\n\nPeriod: $period";
        $results[] = $tmp;
      } */
  }

  mysqli_free_result($sql_result);
  mysqli_close($link);
  /*  exit(); */
  if (!$rowsFound) {
    $msg = "Automator called for Period '$period' but found no tickets to create";
    echo $msg . "\n";
    /* createTicket($msg); */
  }

  return $results;
} // findTicketsToCreate()


function createTicket($subject, $message = null, $topicID = null, $additionaldata = null)
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

  if ($additionaldata) {
    if (is_array($additionaldata)) {
      $data = array_merge($data, $additionaldata);
    }
  }

  function_exists('curl_version') or die('CURL support required');
  function_exists('json_encode') or die('JSON support required');

  set_time_limit(30);

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
} // createTicket()

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
