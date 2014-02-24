<?php

require_once '../include/global.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/Log.php';
require_once '../Classes/Ad.php';
require_once '../Classes/Campaign.php';
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

$duration = $_GET['d'];
$objAd = new Ad();

$adRes = $objAd->getAdIstreamAd();
//echo "here";


$adFreq = $objAd->getAdFreq();


$interval = $adFreq * 60000;

$i = 0;

$json = "";
$timestamp = 0;
$objLog = new Log();
$durs = array();
$adResult = array();
foreach ($adRes as $key => $ad) {
    $objCampaign = new Campaign();
    $campaignMinutes = $objCampaign->getiCampaignMinutes($adRes[$key]['campaign_id']);
    //echo "<br>campaignMinutes " . $campaignMinutes;
    $currentImpressions = $objLog->getImpressionsByCampaignId($adRes[$key]['campaign_id']);
    //echo "<br>".$currentImpressions;
    if ($currentImpressions == false) {
        $currentImpressions = 0;
    }
    //echo "<br>current impressions " . $currentImpressions;
    $adlength = $adRes[$key]['ad_length'];
    if ($campaignMinutes * 60 > $currentImpressions * $adlength) {
        //echo "possible ad.. " . $adRes[$key]['ad_length'];
        $adResult[] = $adRes[$key];
        $durs[] = $adRes[$key]['ad_length'];
    }
}
unset($ad);

if ($adResult) {
    $json['freq'] = $adFreq;
    /*
      echo "here?";
      echo "<pre>";
      print_r($durs);
      echo "</pre>";
      echo "<br>not priting durs";
      sort($durs);
     * 
     */
    if ($_GET['debug'] == 1) {
        echo "<pre>";
        print_r($adResult);
        echo "</pre>";
    }

    $finaldurs = array();
    if (min($durs > $duration)) {
        $json['c'] = 0;
    } else {
        $tmparray = array();
        while (sizeof($tmparray) < sizeof($durs)) {

            $index = rand(0, sizeof($durs) - 1);
            //echo "<br>index".$index;
            if (!in_array($durs[$index], $tmparray)) {
                if (array_sum($finaldurs) + $durs[$index] < $duration) {
                    //echo "<br>".$durs[$index];
                    $finaldurs[] = $durs[$index];
                }
                $tmparray[$index] = $durs[$index];
            }
        }
        /*
          echo "<pre>";
          print_r($finaldurs);
          echo "</pre>";
         * 
         */
        $json['c'] = sizeof($finaldurs);
    }

    for ($k = 0; $k < sizeof($adResult); $k++) {

        if (in_array($adResult[$k]['ad_length'], $finaldurs)) {
            
            $objLog->ad_played_timestamp = 0;
            $objLog->ad_served_timestamp = microtime();
            $objLog->campaign_id = $adResult[$k]['campaign_id'];
            $objLog->client_hit_timestamp = $_GET['t'];
            $objLog->client_ip = $_GET['ip'];
            $objLog->content_id = $adResult[$k]['content_id'];
            $objLog->device = $_GET['dev'];
            $objLog->encoded_ad_name = $adResult[$k]['filename'];
            $objLog->protocol = $_GET['proto'];
            $objLog->req_filename = $_GET['chname'];
            $objLog->ssid = $_GET['ssid'];
            $objLog->program_flag = $_GET['f'];
            $objLog->client_id = $_GET['cid'];
            $objLog->cpm = $adResult[$k]['cpm'];

            $id = $objLog->add();
            if($_GET['debug']==1){
            echo "<pre>";
            print_r($ad);
            echo "</pre>";
            }
            $ad['f'] = $adResult[$k]['filename'];
            $ad['t'] = $adResult[$k]['ad_length'];
            $ad['id'] = $id;


            $json['ads'][] = $ad;
        }
    }
    //$objLog->updateContentLog();


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
    $json['c'] = 0;
}

echo json_encode($json);
?>