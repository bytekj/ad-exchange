<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require '../../include/global.php';
require_once '../../Db/DataHandler.php';
require_once '../../Classes/UserInfo.php';
require_once '../../Classes/Campaign.php';
require_once '../../Classes/Session.php';
require_once '../../Classes/Advertiser.php';
require_once '../../Classes/Utils.php';
require_once '../../Classes/Reports.php';
require_once '../../Classes/Content.php';

$objSession = new Session();

$objUserInfo = new UserInfo();

if ($objSession->isUserLoggedIn()) {
	//TODO show add upload form
	$objUserInfo->getCurrentUserInfo();
} else {
	if ($_SERVER['SCRIPT_NAME'] != '/adex/public/login.php' && $_SERVER['SCRIPT_NAME'] != '/adex/public/register.php') {
		header("Location: /adex/public/login.php?redirect_to=" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
	}
}


$country = $_GET['country'];
$campaignIds = $_GET['camp'];
$contentId = $_GET['con'];
$start_date = $_GET['st_dt'];
$end_date = $_GET['ed_dt'];

$objCampaign = new Campaign();

$objContent = new Content();

$showStatsBool = false;

$cityData = "";

$objReport = new Reports();

$countrycode = "";

if ($objUserInfo->usertype == "advertiser") {

    $ids = explode(",", $campaignIds);
    foreach ($ids as $key => $campaignId) {
        $showStatsBool = $objCampaign->getAdvertiserCampaignById($objUserInfo->userid, $campaignId);
    }
    
    if ($showStatsBool) {
        //$cityData = $objReport->CampaignReportGetAllCityDataByCountry($campaignId, $country);
        $cityData = $objReport->CampaignReportGetAllCityDataByCountryByDate($campaignId, $country, $start_date, $end_date);
        $countrycode = urldecode(Utils::GetCountryCodeByCountryName($country));
    }
} else if ($objUserInfo->usertype == "publisher") {
    $showStatsBool = $objContent->getPublisherContentByPublisherId($objUserInfo->userid, $contentId);

    if ($showStatsBool) {
        $cityData = $objReport->ContentReportGetAllCityDataByCountry($contentId, $country);
        $countrycode = $country;
    }
}


if ($showStatsBool) {
    ?>
    <html>
        <head>
            <link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
            <script type="text/javascript" src="http://www.google.com/jsapi"></script>
            <script type="text/javascript">
                google.load('visualization', '1');
            </script>
        </head>
        <title><?php if($objUserInfo->usertype == "advertiser") echo $objCampaign->name; else echo $objContent->name; ?></title>
        <label style="width: 400px;"><?php if($objUserInfo->usertype == "advertiser") echo $objCampaign->name; else echo $objContent->name; echo  " Regional report"; ?></label><br>
        <table>
            <tr>
                <td>
                    <script type="text/javascript">
                        google.load('visualization', '1', {'packages': ['geochart']});
                        google.load("visualization", "1", {packages:["corechart"]});

                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Cities');
                        data.addColumn('number', '<?php if ($objUserInfo->usertype == "advertiser") echo "Impressions"; else echo "Views"; ?>');
    <?php
    foreach ($cityData as $city) {
        echo "data.addRow(['" . $city['city'] . "', " . $city['count'] . "]);";
    }
    ?>		
        function drawCityChart() {

            var wrapper = new google.visualization.ChartWrapper({
                chartType: 'ColumnChart',
                dataTable: data,
                options: {'title': 'Cities'},
                containerId: 'regiondiv'
            });
            wrapper.draw();
        }
        function drawRegionsMap() {
            var options = {region: '<?php echo $countrycode ?>',  displayMode: 'markers'};

            var chart = new google.visualization.GeoChart(document.getElementById('VisualMap'));
            chart.draw(data, options);

        }
        google.setOnLoadCallback(drawRegionsMap);
        google.setOnLoadCallback(drawCityChart);             
                    </script>
                    <div id="regiondiv" style="width: 100%; height: 300px;"></div>
                    <div id="VisualMap"></div>
                </td>
            </tr>
        </table>
    </html>
    <?php
} else {
    echo "Access prohibited !";
}
?>