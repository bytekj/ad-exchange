<?php

require_once '../include/global.php';
require_once '../Db/DataHandler.php';

$pubid = $_GET['pubid'];
$proto = $_GET['proto'];

$sql = "select * from channels where pubid='" . $pubid . "' and chstatus not like '0' and ".$proto." = 1";

$objData = new DataHandler();

$res = $objData->GetQuery($sql);
if($res != -1){
    $arrChannel = array();
    foreach ($res as $key => $channel) {
        //$arrChannel[$key]['id'] = $channel['id'];
        $arrChannel[$key]['pubid'] = $channel['pubid'];
        $arrChannel[$key]['name'] = $channel['name'];
        $arrChannel[$key]['source_ip'] = $channel['source_ip'] . $channel['path'];
        if ($channel['ad_status'] == 'Enabled') {
            $arrChannel[$key]['ad_status'] = 1;
        } else {
            $arrChannel[$key]['ad_status'] = 0;
        }
    //$arrChannel[$key]['ad_status'] = $channel['ad_status'];
        $arrChannel[$key]['nz'] = $channel['nz'];
        $arrChannel[$key]['commport'] = $channel['commport'];
        $arrChannel[$key]['ad_directory'] = $channel['ad_directory'];
        $arrChannel[$key]['ingest_ip'] = $channel['ingest_ip'] . $channel['path'];
    }
    $result['count'] = sizeof($arrChannel);
    $result['result'] = $arrChannel;
//echo "<pre>";
    echo json_encode($result);
//echo "</pre>";    
}
else{
    $error = $objData->getErrorNo();
    if($error == false)
    $result['error'] = "No Channels";
    else
        $result['error'] = $error;
    echo json_encode($result);
}


?>
