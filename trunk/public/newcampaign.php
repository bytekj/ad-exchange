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
require_once '../Db/DataHandler.php';
require_once '../Classes/Ad.php';
require_once '../Classes/Utils.php';

require_once '../include/global.php';
require 'header.php';

if ($objSession->isUserLoggedIn()) {
	//TODO show add upload form
	$objUserInfo = new UserInfo();
	$objUserInfo->getCurrentUserInfo();
}


if ($_POST['submit']) {
	_debug($_POST);
	//exit;
	$objCampaign = new Campaign();
	$objCampaign->name = $_POST['name'];
	$objCampaign->description = "";
	$objCampaign->advertiser_id = $objUserInfo->userid;
	$objCampaign->start_date = $_POST['start_date'];
	$objCampaign->end_date = $_POST['end_date'];
	$objCampaign->genre = $_POST['genre'];
	$objCampaign->region = $_POST['region'];
	$objCampaign->city = $_POST['city'];
	$objCampaign->platform = $_POST['platform'];
	$objCampaign->status = 'active';
	$objCampaign->cpm = $_POST['cpm'];
	$objCampaign->ad_spots = $_POST['ad_spots'];
	$objcampaign->type = $_POST['adtype'];
	$_SESSION['objcampaign'] = $objCampaign;
	$campaignId = $objCampaign->add();
	
	echo "campaignid".$campaignId;
	
	$objAd = new Ad();
	if($objcampaign->type == 'Vast'){
		if ($objAd->handleVastUpload($campaignId, $_POST['vast_tag'])) {
			header('Location: campaign.php');
		}
	}
	else{
		if ($objAd->handleVideoUpload($campaignId)) {
			header('Location: campaign.php');
		}
	}
}
?>
<script type="text/javascript">
function initAdType(){
	document.getElementById('vast_tag').disabled = true;
	document.getElementById('enable_video').checked = true;
}
function chengeAdType(){
	if(document.getElementById('enable_video').checked){
		//document.getElementById('enable_vast').checked = true;
		document.getElementById('vast_tag').disabled = true;
		document.getElementById('video_file').disabled = false;
	}	
	else{
		//document.getElementById('enable_video').checked = true;
		document.getElementById('vast_tag').disabled = false;
		document.getElementById('video_file').disabled = true;
	}
}
</script>
<div class="page">
	<div class="campaignform">
		<h3>&nbsp;&nbsp;&nbsp;Create new campaign</h3>
		<form method="POST" action="" enctype="multipart/form-data">
			<?php
			$formObj = new UIForm();
			?>
			<table>
				<tr>
					<td>
						<div class="field">
							<?php
							echo $formObj->getElement('Name: ', 'label');
							echo $formObj->getElement('name', 'text');
							?>
						</div>
					</td>
				</tr>

			</table>
			<hr>
			<h4>&nbsp;&nbsp;&nbsp;&nbsp;Campaign dates</h4>
			<table>
				<tr>
					<td>
						<div class="field">
							<script>
							$(function() {
								$( "#start_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
							});
							</script>
							<?php
							echo $formObj->getElement('Start date: ', 'label');
							echo $formObj->getElement('start_date', 'text', '', '', 'start_datepicker');
							//echo "(YYYY-MM-DD)";
							?>
							<!--<input id="datepicker" type="text">-->
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="field">
							<script>
							$(function() {
								$( "#end_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
							});
							</script>
							<?php
							echo $formObj->getElement('End date: ', 'label');
							echo $formObj->getElement('end_date', 'text', '', '', 'end_datepicker');
							?>

						</div>
					</td>
				</tr>
			</table>
			<hr>
			<h4>&nbsp;&nbsp;&nbsp;&nbsp;Campaign parameters</h4>
			<table>
				<tr>
					<td>
						<div class="field">
							<?php
							echo $formObj->getElement('Genre: ', 'label');
							?>
							<script>
							function clearAll(select){
								for(k=1;k<select.options.length;k++)
								{
									select.options[k].selected = false;
								}
							}
							function clearfirst(select){
								select.options[0].selected = false;                                    
							}
							</script>
							<?php
							$arrGenre = Utils::getAllGenres();
							?>
							<select style="width: 200px;" multiple="multiple" name="genre[]">
								<option value="All" selected="selected"
								onclick="clearAll(this.parentNode)">All</option>

								<?php
								foreach ($arrGenre as $genre) {
									echo "<option value='" . $genre['genre'] . "' onclick='clearfirst(this.parentNode)'>" . $genre['genre_name'] . "</option>";
								}
								?>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="field">
							<?php
							echo $formObj->getElement('Region: ', 'label');
							$arrRegion = Utils::getAllRegions();
							?>
							<select style="width: 200px;" multiple="multiple" name="region[]">
								<option value="all" selected="selected"
								onclick="clearAll(this.parentNode)">All</option>
								<?php
								foreach ($arrRegion as $region) {
									echo "<option value='" . $region['region'] . "' onclick='clearfirst(this.parentNode)'>" . $region['region_name'] . "</option>";
								}
								?>
							</select>
						</div>
					</td>
				</tr>
				<!--
				<tr>
					<td>
						<div class="field">
							<?php
							echo $formObj->getElement('City', 'label');
							$arrCity = Utils::getAllCityTiers();
							?>
							<select style="width: 200px;" multiple="multiple" name="city[]">
								<option value="all" selected="selected"
								onclick="clearAll(this.parentNode)">All</option>
								<?php
								foreach ($arrCity as $city) {
									echo "<option value='" . $city['tier'] . "' onclick='clearfirst(this.parentNode)'>" . $city['tier_name'] . "</option>";
								}
								?>
							</select>

						</div>
					</td>
				</tr>
			-->
			<tr>
				<td>
					<div class="field">
						<?php
						echo $formObj->getElement('Platform: ', 'label');
						$arrPlatform = Utils::getAllPlatforms();
						?>
						<select style="width: 200px;" multiple="multiple"
						name="platform[]">
						<option value="all" selected="selected"
						onclick="clearAll(this.parentNode)">All</option>
						<?php
						foreach ($arrPlatform as $platform) {
							echo "<option value='" . $platform['platform'] . "' onclick='clearfirst(this.parentNode)'>" . $platform['platform_name'] . "</option>";
						}
						?>

					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="field">
					<?php
					echo $formObj->getElement('CPM ($)', 'label');
					echo $formObj->getElement('cpm', 'text');
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="field">
					<?php
					if ($objUserInfo->userid == 'istream') {
						echo $formObj->getElement('Minutes', 'label');
					}
					else{
						echo $formObj->getElement('Ad spots', 'label');
					}
					echo $formObj->getElement('ad_spots', 'text');
					?>
				</div>
			</td>
		</tr>
	</table>

	<hr>

	<table>
		<tr>
			<td style='width:100px'><b>Ad Type</b></td>
			<td>
				<input type="radio" name="adtype" value="Video" id='enable_video' onChange='JavaScript:chengeAdType();'>Video&nbsp;&nbsp;&nbsp;&nbsp;<input type='file' name='video' id='video_file'><br>
				<input type="radio" name="adtype" value="Vast" id='enable_vast' onChange='JavaScript:chengeAdType();'>Vast Tag Url&nbsp;&nbsp;<input style='width:200px;' type='text' name='vast_tag' id='vast_tag'> 
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<?php
				echo "<br><br>";
				echo $formObj->getElement('submit', 'submit', 'Submit');
				?>

			</td>
		</tr>
	</table>
	<script type="text/javascript">
	initAdType();
	</script>
</form>
</div>
</div>
<?php require 'footer.php'; ?>
