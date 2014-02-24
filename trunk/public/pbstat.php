<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../Classes/Log.php';

require_once '../Db/DataHandler.php';

require_once '../include/global.php';

$sql = "select * from log_content_usage where 1";

$objData = new DataHandler();
$arrResult = $objData->GetQuery($sql);
global $gi;
echo "<pre>";
print_r($arrResult[0]);
echo "</pre>";
echo "<pre>";
print_r(GeoIP_record_by_addr($gi, $arrResult[0]['client_ip']));
echo "</pre>";


$key = 0;
foreach ($arrResult as $key => $row) {
    //$ipDetail = geoip_record_by_addr($gi, $row['client_ip']);
    $ipDetail = GeoIP_record_by_addr($gi, $row['client_ip']);
    echo $insertSql = "INSERT INTO `bi_content`
        (`id`, `content_id`, `country`, `city`, `client_hit_time`, `client_stop_time`, `device`) VALUES 
        (NULL,'" . $row['content_id'] . "','" . $ipDetail->country_code . "','" . $ipDetail->city . "','" . $row['client_start_time'] . "',NULL,'" . $row['device'] . "')";
    $objData->PutQuery($insertSql);
}
echo "<br>Inserted " . $key . " rows";
?>
