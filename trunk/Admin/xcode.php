#!/usr/bin/php
<?php

/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/
require_once '../include/global.php';

require_once '../Db/DataHandler.php';
require_once '../Classes/Content.php';
require_once '../Classes/UserInfo.php';

$message = "";

$origAdFile = $argv[1];

if($origAdFile != ""){
	$objContent = new Content();
	$config_files = getProfile();

	foreach ($config_files as $config) {

		$n1 = rand(10e16, 10e20);
		$random1 = base_convert($n1, 10, 36);
		$out_filename = $random1;
		//$fp = fopen("xcode.log", "a");
		//mark outfile as transcoding started.
		
		//fwrite($fp,"-----------------------------------------------------------------------------------------------");
		$sql = "INSERT INTO `encoded_ad`
		(`id`, `original_filename`, `profile_id`, `encoded_filename`, `encode_status`)
		VALUES
		(NULL, '" . $origAdFile . "', '" . $config['id'] . "', '" . $out_filename . "', '0')";
		
		$objData = new DataHandler();

		$objData->PutQuery($sql);
		//$cmd = TRANSCODE . " " . TRANSCODE_CONF . $config['config'] . " " . $uploaddir . $filename . " 1 " . AD_STORE . " " . $out_filename . "&";
		//echo "running in shell " . $cmd;
		//exec($cmd, $ret);
		//fwrite($fp, $sql);
		$xcodeOptions = json_encode($config);

		$cmd = TRANSCODE . " '" . $xcodeOptions . "' " . ORIG_AD_FILES . $origAdFile . " " . ENCODED_AD_FILES . $out_filename;
		$return = "";
		//fwrite($fp,$cmd);
		system($cmd);
		updateXcodedFile($origAdFile, $out_filename);
		
		//fwrite($fp, "-----------------------------------------------------------------------------------------------");
		//fclose($fp);

	}
	send_confirm_mail($origAdFile);
}
else {
	$message['error'] = "Invalid Request";
}
echo json_encode($message);

function updateXcodedFile($origFile, $xcodedFile) {
	$sql = "UPDATE
	`encoded_ad`
	SET `encode_status` = '1'
	WHERE
	`original_filename` LIKE '" . $origFile . "' AND
	`encoded_filename` LIKE '" . $xcodedFile . "'";
	$objData = new DataHandler();
	$objData->PutQuery($sql);
}

function getFilesToEncode($origAdFile) {

	$sql = "SELECT
	`original_filename`,
	`profile_id`,
	`encoded_filename`,
	`encode_status`
	FROM
	`encoded_ad`
	WHERE
	`original_filename` like '" . $origAdFile . "'";
	$objData = new DataHandler();

	$arrFilestoEncode = $objData->GetQuery($sql);
	if ($arrFilestoEncode == -1) {
		return FALSE;
	} else {
		return $arrFilestoEncode;
	}
}

function send_confirm_mail($originalAdFile){
	$sql = "select userid,campaign_id,upload_date from ad where filename='".$originalAdFile."'";
	
	$objData = new DataHandler();
	
	$adInfo = $objData->GetQuery($sql);
	
	$userid = $adInfo[0]['userid'];
	$uploadDate = $adInfo[0]['upload_date'];
	$campaignId = $adInfo[0]['campaign_id'];
	
	$userInfoSql = "select email from users where userid='".$userid."'";
	
	$email = $objData->GetQuery($userInfoSql);
	
	$email = $email[0]['email'];
	
	$campSql = "select name from campaign where id=".$campaignId;
	
	$campName = $objData->GetQuery($campSql);
	
	$message = "Your Ad campaign ".$campName[0]['name']."  created on ".$uploadDate." is now fully functional";
	
	$message = wordwrap($message, 70);
	
	sendmail($email, "Your Ad Campaign (".$campName[0]['name'].") Status", $message);
}

function sendmail($to, $subject,$message){
	/*
	$to      = 'kj@novix.in';
	$subject = 'the subject';
	$message = 'hello';
	
	*/
	$headers = 'From: adex@novix.in' . "\r\n" .
			'Reply-To: adex@novix.in' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
	
	$res= mail($to, $subject, $message, $headers);
	if($res){
		echo "Mail sent";
	}
	else{
		echo "Mail not sent";
	}
}

function getProfile($profid="") {
	$sql = "";
	if($profid == ""){
		$sql = "SELECT
		`id`,
		`video_resolution`,
		`video_bit_rate`,
		`video_codec`,
		`video_fps`,
		`audio_bit_rate`,
		`audio_sampling_rate`,
		`audio_channels`,
		`audio_codec`,
		`config`,
		`stream_type`,
		`pixel_aspect_ratio`,
		`key_int_max`,
		`cabac_flag`,
		`audio_codec_profile`,
		`aacformat`,
		`format`
		FROM `profiles_master`";
	}
	else{
		$sql = "SELECT
		`id`,
		`video_resolution`,
		`video_bit_rate`,
		`video_codec`,
		`video_fps`,
		`audio_bit_rate`,
		`audio_sampling_rate`,
		`audio_channels`,
		`audio_codec`,
		`config`,
		`stream_type`,
		`pixel_aspect_ratio`,
		`key_int_max`,
		`cabac_flag`,
		`audio_codec_profile`,
		`aacformat`,
		`format`
		FROM `profiles_master`
		WHERE `id`=" . $profid;
	}
	$objData = new DataHandler();
	//echo $sql;
	$profile = $objData->GetQuery($sql);
	if ($profile == -1) {
		return FALSE;
	} else {
		return $profile;
	}
}

function getOriginalAdFiles($profid) {
	$sql = "select original_filename,encoded_filename from encoded_ad where profile_id=" . $profid;
	if ($_GET['debug']) {
		echo "<br>" . $sql;
	}
	$objData = new DataHandler();

	$arrOrigFiles = $objData->GetQuery($sql);

	if ($arrOrigFiles == -1) {
		return FALSE;
	} else {
		return $arrOrigFiles;
	}
}

?>
