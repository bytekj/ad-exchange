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
require_once '../../Classes/Content.php';


$objSession = new Session();

$objUser = new UserInfo();
$objUser->getCurrentUserInfo();


$id = NULL;
$start_date = NULL;
$end_date = NULL;
$dailyImpressions = NULL;
$objCampaign = NULL;

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
		$objCampaign = new Campaign();
		
		$dailyImpressions = $objReport->CampaignGetDailyImpressions($id, $objUser->userid, $start_date, $end_date);
		//$objCampaign->getAdvertiserCampaignById($objUser->userid, $id);
		/*
		 echo "<pre>";
		print_r($dailyImpressions);
		echo "</pre>";
		echo sizeof($dailyImpressions['daily']);
		*/
		if($_GET['exprt']){
			header("Content-Disposition: attachment; filename=Campaign"."_".$start_date."_".$end_date."_daily.csv");
			$ids = explode(",", $id);
			echo "Campaign,Start Date, End Date\n";
			foreach ($ids as $key => $oneid) {
				$objCampaign->getAdvertiserCampaignById($objUser->userid, $oneid);		
				echo $objCampaign->name.",".$objCampaign->start_date.",".$objCampaign->end_date."\n";
			}

			echo "\n\nday,impressions\n";

			foreach ($dailyImpressions['daily'] as $dayData ){
				echo $dayData['DAY']."-".$dayData['MONTH']."-".$dayData['YEAR'].",".$dayData['count']."\n";
			}
			exit;
		}

		?>
		<html>
		<head>
			<link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
			<script type="text/javascript" src="http://www.google.com/jsapi"></script>
			<script type="text/javascript">
			function drawMonthlyChart() {
				var data = new google.visualization.DataTable();
				data.addColumn('string', 'Day');
				data.addColumn('number', 'Impressions');
				<?php
				foreach ($dailyImpressions['daily'] as $data) {
					echo "data.addRow(['" . $data['DAY'] ." ". Utils::getMonth($data['MONTH'])."', " . $data['count'] . "]);";
				}
				?>

				var wrapper = new google.visualization.ChartWrapper({
					chartType: 'LineChart',
					dataTable: data,
					options: {'title': 'Daily Distribution'},
					containerId: 'dailydiv',
					chartArea:{left:20,top:0,width:"100%",height:"120%"},
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
			<b>Daily Impressions</b><br><br>
			<?php
			$minwidth = 400;
			$size = sizeof($dailyImpressions['daily'])*40;
			if($size < $minwidth){
				$size = $minwidth;
			}
			?>
			<div id="dailydiv" style="width: <?php echo $size; ?>px">

			</div>
		</body>
		</html>
		<?php

	}
	else if($objUser->usertype == 'publisher'){
		$objContent = new Content();
		$publisherContent = $objContent->checkPublisherContentByPublisherId($objUser->userid, $id);


		if($publisherContent != false){
			//$objReport->
			
			$totalInvByDate = $objReport->getContentInventoryTotalByDateGrouped($id, $start_date, $end_date);
			$soldInvByDate = $objReport->getContentInventorySoldByDateGrouped($id, $start_date, $end_date);
			//_debug($totalInvByDate);
			//_debug($soldInvByDate);

			if(isset($_GET['exprt'])){
				header('Content-Disposition: attachment; filename='.$start_date."_".$end_date."_daily.csv");
				echo "Report for\n";
				foreach ($publisherContent as $key => $oneContent) {
					echo ",".$oneContent['name']."\n";
				}
				echo "\n";
				
				echo "Day,Total Inventory, Inventory Sold\n";
				foreach ($totalInvByDate as $key => $value) {
					echo $value['day']."-".$value['month']."-".$value['year'].",".$value['count'].",".$soldInvByDate[$key]['count']."\n";
				}
				exit();
			}
			?>
			<html>
			<head>
				<link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
				<script type="text/javascript" src="http://www.google.com/jsapi"></script>
				<script type="text/javascript">
				function drawDailyChart() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Day');
					data.addColumn('number', 'Total');
					data.addColumn('number', 'Sold');
					<?php
					foreach ($totalInvByDate as $key => $data) {
						echo "data.addRow(['" . $data['day'] ." ". Utils::getMonth($data['month'])."', " . $data['count'] . " , ".$soldInvByDate[$key]['count']."]);";
					}
					?>

					var wrapper = new google.visualization.ChartWrapper({
						chartType: 'LineChart',
						dataTable: data,
						options: {'title': 'Daily Inventory'},
						containerId: 'dailydiv',
						chartArea:{left:20,top:0,width:"100%",height:"120%"},
						vAxis:{minValue:0}
					});

					monthlydata = data;
					wrapper.draw();
				}
				google.load('visualization', '1');
				google.setOnLoadCallback(drawDailyChart);
				</script>
			</head>
			<body>
			<b><?php echo $objContent->name; ?>Daily Inventory</b><br><br>
			<?php
			$minwidth = 400;
			$size = sizeof($totalInvByDate)*40;
			if($size < $minwidth){
				$size = $minwidth;
			}
			?>

			<div id="dailydiv" style="width: <?php echo $size; ?>px">

			</div>
		</body>
			</html>

			<?php

		}
		else{
			echo "Unauthorised Access!";
		}
	}
}
?>
