<?php

require_once '../include/global.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/Log.php';
require_once '../Classes/Ad.php';
require_once '../Classes/Utils.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//echo "<pre>";
//print_r($_GET);
//echo "</pre>";
/*
 * 
 * Array
  (
  [proto] => http
  [cid] => 102
  [ip] => 127.0.0.1
  [chname] => b_buck720_98secs.mp4
  [t] => 10202012
  [dev] => android
  [path] => /home/lilesh/Videos/samples/
  [pubid] => 12345
  [ssid] => 987
  )
 * 
 */

$objAd = new Ad();
$adResult = $objAd->getAd();

$adFreq = $objAd->getAdFreq();

$preroll = $objAd->getPrerollOption();

$interval = $adFreq * 60000;

if($preroll == 0){
    $i=1;
}
else{
    $i=0;
}
$limit = 10;
if($_GET['n']){
    $limit = $_GET['n'] ;
}



$result = "";
$timestamp = 0;
$objLog = new Log();

if ($adResult) {
    $result['freq'] = $adFreq;
    $result['c'] = 10;
    
    for (; $i < $limit ; $i++) {
        
        //echo $i;
        $index = rand(0, sizeof($adResult)-1);
        
        
        $objLog->ad_played_timestamp = 0;
        $objLog->ad_served_timestamp = microtime();
        $objLog->campaign_id = $adResult[$index]['campaign_id'];
        $objLog->client_hit_timestamp = $_GET['t'];
        $objLog->client_ip = $_GET['ip'];
        $objLog->content_id = $adResult[$index]['content_id'];
        //$objLog->device = $_GET['dev'];

        $objLog->device = Utils::getDevice($_GET['ua']);
        $objLog->encoded_ad_name = $adResult[$index]['filename'];
        $objLog->protocol = $_GET['proto'];
        $objLog->req_filename = $_GET['chname'];
        $objLog->ssid = $_GET['ssid'];
        $objLog->program_flag = $_GET['f'];
        $objLog->client_id = $_GET['cid'];
        $objLog->cpm = $adResult[$index]['cpm'];

        $id = $objLog->add();
        
        $ad['f'] = $adResult[$index]['filename'];
        $ad['t'] = $timestamp + ($interval * $i);
        $ad['id'] = $id;


        $result['ads'][] = $ad;
    
    }
    $objLog->updateContentLog();
    
    
    /*
      `campaign_id`,
      `content_id`,
      `encoded_ad_name`,
      `client_ip`,
      `protocol`,
      `req_filename`,
      `client_hit_timestamp`,
      `device`,
      `ad_served_timestamp`,
      `ad_played_timestamp`
     */
} else {
    $result['c'] = 0;
}

echo json_encode($result);
?>
