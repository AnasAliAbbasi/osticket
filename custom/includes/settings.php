<?php
$settings = array(
  'dbHost' => 'localhost',
  'dbTable' => 'sem_pmo.sem_faq', // Database.Table where FAQs are stored.
  'database' => 'sem_pmo',
  'dbUser' => 'anas',
  'dbPass' => 'anas',
  'categoryId' => 5, // The Category ID where Automator tickete FAQs are kept
  'topicId' => 18, // Created tickets are assigned to this topic.
  'subjectPrefix' => '[',
  'subjectSuffix' => ']',
  'reporterEmail' => 'pmo@sem-inc.com',
  'reporterName' => 'Ahmar',
  'apiURL' => 'http://127.0.0.1/osticket/api/http.php/tickets.json',
  'apiKey' => '1C8C5DDD60233047718CCD69900C919F'
);

define('AXEVERSION', '1.0.1');