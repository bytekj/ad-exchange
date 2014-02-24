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


require_once 'header.php';
$id = $_GET['id'];

$objUserInfo = new UserInfo();
$objUserInfo->getCurrentUserInfo();
$objCampaign = new Campaign();

$month = '';
$year = '';
if ($objUserInfo->usertype == "advertiser") {
	$showStatsBool = $objCampaign->getAdvertiserCampaignById($objUserInfo->userid, $id);
}
if($objUserInfo->usertype == 'publisher'){
	if($_POST){
		$month = $_POST['month'];
		$year = $_POST['year'];
	}
	else{
		$month = date('m');
		$year = date('Y');
	}
	//_debug($_POST);

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
	<b>Financial Reports</b><br> <br> 
	<?php
	if($objUserInfo->usertype == 'advertiser'){
		?>
		<a
		href="JavaScript:newPopup('reports/viewinvoice.php?id=<?php echo $objCampaign->id; ?>')">View
		Invoice</a>

		<?php
	}
	else if($objUserInfo->usertype == 'publisher'){
		
		?>
		Revenue For 
		<form name='revenue' method='post'>
			<select name='month' onchange='this.form.submit();'>
				<?php
				for($i=1; $i<=12; $i++){
					$intMonth = $i;
					if($i<10){
						$intMonth = '0'.$i;
					}
					if($i == $month){
						echo "<option value='".$intMonth."' selected='selected'>".Utils::getMonth($i)."</option>";	
					}
					else{
						echo "<option value='".$intMonth."'>".Utils::getMonth($i)."</option>";	
					}

				}

				?>
			</select> 
			<select name='year' onchange='this.form.submit()'>
				<?php
				//echo "here";

				$years = Utils::getPrevYears();
				//_debug($years);
				foreach ($years as $key => $oneyear) {
					if($key == 0){
						echo "<option value='".$oneyear['YEAR']."' selected='selected'>".$oneyear['YEAR']."</option>";
					}
				}
				?>
			</select>
		</form>
		<?php
		$objReport = new Reports();
		//_debug($year);
		$totalRevenue = $objReport->getPublisherRevenueByMonthYearTotal($objUserInfo->userid, $month, $year);

		?>
		<div class='report_info report_border'>
			<table>
				<tr>
					<td style="width:200px">Revenue For The Month: </td><td>$ <?php echo round($totalRevenue, 1); ?></td>
				</tr>
				<tr><td colspan='2'><hr></td></tr>
				<tr>
					<td><a href="JavaScript:newPopup('reports/revenue.php?m=<?php echo $month; ?>&y=<?php echo $year; ?>')"><b>Channel Wise Distribution</b></a></td>
					<td><a href="JavaScript:repexport('reports/revenue.php?exprt=1&m=<?php echo $month ?>&y=<?php echo $year; ?>')">Export</a></td>
				</tr>
				<tr>
					<td><a href="JavaScript:newPopup('reports/platform.php?type=revenue&id=<?php echo $id; ?>&m=<?php echo $month; ?>&y=<?php echo $year; ?>')"><b>Platform Wise Distribution</b></a></td>
					<td><a href="JavaScript:repexport('reports/platform.php?type=revenue&id=<?php echo $id; ?>&exprt=1&m=<?php echo $month ?>&y=<?php echo $year; ?>')">Export</a></td>
				</tr>
			</table>
		</div>
		<?php
	}
	?>
</div>
<?php
require_once 'footer.php';
?>