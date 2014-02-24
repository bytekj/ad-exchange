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
	if($objUser->usertype == 'publisher'){
		$objContent = new Content();
		$publisherContent = $objContent->checkPublisherContentByPublisherId($objUser->userid, $id);
		//_debug($publisherContent);

		if($publisherContent != false){
			//$objReport->
			
			$totalInvByDate = $objReport->getContentInventoryTotalByDateGrouped($id, $start_date, $end_date,'month');
			$soldInvByDate = $objReport->getContentInventorySoldByDateGrouped($id, $start_date, $end_date,'month');
			//_debug($totalInvByDate);
			//_debug($soldInvByDate);

			if(isset($_GET['exprt'])){
				header('Content-Disposition: attachment; filename='.$start_date."_".$end_date."_monthly.csv");
				echo "Report for\n";
				foreach ($publisherContent as $key => $oneContent) {
					echo ",".$oneContent['name']."\n";
				}
				echo "\n";

				echo "Month,Total Inventory, Inventory Sold\n";
				foreach ($totalInvByDate as $key => $value) {
					echo Utils::getMonth($value['month'])." ".$value['year'].",".$value['count'].",".$soldInvByDate[$key]['count']."\n";
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
						echo "data.addRow(['" . Utils::getMonth($data['month'])."', " . $data['count'] . " , ".$soldInvByDate[$key]['count']."]);";
					}
					?>

					var wrapper = new google.visualization.ChartWrapper({
						chartType: 'BarChart',
						dataTable: data,
						options: {'title': 'Monthly Inventory'},
						containerId: 'dailydiv',
						chartArea:{left:20,top:0,width:"100%",height:"100%"},
						yAxis:{minValue:0}
					});

					monthlydata = data;
					wrapper.draw();
				}
				google.load('visualization', '1');
				google.setOnLoadCallback(drawDailyChart);
				</script>
			</head>
			<body>
				<b><?php echo $objContent->name; ?> Monthly Inventory</b><br><br>
				<?php
				$minheight = 400;
				$height = 0;
				if(sizeof($totalInvByDate)*40 <400){
					$height = $minheight;
				}
				else{
					$height = sizeof($totalInvByDate)*40;
				}
			//echo $height;
				?>
				<div id="dailydiv" style="width:600px;height: <?php echo $height; ?>px;">

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