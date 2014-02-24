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
require_once '../Classes/Utils.php';
require 'header.php';

$message = '';

$campaignId = $_GET['id'];
$objUserInfo = new UserInfo();
$objUserInfo->getCurrentUserInfo();

$objCampaign = new Campaign();

$showCampaignBool = $objCampaign->getAdvertiserCampaignById($objUserInfo->userid, $campaignId);
//echo "<pre>";
//print_r($objCampaign);
//echo "</pre>";
if ($showCampaignBool == false) {
	$message = "Unauthorized access";
}
$allGenre = Utils::getAllGenres();
$allRegion = Utils::getAllRegions();
$allCities = Utils::getAllCityTiers();
$allPlatforms = Utils::getAllPlatforms();
?>
<script type="text/javascript">
<!--

//-->
function newPopup(url) {
    //popupWindow = window.open(url,'_blank','height=400,width=600,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no,status=yes');
    window.open(url,'_blank','location=no,directories=no,status=yes,width=320,height=240,scrollbars=yes');
    
}

function updateTags(){
    var XHR = new XMLHttpRequest();
    var tags = document.getElementById('tags');
    XHR.open("GET", "updatetags.php?<?php echo "campid=".$campaignId ?>&tags="+tags.value, true);
    XHR.send();
   
   
}
$(function(){
    $("#update_tags").click(updateTags);
});
</script>
<div class="page">

	<h6>
		<?php
		echo $message
		?>
	</h6>
	<?php
	if ($showCampaignBool) {
        ?>

	<table style="width: 100%;">
		<tr>
			<td style="width: 310px"><label style="width: 305px"> <?php echo $objCampaign->name; ?>
			</label>
			</td>
			<td><a href="editcampaign.php?id=<?php echo $objCampaign->id; ?>">Edit Parameters</a>
			</td>
		</tr>
	</table>

	<table class="campaign">
		<tr>
			<td style="width: 300px;">Status</td>
			<td><?php echo $objCampaign->status ?></td>

		</tr>
		<tr>
			<td>Start date</td>
			<td><?php echo $objCampaign->start_date ?></td>
		</tr>
		<tr>
			<td>End date</td>
			<td><?php echo $objCampaign->end_date; ?></td>

		</tr>
		<tr>
			<td><a
				href="JavaScript:newPopup('viewad.php?id=<?php echo $objCampaign->id; ?>')">View
					Ad</a>
			</td>
			<td>&nbsp;</td>
		</tr>

	</table>

	<hr>

	<label>CPM and ad spots</label>
	<table class="campaign">
		<tr>
			<td style="width: 300px;"><?php
			if ($objUserInfo->userid == 'istream') {
                        echo "Minutes";
                    } else {
                        echo "Ad spots";
                    }
                    ?>
			</td>
			<td><?php echo number_format($objCampaign->ad_spots) ?></td>

		</tr>
		<tr>
			<td>CPM</td>
			<td><?php echo "$" . $objCampaign->cpm ?></td>

		</tr>
	</table>
	<hr>
	<label>Campaign parameters</label>

	<table class="campaign parameters">
		<tr>
			<td valign="top" style="width: 300px;">Genre</td>
			<td><?php
			foreach ($objCampaign->genre as $key => $genre) {
                    $delim = ", ";

                    //echo $key." ".sizeof($objCampaign->genre);
                    if ($key + 1 == sizeof($objCampaign->genre)) {
                        $delim = "";
                    }
                    foreach ($allGenre as $onegenre) {
                        if ($onegenre['genre'] == $genre) {
                            $genre = $onegenre['genre_name'];
                            break;
                        }
                    }
                    echo $genre . $delim;
                }
                ?>
			</td>
		</tr>
		<tr>
			<td valign="top">Region</td>
			<td><?php
			foreach ($objCampaign->region as $key => $region) {
                    $delim = ", ";
                    if ($key + 1 == sizeof($objCampaign->region)) {
                        $delim = "";
                    }
                    //echo $key." ".sizeof($objCampaign->genre);
                    foreach ($allRegion as $oneRegion) {
                        if ($oneRegion['region'] == $region) {
                            $region = $oneRegion['region_name'];
                            break;
                        }
                    }
                    echo $region . $delim;
                }
                ?>
			</td>
		</tr>
		<!--
		<tr>
			<td valign="top">City</td>
			<td><?php
			foreach ($objCampaign->city as $key => $city) {
                    $delim = ", ";
                    if ($key + 1 == sizeof($objCampaign->city)) {
                        $delim = "";
                    }

                    foreach ($allCities as $oneCity) {
                        if ($oneCity['tier'] == $city) {
                            $city = $oneCity['tier_name'];
                            break;
                        }
                    }
                    echo $city . $delim;
                }
                ?>
			</td>
		</tr>
		-->
		<tr>
			<td valign='top'>Platform</td>
			<td><?php
			foreach ($objCampaign->platform as $key => $platform) {
                    $delim = ", ";
                    if ($key + 1 == sizeof($objCampaign->platform)) {
                        $delim = "";
                    }
                    foreach ($allPlatforms as $onePlatform) {
                        if ($onePlatform['platform'] == $platform) {
                            $platform = $onePlatform['platform_name'];
                            break;
                        }
                    }
                    echo $platform . $delim;
                }
                ?>
			</td>
		</tr>
		<tr>
			<td>
				<!-- <input id="tags" type="text" value="<?php echo $objCampaign->getCampaignTagsById(); ?>"/><input type="button" id="update_tags" value="submit"/> -->
			</td>
		</tr>
	</table>
	<hr>
	<label style="width: 300;">Campaign Reports</label>
	<table class="campaign">
		<tr>
			<td><a href="adreports.php?id=<?php echo $objCampaign->id; ?>">Performance
					Report</a></td>
		</tr>
		<tr>
			<td><a href="finreport.php?id=<?php echo $objCampaign->id; ?>">Finance
					Report</a></td>
		</tr>
	</table>
	<div style="float: right;">
		<?php
		
		
        }
        ?>

	</div>
</div>


<?php
require 'footer.php';
?>
