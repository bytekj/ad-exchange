<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../include/global.php';
require_once '../Db/DataHandler.php';

$no = $_GET['c'];
$campaignId = $_GET['id'];


$cities = array("CHENNAI","MUMBAI","BANGALORE","KOLKATA","HUBLI","AHMEDABAD","CALCUTTA","HYDERABAD","NEW DELHI","DELHI","AMRAVATI","VADODARA","AMRITSAR","GUNTUR","SIHOR","GULBARGA");


$platforms = array("iPhone","OSX Desktop","Android Phone","Android tab","Blackberry","J2ME","Windows Phone","Windows Desktop");



//for ($i=0; $i<$no; $i++){
//    //$genre = $genres[rand(0,sizeof($genres)-1)];
//    //$platform = $platforms[rand(0,sizeof($platforms)-1)];
//    //$region  = $regions[rand(0,sizeof($regions)-1)];
//    $city  = $cities[rand(0,sizeof($cities)-1)];
//    //$sql = "INSERT INTO `adex`.`log_users` (`campain_id`, `region`, `platform`, `city`, `genre`) 
//    //    VALUES ('".$campaignId."', '".$region."', '".$platform."', '".$city."', '".$genre."');";
//    $objData = new DataHandler();
//    $objData->PutQuery($sql);
//    //echo "inserted (".$campaignId.",".$region.",".$platform.",".$city.",".$genre.")<br>";
//    //flush();
//}


for ($i=0; $i<$no; $i++){
    $city  = $cities[rand(0,sizeof($cities)-1)];
    $platform = $platforms[rand(0,sizeof($platforms)-1)];
    $objData = new DataHandler();
    $timestamp = "select from_unixtime(
       unix_timestamp('2012-04-14 01:00:00')+floor(rand()*2592000)) as time";
    $time = $objData->GetQuery($timestamp);
    
    echo "<br>".$sql = "INSERT INTO `bi_users`(`id`,`campaign_id`, `country`, `city`, `platform`, `client_hit_time`) values(NULL,'".$campaignId."','INDIA','".$city."','".$platform."','".$time[0]['time']."' )";
    
    $objData->PutQuery($sql);
}

?>
