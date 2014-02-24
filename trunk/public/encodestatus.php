<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../Db/DataHandler.php';
require_once '../include/global.php';
require_once '../Classes/Utils.php';
require_once '../Classes/Ad.php';

$filename = $_GET['fname'];

$sql = "update encoded_ad set encode_status=1 where encoded_filename like '".$filename."'";
$objData = new DataHandler();
$objData->PutQuery($sql);

$arrStreamingSever = Ad::getContentStreamingServerByEncodedAdName($filename);

$req = "http://www.google.com";
//echo "<pre>";
//print_r($arrStreamingSever);
//echo "</pre>";
//port 60000
$req = "/newad?fname=".$filename."&f=0";
foreach ($arrStreamingSever as $streamingServer){
    
    echo "http://".$streamingServer['ssip'].$req;
    //$res = Utils::http("46.");
}


?>
