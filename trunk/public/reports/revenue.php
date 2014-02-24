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

if(isset($_GET['m'])){
	$month = $_GET['m'];
}
if(isset($_GET['y'])){
	$year = $_GET['y'];
}

if($objUserInfo->usertype == 'publisher'){

	$objReport = new Reports();
	$arrRevenue = $objReport->getPublisherRevenueByMonthYearGroupedByContent($objUserInfo->userid, $month, $year);
	$tmpRevenue = array();
	foreach ($arrRevenue as $key => $revenue) {
		$tmpRevenue[$key] = $revenue['revenue'];
	}

	array_multisort($tmpRevenue, SORT_DESC, $arrRevenue);
	//_debug($arrRevenue);
	/*
	[id] => 1
    [name] => zenga live tv
    [revenue] => 263.7550
	*/
    if($_GET['exprt']){
    	header('Content-Disposition: attachment; filename='.$month."_".$year."_revenue.csv");
    	echo "Channel,Revenue($)\n";
    	foreach ($arrRevenue as $revenue ){
    		echo $revenue['name'].",".$revenue['revenue']."\n";
    	}
    	exit;
    }
}
?>
<!DOCTYPE html>
<html>
<title>Channel Ratings</title>
<head>
	<link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
	<script type="text/javascript">
	function changeScreenSize(w,h)
	{
		window.resizeTo( w,h )
	}
	</script>
</head>
<body class='base' style="width:auto;" onload="changeScreenSize(500,500)">
	<div class='report_info report_border' style='margin-left:auto;margin-right:auto;width:400px;'>
		<b>Channel Wise Revenue Distribution</b><br><br>
		<table style='margin-left:auto;margin-right:auto;width:300px;'>
			<th align='left'>Channel</th><th>Revenue($)</th>
			<?php
			foreach ($arrRevenue as $key => $channelRevenue) {
				echo "<tr>";
				echo "<td>".$channelRevenue['name']."</td>";
				echo "<td align='right' style='padding-right:30px;'>".round($channelRevenue['revenue'],2)."</td>";
				echo "</tr>";
			}
			?>
		</table>
	</div>
</body>
</html>