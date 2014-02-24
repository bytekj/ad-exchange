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

$request = $_GET;

$objAd = new Ad();

$request['ua'] = $_SERVER['HTTP_USER_AGENT'];

$request['cid'] = rand(100000000, 9999999999);


$adResult = $objAd->getTag($request);

$adFreq = $objAd->getAdFreq($request);

$interval = $adFreq * 60000;


// default limit of number of ads is 1

if ($_GET['debug']) {
	echo "<br>starting with " . $i;
}

$result = "";


$objLog = new Log();

if ($adResult) {

  $index = rand(0, sizeof($adResult) - 1);

  $url = $adResult[$index]['vast_url'];
  $vast_xml = $adResult[$index]['vast_xml'];

  //vast_xml empty means the campaign was created using ad tag url
  if($vast_xml == ""){

    $xml = file_get_contents($url);
    $parsedXML = new SimpleXMLElement($xml, LIBXML_NOCDATA);

    $parsedXML->Ad->InLine->Creatives->Creative->Linear->MediaFiles->MediaFile[0] = 'http://54.243.237.61/adex/resource/ads/orig/'.$adResult[$index]['filename'];

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

    $result = $parsedXML->asXML();
  }

  // else condition for campaigns created using
  else{
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
    $result = $vast_xml;
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
    } 
    else {
     exit();
   }
//INSERT INTO `adsparx`.`debug` (`id`, `timestamp`, `text`) VALUES (NULL, CURRENT_TIMESTAMP, 'test');
   echo $result;

   ?>