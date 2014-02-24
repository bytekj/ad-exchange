<?php

require_once '../include/global.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/Log.php';
require_once '../Classes/Ad.php';
require_once '../Classes/Utils.php';
require_once '../Classes/Content.php';
require_once '../Classes/UserInfo.php';
require_once '../Classes/Campaign.php';
require_once '../Classes/Reports.php';
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
/*
$data = $_GET;

$data = explode("&", decrypt(KEY, $str));

$request = array();
foreach ($data as $datum){
	$req_param = explode("=", $datum);
	$request[$req_param[0]] = $req_param[1];
}

*/
$request = $_GET;

$objAd = new Ad();

$adResult = $objAd->getAd($request);

$adFreq = $objAd->getAdFreq($request);

$preroll = $objAd->getPrerollOption($request);

$interval = $adFreq * 60000;


// default limit of number of ads is 10
$limit = 10;
if ($request['n']) {
    $limit = $request['n'];
}

//default offset of the client's ad count is 0
$offset = 0;
if (isset($request['o'])) {
    $offset = $request['o'];
}


// modify i only if client is starting for the first time next time offset will not be 0
if ($preroll == 0) {
    $i = 1;
} else {
    $i = 0;
}
if ($_GET['debug']) {
    echo "<br>starting with " . $i;
}

$result = "";

$timestamp = ($offset) * $interval;
$objLog = new Log();

if ($adResult) {
    $result['freq'] = $adFreq;
    $result['c'] = $limit;

    for ($t=0; $t< $limit; $i++, $t++) {

        //echo $i;
        $index = rand(0, sizeof($adResult) - 1);


        $objLog->ad_played_timestamp = 0;
        $objLog->ad_served_timestamp = microtime();
        $objLog->campaign_id = $adResult[$index]['campaign_id'];
        $objLog->client_hit_timestamp = $request['t'];
        $objLog->client_ip = $request['ip'];
        $objLog->content_id = $adResult[$index]['content_id'];
        //$objLog->device = $_GET['dev'];

        $objLog->device = Utils::getDeviceType($request['ua']);
        $objLog->encoded_ad_name = $adResult[$index]['filename'];
        $objLog->protocol = $request['proto'];
        $objLog->req_filename = $request['chname'];
        $objLog->ssid = $request['ssid'];
        $objLog->program_flag = $request['f'];
        $objLog->client_id = $request['cid'];
        $objLog->cpm = $adResult[$index]['cpm'];
        $objLog->ua = $request['ua'];

        $id = $objLog->add();

        $ad['f'] = $adResult[$index]['filename'];
        $ad['t'] = $timestamp + ($interval * $i);
        $ad['id'] = $id;


        $result['ads'][] = $ad;
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
    $result['c'] = 0;
}
//INSERT INTO `adsparx`.`debug` (`id`, `timestamp`, `text`) VALUES (NULL, CURRENT_TIMESTAMP, 'test');
echo json_encode($result);
?>
