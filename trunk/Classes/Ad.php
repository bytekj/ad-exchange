<?php

/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/

/**
 * Description of Ad
 *
 * @author kiran
 */
class Ad {

	//put your code here
	var $campaignId;
	var $filename;
	var $filepath;
	var $filetype;
	var $status;
	var $userdir;
	var $memcached;
	var $vast_tag;

	public function __construct() {
		$this->memcached = new Memcached;
		$this->memcached->addServer('localhost', 11211);
		$this->memcached->setOption(Memcached::OPT_CONNECT_TIMEOUT, 1000);
	}

	private function get($key){
		return $this->memcached->get($key);
	}

	private function set($key, $value){
		return $this->memcached->set($key, $value, 600);
	}

	public static function getContentStreamingServerByEncodedAdName($EncodedAdName) {
		$sql = "select distinct(ssip) from content_profile";
		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if ($ret == -1) {
			return false;
		} else {
			return $ret;
		}
	}

	private function sendNewContentMail() {
		$objUserInfo = new UserInfo();
		$objUserInfo->getCurrentUserInfo();


		$to = 'adex@novix.in';
		$subject = 'New ad uploaded by' . $objUserInfo->email . " on" ;
		$message = 'Filename: ' . $this->filename . "<br>" .
		'CampaignId: ' . $this->campaignId . "<br>" .
		'Filepath: ' . $this->filepath . "<br>";


		$headers = 'From: ' . $objUserInfo->email . "\r\n" .
		'Reply-To: ' . $objUserInfo->email . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

		if (mail($to, $subject, $message, $headers)) {
			echo "mail sent";
		} else {
			echo "mail sending failed";
		}
	}

	private function registerContent() {
		$objUserInfo = new UserInfo();
		$objUserInfo->getCurrentUserInfo();
		$url = "http://" . $_SERVER['SERVER_NAME'] . Ad::fileUrlPath() . $this->filename;
		$url = urlencode($url);
		$videoDuration = 0;

		if (extension_loaded("ffmpeg")) {
			$videoFile = new ffmpeg_movie($this->filepath);
			$videoDuration = $videoFile->getDuration();
			$videoDuration = explode(".", $videoDuration);
			$videoDuration = $videoDuration[0];
		}

		$sql = "INSERT INTO `ad` ( `id`,`campaign_id`,`content_type` ,`physical_loc` ,`url` ,`filename` ,`userid` ,`upload_date` ,`status` ,`validity`, `ad_length`) 
		VALUES (NULL, '" . $this->campaignId . "', '" . $this->filetype . "', '" . $this->filepath . "', '" . $url . "', '" . $this->filename . "', '" . $objUserInfo->userid . "',CURRENT_TIMESTAMP , 'pending', '2012-04-30 00:00:00', '".$videoDuration."')";

		$objData = new DataHandler();
		$ret = $objData->PutQuery($sql);
		//$this->sendNewContentMail();

		// '@@@@@@' from the below xml are replaced with the id later when delivering the ad in gettag api

		$xml = '<?xml version="1.0" encoding="UTF-8"?><VAST xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="vast.xsd" version="2.0"><Ad id="d1d4f7a6-f53f-4028-9225-02268acb9789"><InLine><AdSystem>Adform</AdSystem><AdTitle>In Stream</AdTitle><Creatives><Creative><Linear><Duration>'.gmdate("H:i:s", $videoDuration).'</Duration>';
		$TrackingEvents = '<TrackingEvents><Tracking event="complete"><![CDATA[http://'.$_SERVER['SERVER_NAME'].'/adex/public/log.php?id=@@@@@@&debug=jwplayer_vast]]></Tracking></TrackingEvents>';
		$media= '<MediaFiles><MediaFile delivery="progressive" type="video/x-flv" bitrate="#Please enter bitrate value#" width="320" height="240" scalable="false" maintainAspectRatio="false">http://'.$_SERVER['SERVER_NAME'].'/adex/resource/ads/orig/'.$this->filename.'</MediaFile></MediaFiles></Linear></Creative></Creatives></InLine></Ad></VAST>';
		$vastTag = $xml.$TrackingEvents.$media;

		$vastsql = "INSERT INTO `vast_tag`( `campaign_id`, `vast_url`, `media_file_name`) VALUES (".$this->campaignId.",'".$vastTag."','".$this->filename."')";

		$objData->PutQuery($vastsql);

		return $ret;
	}

