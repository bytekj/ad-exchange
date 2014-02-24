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
$objAdvertiser = new Advertiser();
$objAdvertiser->getCurrentAdvertiserInfo();


$id = NULL;
$message = "Reports";
$default_dates = true;
$start_date = NULL;
$end_date = NULL;

//_debug($_GET);

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

	$arrCampaign = array();
	//$objCampaign = new Campaign();

	$showStatsBool = false;

	
	$start_date_int = 9999999999;
	$end_date_int = 0;
	if ($objAdvertiser->usertype == "advertiser") {
		$ids = explode(",", $id);

		foreach ($ids as $key => $oneid) {
			$arrCampaign[$key] = new Campaign();
			$showStatsBool = $arrCampaign[$key]->getAdvertiserCampaignById($objAdvertiser->userid, $oneid);	

			if($start_date_int > strtotime($arrCampaign[$key]->start_date)){
				$start_date_int = strtotime($arrCampaign[$key]->start_date);
			}
			if($end_date_int < strtotime($arrCampaign[$key]->end_date)){
				$end_date_int = strtotime($arrCampaign[$key]->end_date);
			}
		}
		$start_date = date('Y-m-d', $start_date_int);
		$end_date = date('Y-m-d', $end_date_int);
	}

	if(strtotime($end_date) < strtotime($start_date)){
		$showStatsBool = false;
		echo "Invalid dates";
		exit;
	}
	//echo "<pre>";
	//print_r($objCampaign);
	//echo "</pre>";
	if ($showStatsBool == false) {
		$message = "Unauthorized access";
	} else {
		$message = "Reports";

		$objReport = new Reports();
		/*
		if($default_dates == true){
			$start_date = $objCampaign->start_date;
			$end_date = $objCampaign->end_date;

		}
		*/

		$totalImpressions = $objReport->CampaignGetTotalImpressionsByDate($arrCampaign[0]->advertiser_id,$id, $start_date, $end_date);

		$reqImpressions = $objReport->CampaignGetRequestedImpressions($id);
	}
} else {
	$message = "Unauthorized access";
}
?>
<script type="text/javascript">
function newPopup(url) {
	popupWindow = window.open(
		url,'popUpWindow','height=400,width=600,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=yes,location=no,directories=no,status=yes')
}
function repexport(url, name)
{
	window.target="_blank";
	window.open(url, name);
}
function change_dates(id){
	start_date = document.getElementById("start_datepicker").value;
	end_date = document.getElementById("end_datepicker").value;

	window.location = "adreports.php?id="+id+"&st_dt="+start_date+"&ed_dt="+end_date;
}


</script>
<script type="text/javascript">
$(function() {
	$( "#start_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#end_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
})
</script>
<div class="page">
	<?php 
	if($showStatsBool == true){
		?>
		<label style="width: 400px;">Campaign Performance</label><br> <br> <br>
		<div class="report_border report_info">
			<table style="width: 100%;">
				<tr>
					<td colspan="2">
						<input value="<?php echo $start_date; ?>" id="start_datepicker" onchange="JavaScript:change_dates('<?php echo $id; ?>')" type="text">to 
						<input value="<?php echo $end_date; ?>" id="end_datepicker" onchange="JavaScript:change_dates('<?php echo $id; ?>')" type="text">
						<!--<a href="JavaScript:change_dates(<?php echo $objCampaign->id; ?>)">Change Dates</a>-->
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table> 
							<?php
							foreach ($arrCampaign as $key => $oneCampaign) {
								echo "<tr><td>".$oneCampaign->name."</td><td>Status: ".$oneCampaign->status."</td><td>End date: ".$oneCampaign->end_date."</td><tr>"; 
							}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td colspan="2">Requested Impressions - <?php echo number_format($reqImpressions); ?></td>
				</tr>
				<tr>
					<td colspan="2">Impressions Consumed - <?php echo number_format($totalImpressions); ?></td>

				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td style="width: 300px;">
						<a href="JavaScript:newPopup('reports/daily.php?id=<?php echo $id ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">
							View Daily Distribution
						</a>
					</td>
					<td>
						<a href="JavaScript:repexport('reports/daily.php?exprt=1&id=<?php echo $id ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">Export</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="JavaScript:newPopup('reports/hourly.php?id=<?php echo $id ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">
							View Hourly Distribution
						</a>
					</td>
					<td><a
						href="JavaScript:repexport('reports/hourly.php?exprt=1&id=<?php echo $id ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">Export</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="JavaScript:newPopup('reports/geo.php?id=<?php echo $id ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">
							View Geographic Distribution
						</a>
					</td>
					<td>
						<a href="JavaScript:repexport('reports/geo.php?exprt=1&id=<?php echo $id ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>')">Export</a>
					</td>
				</tr>
				<?php
		/*
		<tr>
			<td colspan="2"><br> <br> <br> <label>Financial Reports</label><br> <br>
			</td>
		</tr>
		<tr>
			<td><a
				href="JavaScript:newPopup('reports/viewinvoice.php?id=<?php echo $objCampaign->id; ?>')">View
					Invoice</a></td>
		</tr>
		*/
		?>
	</table>
</div>
<?php
}
else{
	echo "Unauthorized access";
}
?>
</div>
