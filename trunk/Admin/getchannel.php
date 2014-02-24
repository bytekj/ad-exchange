<?php

require_once '../include/global.php';
require_once '../Db/DataHandler.php';

$id = "";
$sql = "";


if (isset($_GET['pubid']) && isset($_GET['name'])) {

    $pubid = $_GET['pubid'];
    $name = $_GET['name'];

    $sql = "SELECT `pubid`, `name`, `source_ip`, `path`, `ad_status`, `nz`, `commport`, `ad_directory`, `ingest_ip`
        from channels where pubid='" . $pubid . "' and name='" . $name . "'";
} else {
    $result['error'] = "invalid request";
    echo json_encode($result);
    exit();
}

if (isset($_GET['debug'])) {
    echo "<br>" . $sql;
}

$objData = new DataHandler();

$res = $objData->GetQuery($sql);
$arrChannel = array();
foreach ($res as $key => $channel) {

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
print_r(json_encode($result));
//echo "</pre>";
?>