	private static function fileUrlPath() {
		return AD_URL;
	}

	private static function fileUploadDir() {
		return AD_STORE;
	}

	public function getCurrentUserContent($contentType = "") {
		$objUserInfo = new UserInfo();
		$objUserInfo->getCurrentUserInfo();

		if ($contentType == "") {
			$sql = "SELECT `content_type` , `url` , `upload_date` , `status` , `validity`,`content`" .
			"FROM `ad`" .
			"WHERE userid LIKE '" . $objUserInfo->userid . "'";
		} else {
			$sql = "SELECT `content_type` , `url` , `upload_date` , `status` , `validity`,`content`" .
			"FROM `ad`" .
			"WHERE userid LIKE '" . $objUserInfo->userid . "'" .
			"AND `content_type` LIKE 'image'";
		}
		$objData = new DataHandler();
		return $objData->GetQuery($sql);
	}

	public function handleVastUpload($campaignId, $vast_tag){
		//fetch the xml contents of the tag
		//_debug($vast_tag);
		$xml = file_get_contents($vast_tag);

		//_debug($xml);
		//parse xml
		$parsedXML = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);

		//_debug($parsedXML);
		
		$mediaFileUrl = (string) trim($parsedXML->Ad->InLine->Creatives->Creative->Linear->MediaFiles->MediaFile);

		// download the media file
		$mediaContents = file_get_contents($mediaFileUrl);

		// rename it and save
		$n = rand(10e16, 10e20);
		$random = base_convert($n, 10, 36);

		$filename = $random;
		file_put_contents(ORIG_AD_FILES.$filename, $mediaContents);

