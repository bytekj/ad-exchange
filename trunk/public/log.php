<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 * input params
 * 
 * 1)connection time
 * 2) client ip
 * 3) ssid
 * 4) content name
 * 5) adid
 */
require_once '../include/global.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/Log.php';
require_once '../Classes/Ad.php';
require_once '../Classes/Utils.php';

if ($_GET['id']) {
    $id = $_GET['id'];
} else {
    $id = 0;
}
$played_timestamp = $_GET['t'];
$program_flag = $_GET['f'];
$client_id = $_GET['cid'];
$ssid = $_GET['ssid'];

$objLog = new Log();
$objLog->id = $id;

$objLog->ad_played_timestamp = $played_timestamp;
$objLog->program_flag = $program_flag;
$objLog->client_id = $client_id;
$objLog->ssid = $ssid;
$objLog->update();
//$objLog->updateContentLog();

echo "1";
?>
