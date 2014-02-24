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

		$objCampaign->getAdvertiserCampaignById($objUser->userid, $id);
		
		$topCountryData = $objReport->CampaignReportGetTopCountryData($id, 50, $start_date, $end_date);

		$topCityData = $objReport->CampaignReportGetTopCityData($id, 50, $start_date, $end_date);
		
		_debug($topCountryData);
		
		if($_GET['exprt']){
			header('Content-Disposition: attachment; filename='.$objCampaign->name."_".$start_date."_".$end_date."_geo.csv");
			
			$ids = explode(",", $id);

			echo "Campaign,Start Date, End Date\n";
			foreach ($ids as $key => $oneid) {
				$objCampaign->getAdvertiserCampaignById($objUser->userid, $oneid);		
				echo $objCampaign->name.",".$objCampaign->start_date.",".$objCampaign->end_date."\n";
			}

			echo "\n\nCountry,Impressions\n";
			foreach ($topCountryData as $data ){
				echo str_replace(",", "-", $data['country']).",".$data['count']."\n";
			}
			echo "\n\n";
			echo "City,Impressions\n";
			foreach ($topCityData as $data ){
				echo str_replace(",", "-", $data['city']).",".$data['count']."\n";
			}
			exit;
		}
	}
}
?>
<html>
<head>
	<link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
	<script type="text/javascript">
	function newPopup(url) {
    //popupWindow = window.open(url,'_blank','height=400,width=600,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no,status=yes');
    window.open(url,'_blank','location=no,directories=no,status=yes,width=600,scrollbars=yes');
    
}
</script>
</head>
<body>
	<div>
		<table>
			<tr>
				<td class="report" valign="top" align="center">Countries
					<table class="smallreport">
						<!-- <a href="JavaScript:newPopup('http://www.quackit.com/html/html_help.cfm');">Open a popup window</a> -->
						<tr>
							<th>Country</th>
							<th>Impressions</th>
						</tr>
						<?php
						foreach ($topCountryData as $coutryData) {
							//echo '<tr><td>' . $coutryData['country'] . '</a></td>';
							echo '<tr><td><a href="JavaScript:newPopup(\'http://' . $_SERVER['SERVER_NAME'] . '/adex/public/reports/countryreport.php?country=' . $coutryData['country'] . '&camp=' . $id .'&st_dt='.$start_date.'&ed_dt='.$end_date. '\');">' . $coutryData['country'] . '</a></td>';
							echo "<td class='report_numbers'>" . $coutryData['count'] . "</td></tr>";
						}
						?>
						
					</table>
				</td>
				<td class="report" valign="top" align="center">Cities
					<table class="smallreport">
						<tr>
							<th>City</th>
							<th>Impressions</th>

						</tr>
						<?php
						foreach ($topCityData as $cityData) {
							echo "<tr><td>" . $cityData['city'] . "</td><td class='report_numbers'>" . $cityData['count'] . "</td></tr>";
						}
						?>
					</table>
				</td>
			</tr>
		</table>

	</div>

</body>
</html>
