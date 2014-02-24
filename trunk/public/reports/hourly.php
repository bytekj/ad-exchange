<?php
if(isset($_GET['exprt'])){
	//set header for export
	header('Content-type: application/x-excel');
}

require_once '../../include/global.php';
require_once '../../Db/DataHandler.php';
require_once '../../Classes/UserInfo.php';
require_once '../../Classes/Campaign.php';
require_once '../../Classes/Session.php';
require_once '../../Classes/Advertiser.php';
require_once '../../Classes/Utils.php';
require_once '../../Classes/Reports.php';


$objSession = new Session();

$objUser = new UserInfo();
$objUser->getCurrentUserInfo();


$id = NULL;
$start_date = NULL;
$end_date = NULL;
$dailyImpressions = array();
$objCampaign = new Campaign();


if ($_POST['id'] || $_GET['id']) {
	if (isset($_POST['id'])) {
		$id = $_POST['id'];
	} else if (isset($_GET['id'])) {
		$id = $_GET['id'];
	}
	if(isset($_GET['st_dt']) && isset($_GET['ed_dt'])){
		$default_dates = false;
		$start_date = $_GET['st_dt'];
		$end_date = $_GET['ed_dt'];
	}


	$objReport = new Reports();
	if($objUser->usertype == 'advertiser'){

		$hourlyImpressions = $objReport->CampaignGetHourlyImpressions($id, $objUser->userid, $start_date, $end_date);
		/*
		 echo "<pre>";
		print_r($hourlyImpressions);
		echo "</pre>";
		*/
		if($hourlyImpressions){
			$formattedHourlyData = array();
			for ($i = 0; $i < 24; $i++) {

				$formattedHourlyData[$i]['hour'] = $i;
				$formattedHourlyData[$i]['count'] = 0;

				foreach ($hourlyImpressions['hourly'] as $key => $oneHourData) {

					if ($oneHourData['hour'] == $i) {
						$formattedHourlyData[$i]['hour'] = $i;
						$formattedHourlyData[$i]['count'] = $oneHourData['count'];
						break;
					}
				}
			}
		}

		//$objCampaign->getAdvertiserCampaignById($objUser->userid, $id);
		/*
		 echo "-----------------------------------";
		echo "<pre>";
		print_r($formattedHourlyData);
		echo "</pre>";
		*/
		//echo sizeof($dailyImpressions['daily']);

		if($_GET['exprt']){
			header('Content-Disposition: attachment; filename=Campaign'."_".$start_date."_".$end_date."_hourly.csv");
			$ids = explode(",", $id);

			echo "Campaign,Start Date, End Date\n";
			foreach ($ids as $key => $oneid) {
				$objCampaign->getAdvertiserCampaignById($objUser->userid, $oneid);		
				echo $objCampaign->name.",".$objCampaign->start_date.",".$objCampaign->end_date."\n";
			}

			echo "\n\nHour,Impressions\n";
			foreach ($formattedHourlyData as $hourData ){
				echo $hourData['hour'].",".$hourData['count']."\n";
			}
			exit;
		}
	}
}
?>
<html>
<head>
	<link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
	function drawMonthlyChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Hour');
		data.addColumn('number', 'Impressions');
		<?php
		foreach ($formattedHourlyData as $data) {
			echo "data.addRow(['" . $data['hour'] ."', " . $data['count'] . "]);";
		}
		?>

		var wrapper = new google.visualization.ChartWrapper({
			chartType: 'LineChart',
			dataTable: data,
			options: {'title': 'Hourly Distribution'},
			containerId: 'dailydiv',
			chartArea:{left:20,top:0,width:"100%",height:"500px"},
			vAxis:{minValue:0}
		});

		monthlydata = data;
		wrapper.draw();
	}
	google.load('visualization', '1');
	google.setOnLoadCallback(drawMonthlyChart);
	</script>
</head>
<title>Ad$parx</title>
<body>
	<b>Hourly Impressions</b>
		<br>
		<br>
		<?php
		$minwidth = 400;
		$size = sizeof($formattedHourlyData)*40;
		if($size < $minwidth){
			$size = $minwidth;
		}
		?>
		<div id="dailydiv" style="width: <?php echo $size; ?>px">

		</div>
	</body>
	</html>
