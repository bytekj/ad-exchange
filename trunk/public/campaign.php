<?php
/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/
require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Content.php';
require_once '../Classes/UIForm.php';
require_once '../Classes/Adspot.php';
require_once '../Classes/Campaign.php';
require_once '../Classes/Advertiser.php';
require_once '../Db/DataHandler.php';
require_once '../include/global.php';

require 'header.php';

$objSession = new Session();

if ($objSession->isUserLoggedIn()) {
	//TODO show add upload form

	$objAdvertiser = new Advertiser();
	$objAdvertiser->getCurrentAdvertiserInfo();
}

if (isset($_GET['pauseid'])) {
	$id = $_GET['pauseid'];
	$objCampaign = new Campaign();
	$objCampaign->pauseCampaign($id);
	header('Location: campaign.php');
}
else if (isset($_GET['resumeid'])) {
	$id = $_GET['resumeid'];
	$objCampaign = new Campaign();
	$objCampaign->resumeCampaign($id);
	header('Location: campaign.php');
}
else if(isset($_GET['deleteid'])){
	$id = $_GET['deleteid'];
	$objCampaign = new Campaign();
	$objCampaign->updateCampaignStatus($id, 'delete');
	header('Location: campaign.php');
}
else if(isset($_GET['archiveid'])){
	$id = $_GET['archiveid'];
	$objCampaign = new Campaign();
	$objCampaign->updateCampaignStatus($id, 'archive');
	header('Location: campaign.php');
}
?>
<div class="page">
	<!--<div class="logo">
        <img width="auto" height="100%" src="<?php echo LOGO_URL . $objAdvertiser->brand_logo ?>"/>
    </div>-->
	<script>
function confirmDelete(delUrl, name) {
  if (confirm("Are you sure you want to delete "+name+"?")) {
    document.location = delUrl;
  }
}
</script>
	<h6>
		<a href="newcampaign.php">Create new campaign</a>
	</h6>
	<h5>Current campaigns</h5>

	<?php
	$objCampaign = new Campaign();
	//$currentCampaigns = $objCampaign->getByAdvertiser($objAdvertiser->userid);
	//by default the below function returns active campaigns
	$arrActiveCampaigns = $objCampaign->getActiveCampaignsByAdvertiserIdAndStatus($objAdvertiser->userid);

	if ($arrActiveCampaigns) {
		?>
	<table class="campaign">
		<tr>
			<th style="width: 220px;">Name</th>
			<th style="width: 100px;">Start date</th>
			<th style="width: 100px;">End date</th>
			<th style="width: 20px;">CPM($)</th>
			<th style="width: 80px;">Requested Impressions</th>
			<th style="width: 25px;">&nbsp;</th>
			<th style="width: 25px;">&nbsp;</th>
			<th style="width: 25px;">&nbsp;</th>
			<th style="width: 25px;">&nbsp;</th>
		</tr>
		<?php
		foreach ($arrActiveCampaigns as $campaign) {

			echo "<tr>";
			echo "<td><a href='showcampaign.php?id=" . $campaign['id'] . "'>" . $campaign['name'] . "</a></td>";
			echo "<td>" . $campaign['start_date'] . "</td>";
			echo "<td>" . $campaign['end_date'] . "</td>";
			echo "<td>" . $campaign['cpm'] . "</td>";
			echo "<td>" . number_format($campaign['ad_spots']) . "</td>";
			echo "<td><a href='reports.php?id='".$campaign['id']."'>Reports</a></td>"; 
			echo "<td><a href='campaign.php?pauseid=" . $campaign['id'] . "'>Pause</a></td>";
			echo "<td><a href='campaign.php?archiveid=" . $campaign['id'] . "'>Archive</a></td>";
			echo "<td><a href=\"javascript:confirmDelete('campaign.php?deleteid=" . $campaign['id'] . "', '".$campaign['name']."')\">Delete</a></td>";
			echo "</tr>";
		}
		?>
	</table>
	<?php
	} else {
		echo "<h6>No active campaigns!</h6>";
	}
	$arrPausedCampaigns = $objCampaign->getActiveCampaignsByAdvertiserIdAndStatus($objAdvertiser->userid, 'paused');

	if ($arrPausedCampaigns) {
		?>
	<h5>Paused campaigns</h5>
	<table class="campaign">
		<tr>
			<th style="width: 220px;">Name</th>
			<th style="width: 100px;">Start date</th>
			<th style="width: 100px;">End date</th>
			<th style="width: 20px;">CPM($)</th>
			<th style="width: 80px;">Requested Impressions</th>
			<th style="width: 25px;">&nbsp;</th>
			<th style="width: 25px;">&nbsp;</th>
			<th style="width: 25px;">&nbsp;</th>
			<th style="width: 25px;">&nbsp;</th>


		</tr>
		<?php
		foreach ($arrPausedCampaigns as $campaign) {

			/*
			 `id`,
			`advertiser_id`,
			`status`,
			`start_date`,
			`end_date`,
			`ad_content_id`,
			`genre`,
			`country`,
			`state`,
			`city`,
			`platform`
			*/
			echo "<tr>";
			echo "<td><a href='showcampaign.php?id=" . $campaign['id'] . "'>" . $campaign['name'] . "</a></td>";
			echo "<td>" . $campaign['start_date'] . "</td>";

			echo "<td>" . $campaign['end_date'] . "</td>";
			echo "<td>" . $campaign['cpm'] . "</td>";
			echo "<td>" . number_format($campaign['ad_spots']) . "</td>";
			//echo "<td><a href='showcampaign.php?id=" . $campaign['id'] . "'><image class=\"icon\" src=\"../CSS/icon/detail.png\"></a></td>";
			echo "<td><a href='reports.php?id='".$campaign['id']."'>Reports</a></td>"; 
			echo "<td><a href='campaign.php?resumeid=" . $campaign['id'] . "'>Resume</a></td>";
			echo "<td><a href='campaign.php?archiveid=" . $campaign['id'] . "'>Archive</a></td>";
			echo "<td><a href=\"javascript:confirmDelete('campaign.php?deleteid=" . $campaign['id'] . "','".$campaign['name']."')\">Delete</a></td>";
			echo "</tr>";
		}
		?>
	</table>
	<h5>Archived campaigns</h5>
	<table class="campaign">
	<?php
	$arrArchivedCampaigns = $objCampaign->getActiveCampaignsByAdvertiserIdAndStatus($objAdvertiser->userid, 'archived');
	foreach($arrArchivedCampaigns as $archivedCampaign){
		echo "<tr><td>".$archivedCampaign['name']."</td></tr>";
	}
		
	?>
	</table>
	
	<?php
	}
	?>
	
</div>
<?php
require 'footer.php';
?>