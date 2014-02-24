<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../include/global.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/UserInfo.php';
require_once '../Classes/Campaign.php';
require_once '../Classes/Session.php';
require_once '../Classes/Advertiser.php';
require_once '../Classes/Utils.php';
require_once '../Classes/Reports.php';

function _Print($toPrint) {
    echo "<pre>";
    print_r($toPrint);
    echo "</pre>";
}

$userId = "zenga_adv";
$campaignId = 21;

$objReport = new Reports();

//$yesterDayData = $objReport->CampaignGetTodayImpressions($campaignId, $userId);
$yesterDayData = $objReport->CampaignGetTodayImpressions($campaignId, $userId);



$formattedHourlyData = array();
for ($i = 0; $i < 24; $i++) {

    $formattedHourlyData[$i]['hour'] = $i;
    $formattedHourlyData[$i]['count'] = 0;

    foreach ($yesterDayData['hourly'] as $key => $oneHourData) {

        if ($oneHourData['hour'] == $i) {
            $formattedHourlyData[$i]['hour'] = $i;
            $formattedHourlyData[$i]['count'] = intval($oneHourData['count']);
            break;
        }
    }
}
$data['cols'][0]['id'] = 'hour';
$data['cols'][0]['label'] = 'Hour';
$data['cols'][0]['type'] = 'string';

$data['cols'][1]['id'] = 'count';
$data['cols'][1]['label'] = 'Impressions';
$data['cols'][1]['type'] = 'number';

foreach ($formattedHourlyData as $key=> $oneHourData){
    $data['rows'][$key]['c'][0]['v'] = $oneHourData['hour'];
    $data['rows'][$key]['c'][1]['v'] = $oneHourData['count'];
}

echo json_encode($data);
//echo json_encode($formattedHourlyData);
?>