		//insert entry in db
		$sql = "INSERT INTO `vast_tag`( `campaign_id`, `vast_url`, `media_file_name`) VALUES (".$campaignId.",'".$vast_tag."','".$filename."')";
		_debug($sql);
		//exit();
		$objData = new DataHandler();
		$objData->PutQuery($sql);
		return true;
	}

	public function handleVideoUpload($campaignId) {
			//TODO media update
		$objUserInfo = new UserInfo();
		$objUserInfo->getCurrentUserInfo();
			//                echo "<pre>";
			//                print_r($_FILES);
			//                echo "</pre>";
			//        exit();
		if ($_FILES['video']['type'] == "video/mp4"
			|| $_FILES['video']['type'] == "video/x-ms-wmv"
			|| $_FILES['video']['type'] == "video/quicktime"
			|| $_FILES['video']['type'] == "video/avi"
			|| $_FILES['video']['type'] == "video/x-m4v"
			|| $_FILES['video']['type'] == "video/mpeg") {
			switch ($_FILES['video']['type']) {
				case "video/mp4":
				$filetype = "mp4";
				$contentType = "video";
				break;
				case "video/mpeg":
				$filetype = "mpeg";
				$contentType = "video";
				break;
				case "video/quicktime":
				$filetype = "mov";
				$contentType = "video";
				break;
				case "video/x-ms-wmv":
				$filetype = "wmv";
				$contentType = "video";
				case "video/x-m4v":
				$filetype = "m4v";
				$contentType = "video";
			}

			$uploaddir = ORIG_AD_FILES;

			if (!is_dir($uploaddir)) {

				if (!mkdir($uploaddir, 0777, true)) {
					return false;
				}
			}
			$n = rand(10e16, 10e20);
			$random = base_convert($n, 10, 36);

			$filename = $random . "." . $filetype;
				//            echo "<pre>";
				//            print_r($_FILES);
				//            echo "</pre>";
			if (move_uploaded_file($_FILES['video']['tmp_name'], $uploaddir . $filename)) {
					//echo "The file " . basename($_FILES['media_content']['name']) . " has been uploaded to " . $filename;
					//exit();
				$this->status = 'approved';
				$this->campaignId = $campaignId;
				$this->userid = $objUserInfo->userid;
				$this->filename = $filename;
				$this->filepath = $uploaddir . $filename;
				$this->filetype = $contentType;
				$this->userdir = $objUserInfo->uploaddir;

				$this->registerContent();
					//  $return = false;
					//nohup makes the php run in bg even if the shell isnt present or user logs out.
				$cmd = "/var/www/adex/Admin/xcode.php " . $filename . " > /dev/null &";

					//$url = "http://".$_SERVER['SERVER_ADDR']."/adex/Admin/xcode.php?profid=".$config['id']."&origadfile=".$filename;
					//Utils::http($url);
				exec($cmd);

				
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	public function handleTextUpdate() {
		$objUserInfo = new UserInfo();
		$objUserInfo->getCurrentUserInfo();
			//        echo "<pre>";
			//        print_r($_POST);
			//        echo "</pre>";
			//        exit;
		$text = $_POST['text_ad_content'];
		if ($text) {
			echo $sql = "INSERT INTO `ad` (`id`,`content_type` ,`physical_loc` ,`userid` ,`upload_date` ,`status` ,`validity`,`content`)" .
			"VALUES (NULL, 'text', '" . $this->filepath . "', '" . $objUserInfo->userid . "',CURRENT_TIMESTAMP , 'pending', '2012-04-30 00:00:00','" . $text . "');";
			$objData = new DataHandler();
			$objData->PutQuery($sql);
			return true;
		}
		else
			return false;
	}

	public function getAdIstreamAd() {
		$platform = $_GET['dev'];
		$filename = $_GET['chname'];
		$resolution = $_GET['wh'];
		$pub_id = $_GET['pubid'];
		$profile_id = $_GET['profid'];

			//special case for istream
		$sql = "SELECT
		ea.encoded_filename as filename,
		a.campaign_id as campaign_id,
		cp.content_id as content_id,
		c.cpm as cpm, c.id,
		a.ad_length as ad_length
		FROM encoded_ad ea, ad a, content_profile cp, publisher_profiles pp, profiles_master pm,
		campaign c
		WHERE a.campaign_id
		IN (SELECT `campaign_id` FROM `campaign_parameters` cp1, campaign c1 WHERE cp1.`pref` = 'platform' AND cp1.`value` = '" . $platform . "' and cp1.campaign_id=c1.id and c1.status like 'active')
		and c.advertiser_id = 'timepass'
		AND ea.original_filename LIKE a.filename
		AND a.campaign_id = c.id
		AND cp.publisher_profile_id=pp.id
		AND pp.profile_id=pm.id
		AND ea.profile_id=pm.id
		AND pp.id=" . $profile_id . "
		ORDER BY c.cpm DESC";
		if ($_GET['debug'] == 1) {
			echo "<pre>";
			print_r($sql);
			echo "</pre>";
		}
		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if ($ret == -1)
			return false;
		else
			return $ret;
	}

	public function getTag($request) {
		$platform = Utils::getDevice($request['ua']);
		$requested_filename = $request['chname'];
		$cid = $request['cid'];
		$pub_id = $request['pubid'];
		$profile_id = $request['profid'];

		$server_tz = SERVER_TZ;

		$objContent = new Content();

		$pub_tz = UserInfo::GetUserTimeZone($pub_id);

		$contentTags = $objContent->getContentTagsById($cid);
		

		$sql = 
		"select
		vast_tag.vast_url as vast_url, 
		vast_tag.media_file_name as filename,
		vast_tag.campaign_id as campaign_id,
		channels.cid as content_id,
		campaign.cpm as cpm, campaign.id, campaign.ad_spots
		FROM 
		vast_tag
		join campaign on (campaign.id LIKE vast_tag.campaign_id)
		join channels 
		WHERE 
		vast_tag.campaign_id
		IN (SELECT `campaign_id` FROM `campaign_parameters` cp1, campaign c1 WHERE cp1.`pref` = 'platform' AND cp1.`value` = '".$platform."' AND cp1.campaign_id=c1.id	AND c1.status like 'active'	AND c1.start_date <= DATE(convert_tz( NOW( ),'".$server_tz."','".$pub_tz."') ) AND c1.end_date >= DATE( convert_tz(NOW( ),'".$server_tz."','".$pub_tz."') ))
		and channels.name='".$requested_filename."'
		order by campaign.cpm";

		if ($_GET['debug'] == 1) {
			echo "<pre>";
			print_r($sql);
			echo "</pre>";
		}


		$objData = new DataHandler();
		/*
		construct key as function of($platform, $requested_filename, $profid, )
		*/
		$ckey = $platform."_".$requested_filename."_".$profile_id;

		/*
		$ret = $this->get($ckey);
		if($ret == false){

			$ret = $objData->GetQuery($sql);
			$this->set($ckey, $ret, 300);
		}
		else{
			if($_GET['debug'] == 1){
				echo "<pre>";
				print_r("from cache with key");
				print_r($ckey);
				echo "</pre>";
			}
		}
		*/

		$ret = $objData->GetQuery($sql);
		$objCampaign = new Campaign();

		$filteredByTags = array();
		if ($ret == -1)
			return false;
		else {

			foreach ($ret as $result) {

				$objCampaign->id=$result['campaign_id'];
				$campaignTags = $objCampaign->getCampaignTagsById();
				$common = array();
				if ($result['tags'] == "" && $contentTags == "") {
					$filteredByTags[] = $result;
				} else {
					$arrContentTags = explode(",", $contentTags);
					$arrCampaignTags = explode(",", $campaignTags);
					$common = array_intersect($arrCampaignTags, $arrContentTags);
					if ($common != "") {
						$filteredByTags[] = $result;
					}
				}
			}
		}

		if($_GET['debug'] == 1){
			echo "<br>filteredByTags";
			echo "<pre>";
			print_r($filteredByTags);
			echo "</pre>";
		}
		$filterByImpressions = array();
		$objReport = new Reports();
		if($filteredByTags == ""){
			return false;
		}
		else{
			$filterByImpressions = $filteredByTags;
		}

		/*
		else{
			foreach($filteredByTags as $ad){
				$impressions = $objReport->CampaignGetTotalImpressions($ad['campaign_id']);
				if($_GET['debug'] == 1){
					echo "<br>".$ad['campaign_id']." impressions-".$impressions;
					echo "<br>".$ad['campaign_id']." spots-".$ad['campaign_id'];
				}
				if($impressions < $ad['ad_spots']){
					$filterByImpressions[] = $ad;

				}
			}
			if($_GET['debug'] == 1){
				echo "<br>filterByImpressions";
				echo "<pre>";
				print_r($filterByImpressions);
				echo "</pre>";
			}

			return $filterByImpressions;
		}
		*/
		return $filterByImpressions;

	}

	public function getAd($request) {
		$platform = Utils::getDevice($request['ua']);
		$requested_filename = $request['chname'];
		$cid = $request['cid'];
		$pub_id = $request['pubid'];
		$profile_id = $request['profid'];

		$server_tz = SERVER_TZ;

		$objContent = new Content();

		$pub_tz = UserInfo::GetUserTimeZone($objContent->GetPublisherByPublisherProfile($profile_id));

		$contentTags = $objContent->getContentTagsById($cid);
		

		$sql = 
		"select 
		encoded_ad.encoded_filename as filename,
		ad.campaign_id as campaign_id,
		channels.cid as content_id,
		campaign.cpm as cpm, campaign.id, campaign.ad_spots
		FROM 
		encoded_ad  
		join ad on (original_filename LIKE filename)
		join profiles_master on(profiles_master.id=encoded_ad.profile_id)
		join publisher_profiles on(profiles_master.id=publisher_profiles.profile_id)
		join content_profile on(publisher_profiles.id=content_profile.publisher_profile_id)
		join content on (content_profile.content_id=content.id)
		join channels on (content.id=channels.cid)
		join campaign on (ad.campaign_id=campaign.id)
		WHERE 
		ad.campaign_id
		IN (SELECT `campaign_id` FROM `campaign_parameters` cp1, campaign c1 WHERE cp1.`pref` = 'platform' AND cp1.`value` = '".$platform."' AND cp1.campaign_id=c1.id	AND c1.status like 'active'	AND c1.start_date <= DATE(convert_tz( NOW( ),'".$server_tz."','".$pub_tz."') ) AND c1.end_date >= DATE( convert_tz(NOW( ),'".$server_tz."','".$pub_tz."') ))
		and encoded_ad.encode_status=1
		and channels.name='".$requested_filename."'
		and publisher_profiles.id=".$profile_id."
		order by campaign.cpm";

		if ($_GET['debug'] == 1) {
			echo "<pre>";
			print_r($sql);
			echo "</pre>";
		}


		$objData = new DataHandler();
		/*
		construct key as function of($platform, $requested_filename, $profid, )
		*/
		$ckey = $platform."_".$requested_filename."_".$profile_id;

		/*
		$ret = $this->get($ckey);
		if($ret == false){

			$ret = $objData->GetQuery($sql);
			$this->set($ckey, $ret, 300);
		}
		else{
			if($_GET['debug'] == 1){
				echo "<pre>";
				print_r("from cache with key");
				print_r($ckey);
				echo "</pre>";
			}
		}
		*/

		$ret = $objData->GetQuery($sql);
		$objCampaign = new Campaign();

		$filteredByTags = array();
		if ($ret == -1)
			return false;
		else {

			foreach ($ret as $result) {

				$objCampaign->id=$result['campaign_id'];
				$campaignTags = $objCampaign->getCampaignTagsById();
				$common = array();
				if ($result['tags'] == "" && $contentTags == "") {
					$filteredByTags[] = $result;
				} else {
					$arrContentTags = explode(",", $contentTags);
					$arrCampaignTags = explode(",", $campaignTags);
					$common = array_intersect($arrCampaignTags, $arrContentTags);
					if ($common != "") {
						$filteredByTags[] = $result;
					}
				}
			}
		}

		if($_GET['debug'] == 1){
			echo "<br>filteredByTags";
			echo "<pre>";
			print_r($filteredByTags);
			echo "</pre>";
		}
		$filterByImpressions = array();
		$objReport = new Reports();
		if($filteredByTags == ""){
			return false;
		}
		else{
			$filterByImpressions = $filteredByTags;
		}

		/*
		else{
			foreach($filteredByTags as $ad){
				$impressions = $objReport->CampaignGetTotalImpressions($ad['campaign_id']);
				if($_GET['debug'] == 1){
					echo "<br>".$ad['campaign_id']." impressions-".$impressions;
					echo "<br>".$ad['campaign_id']." spots-".$ad['campaign_id'];
				}
				if($impressions < $ad['ad_spots']){
					$filterByImpressions[] = $ad;

				}
			}
			if($_GET['debug'] == 1){
				echo "<br>filterByImpressions";
				echo "<pre>";
				print_r($filterByImpressions);
				echo "</pre>";
			}

			return $filterByImpressions;
		}
		*/
		return $filterByImpressions;

	}

	public function getAdFreq($request) {
		$filename = $request['chname'];
		$profile_id = $request['profid'];

		$sql = "SELECT c.ad_freq FROM `content` c, `publisher_profiles` pp, `content_profile` cp WHERE cp.content_id=c.id and pp.id=cp.publisher_profile_id and pp.id=" . $profile_id;

		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if ($ret == -1)
			return false;
		else
			return $ret[0]['ad_freq'];
	}

	public function getPrerollOption($request) {
		$filename = $request['chname'];
		$profile_id = $request['profid'];

		$sql = "SELECT c.preroll FROM `content` c, `publisher_profiles` pp, `content_profile` cp
		WHERE cp.content_id=c.id
		and pp.id=cp.publisher_profile_id
		and pp.id=" . $profile_id;

		$objData = new DataHandler();
		$ret = $objData->GetQuery($sql);
		if ($ret == -1)
			return false;
		else
			return $ret[0]['preroll'];
	}
}
?>

