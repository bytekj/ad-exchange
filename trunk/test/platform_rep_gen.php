<?php

require_once '../include/global.php';
require_once '../Db/DataHandler.php';

require_once '../Classes/Utils.php';

$sql = "select * from log where ad_played_timestamp not like '0000-00-00 00:00:00' and content_id = 2";
$objData = new DataHandler();
$objData->connect();
$log = $objData->NoConnectGetQuery($sql);
_debug($sql);
_debug($log);
exit;

foreach ($log as $key => $logEntry) {
	$ipDetail = Utils::getIpDetails($logEntry['client_ip']);	
	$device = Utils::getDeviceType($logEntry['ua']);

	$biSql = "INSERT INTO `bi_content_new`
	(`id`, `content_id`, `country`, `city`, `client_hit_time`, `client_stop_time`, `device`) VALUES
	(NULL,'" . $logEntry['content_id'] . "','" . $ipDetail->country_code . "','" . $ipDetail->city . "',NOW(),NULL,'" . $device . "')";

	if($key == 5){
		break;
	}
	$objData->NoConnectPutQuery();
}
$objData->Disconnect();
?>