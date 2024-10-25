<?php
$settings = array(
  'dbHost' => '10.1.13.18',
  'dbTable' => 'sem_pmo.sem_faq', // Database.Table where FAQs are stored.
  'database' => 'sem_pmo',
  'dbUser' => 'sem_user',
  'dbPass' => 'sem123',
  'categoryId' => 5, // The Category ID where Automator tickete FAQs are kept
  'topicId' => 18, // Created tickets are assigned to this topic.
  'subjectPrefix' => '[',
  'subjectSuffix' => ']',
  'reporterEmail' => 'pmo@sem-inc.com',
  'reporterName' => 'Ahmar',
  'apiURL' => 'http://10.1.13.18/sem-pmo/api/http.php/tickets.json',
  'apiKey' => 'FA752310B7687E90F1347AF792412584'
);

define('AXEVERSION', '1.0.1');