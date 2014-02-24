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
require_once '../Classes/Publisher.php';
require_once '../Classes/Content.php';
require_once '../Classes/Reports.php';
require_once '../Classes/Utils.php';

require 'header.php';

$objUserInfo = new UserInfo();
$objUserInfo->getCurrentUserInfo();
$objContent = new Content();
$showContentReportBool = true;

$id = "";

$totalInv = "";
$soldInv = "";

if (isset($_GET['id'])) {

	//id comes in get when user is first time redirected to this page
	$id = $_GET['id'];
	if(isset($_GET['st_dt']) && isset($_GET['ed_dt'])){
		$start_date = $_GET['st_dt'];
		$end_date = $_GET['ed_dt'];
	}
	else{
		$start_date = date('Y-m-').'01';
		$end_date = date('Y-m-d');
	}
	$publisherContent = $objContent->checkPublisherContentByPublisherId($objUserInfo->userid, $id);
	//echo "here";
	//_debug($result);
	if ($publisherContent) {
		$objReport = new Reports();
		$objData = new DataHandler();
		$showContentReportBool = true;
		//_debug($result);
		$totalInv = $objReport->getContentInventoryTotalByDate($objUserInfo->id, $id, $start_date, $end_date);
		//_debug($totalInv);
		$soldInv = $objReport->getContentInventorySoldByDate($objUserInfo->id, $id, $start_date, $end_date);

	} else {
		$showContentReportBool = false;
	}
}
if ($showContentReportBool) {
	$message = "Reports";
} else {
	$message = "Unauthorized access";
}

?>
<div class="page">
	<script type="text/javascript">
	function newPopup(url) {
		popupWindow = window.open(
			url,'popUpWindow','height=600,width=600,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no,status=yes')
	}
	function repexport(url, name)
	{
		window.target="_blank";
		window.open(url, name);
	}
	</script>
	<b>Inventory & Performance Report 
		<?php foreach ($publisherContent as $key => $oneContent) {
			$delim = ",";
			if(sizeof($publisherContent) == $key+1){
				$delim = "";
			}
			echo $oneContent['name']." ".$delim;
		} ?>
	</b>
	<div class="report_border report_info">

		Starting: <?php echo $start_date; ?> Ending: <?php echo $end_date; ?><br><br>
		<table>
			<tr>
				<td>Total Inventory: <?php echo number_format($totalInv); ?> Impressions</td><td>Inventory sold: <?php echo number_format($soldInv);  ?> Impressions</td>
			</tr>
			<tr>
				<td colspan='2'><hr></td>
			</tr>
			<tr>
				<td style="width:300px;">
					<a href="JavaScript:newPopup('reports/daily.php?id=<?php echo $id; ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">
						<b>Daily Inventory & Performance</b>
					</a>
				</td>
				<td>
					<a href="JavaScript:repexport('reports/daily.php?id=<?php echo $id; ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>&exprt=1')">Export</a>
				</td>
			</tr>
			<tr>
				<td><a href="JavaScript:newPopup('reports/monthly.php?id=<?php echo $id; ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">
					<b>Monthly Inventory & Performance</b>
				</a></td>
				<td>
					<a href="JavaScript:repexport('reports/monthly.php?id=<?php echo $id; ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>&exprt=1')">Export</a>
				</td>
			</tr>
			<tr>
				<td><a href="JavaScript:newPopup('reports/platform.php?type=inv&id=<?php echo $id; ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">
					<b>Platform wise Distribution of Inventory</b>
				</a></td>
				<td>
					<a href="JavaScript:repexport('reports/platform.php?type=inv&id=<?php echo $id; ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>&exprt=1')">Export</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href="JavaScript:newPopup('reports/rating.php?id=<?php echo $id; ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">
						<b>Channel Rating</b>
					</a>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
require 'footer.php';
?>
