<?php 
/*
IMPORTANT NOTE - 
$SECRET = Specific string which should need to be same as on Production servers
$PATH = This is a absolute path of Staging server directory which you want to sync with production server. (/home/######/public_html/staging)
*/

date_default_timezone_set("Asia/Calcutta");
require_once 'AbstractSync.php';
require_once 'Staging.php';

const SECRET = '5ecR3t'; //make this long and complicated
const PATH = '/home/######/public_html/staging'; //sync all files and folders below this path

$server = new \Outlandish\Sync\Server(SECRET, PATH);
$server->run(); //process the request
  
