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

new Session();

$objUserInfo = new UserInfo();
$objUserInfo->getCurrentUserInfo();

$arrRevenue = array();
$month = date('m');
$year = date('Y');
$start_date = '';
$end_date = '';
$report_type = '';
$id = '';

if(isset($_GET['m'])){
	$month = $_GET['m'];
}
if(isset($_GET['y'])){
	$year = $_GET['y'];
}

if(isset($_GET['type'])){
	
	if($_GET['type'] == 'impr'){
		$report_type = 'impr';
	}
	else if($_GET['type'] == 'revenue'){
		$report_type = 'revenue';
	}
	else if($_GET['type'] == 'inv'){
		$report_type = 'inv';
	}
	else{
		echo 'Invalid Request';
		exit;
	}
}
if(isset($_GET['st_dt'])){
	$start_date = $_GET['st_dt'];
}
if(isset($_GET['ed_dt'])){
	$end_date = $_GET['ed_dt'];
}
if(isset($_GET['id'])){
	$id = $_GET['id'];
}

if($objUserInfo->usertype == 'publisher'){
	
	$objReport = new Reports();
	$objContent = new Content();
	$publisherContent = $objContent->checkPublisherContentByPublisherId($objUserInfo->userid, $id);
	if($publisherContent != false){
		if($report_type == 'inv'){

			$totalInvByDevice = $objReport->getPublisherInventoryTotalGroupedByDevice($objUserInfo->userid, $start_date, $end_date, $id);
			$soldInvByDevice = $objReport->getPublisherInventorySoldGroupedByDevice($objUserInfo->userid, $start_date, $end_date, $id);
			
			//_debug($totalInvByDevice);
			//_debug($soldInvByDevice);
			//exit;
			if(isset($_GET['exprt'])){
				header("Content-Disposition: attachment; filename=".$start_date."_".$end_date."_platform_impressions.csv");
				echo "Report for\n";
				foreach ($publisherContent as $key => $oneContent) {
					echo ",".$oneContent['name']."\n";
				}
				echo "\n";

				echo "Platform wise Distribution of Total Inventory\n";
				echo "Platform, Inventory\n";
				foreach ($totalInvByDevice as $key => $value) {
					echo $value['device'].",".$value['count']."\n";
				}

				echo "\n\nPlatform wise Distribution of Sold Inventory\n";
				echo "Platform, Inventory\n";
				foreach ($soldInvByDevice as $key => $value) {
					echo $value['device'].",".$value['count']."\n";
				}

				exit();
			}
			?>
			<html>
			<head>
				<link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
				<script type="text/javascript" src="http://www.google.com/jsapi"></script>
				<script type="text/javascript">
				function drawTotalInventoryChart() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Device');
					data.addColumn('number', 'Count');
					<?php
					foreach ($totalInvByDevice as $key => $data) {
						echo "data.addRow(['" . $data['device'] ."', " . $data['count'] . "]);";
					}
					?>

					var wrapper = new google.visualization.ChartWrapper({
						chartType: 'PieChart',
						dataTable: data,
						options: {'title': 'Total Inventory Distribution by Device'},
						containerId: 'deviceTotalDiv',
						chartArea:{left:20,top:0,width:"100%",height:"120%"},
						vAxis:{minValue:0}
					});

					monthlydata = data;
					wrapper.draw();
				}
				function drawSoldInventoryChart() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Device');
					data.addColumn('number', 'Count');
					<?php
					foreach ($soldInvByDevice as $key => $data) {
						echo "data.addRow(['" . $data['device'] ."', " . $data['count'] . "]);";
					}
					?>

					var wrapper = new google.visualization.ChartWrapper({
						chartType: 'PieChart',
						dataTable: data,
						options: {'title': 'Sold Inventory Distribution by Device'},
						containerId: 'deviceSoldDiv',
						chartArea:{left:20,top:0,width:"100%",height:"120%"},
						vAxis:{minValue:0}
					});

					//monthlydata = data;
					wrapper.draw();
				}
				google.load('visualization', '1');
				google.setOnLoadCallback(drawTotalInventoryChart);
				google.setOnLoadCallback(drawSoldInventoryChart);
				</script>
			</head>
			<body>
				<b><?php echo $objContent->name; ?> Inventory</b><br><br>
				<?php
				$minwidth = 400;
				$size = sizeof($totalInvByDevice)*40;
				if($size < $minwidth){
					$size = $minwidth;
				}
				?>
				<div id="deviceTotalDiv" style="width: <?php echo $size; ?>px"></div>
				<div id="deviceSoldDiv" style="width: <?php echo $size; ?>px"></div>
			</body>
			</html>

			<?php

		}
		else if($report_type == 'revenue'){
			$revenueByDev = $objReport->getPublisherRevenueByMonthYearGroupedByDevice($objUserInfo->userid, $month, $year);
			//_debug($res);
			if(isset($_GET['exprt'])){
				header("Content-Disposition: attachment; filename=".$month."_".$year."_platform_revenue.csv");
				echo "Report for\n";
				foreach ($publisherContent as $key => $oneContent) {
					echo ",".$oneContent['name']."\n";
				}
				echo "\n";

				echo "Platform wise Distribution of Revenue\n";
				echo "Platform, Revenue($)\n";
				foreach ($revenueByDev as $key => $value) {
					echo $value['device'].",". round($value['revenue'], 1)."\n";
				}

				exit();
			}
			?>
			<html>
			<head>
				<link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
				<script type="text/javascript" src="http://www.google.com/jsapi"></script>
				<script type="text/javascript">
				function drawDeviceRevenueChart() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Device');
					data.addColumn('number', 'Count');
					<?php
					foreach ($revenueByDev as $key => $data) {
						echo "data.addRow(['" . $data['device'] ."', " . $data['revenue'] . "]);";
					}
					?>

					var wrapper = new google.visualization.ChartWrapper({
						chartType: 'PieChart',
						dataTable: data,
						options: {'title': 'Revenue Distribution by Platform'},
						containerId: 'deviceRevenueDiv',
						chartArea:{left:20,top:0,width:"100%",height:"120%"},
						vAxis:{minValue:0}
					});

					//monthlydata = data;
					wrapper.draw();
				}
				google.load('visualization', '1');
				google.setOnLoadCallback(drawDeviceRevenueChart);
				</script>
			</head>
			<body>
				<b>Revenue distribution</b><br><br>
				<?php
				$minwidth = 400;
				$size = sizeof($totalInvByDevice)*40;
				if($size < $minwidth){
					$size = $minwidth;
				}
				?>
				<div id="deviceRevenueDiv" style="width: <?php echo $size; ?>px"></div>
				<font size='small'>(Revenue in USD)</font>
			</body>
			</html>
			<?php
		}
		
	}
	else{
		echo "Unauthorised Access!";
	}
	
}
?>