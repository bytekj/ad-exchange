<?php
require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Content.php';
require_once '../Classes/UIForm.php';
require_once '../Classes/Adspot.php';
require_once '../Classes/Campaign.php';
require_once '../Classes/Advertiser.php';
require_once '../Db/DataHandler.php';
require_once '../include/global.php';
require_once '../Classes/Publisher.php';

require 'header.php';

//$objUserInfo = NULL;
if ($objSession->isUserLoggedIn()) {
	$message = '';
	if(isset($_GET['id'])){
		$contentId = $_GET['id'];	
	}
	
	$objPublisher = new Publisher();
	$objPublisher->getCurrentPublisherInfo();

	$objContent = new Content();

	$mode = NULL;
	$profile_id = NULL;
	if(isset($_GET['mode'])){
		$mode = $_GET['mode'];
		$profile_id = $_GET['profid'];
	}
	/*
	echo "<pre>";
	print_r($_GET);
	echo "</pre>";
	*/
	if($mode == "Add"){
		//delete the content profile
		$objContent->AddProfileToContent($contentId, $profile_id);
		header("Location: addprofile.php?id=".$contentId);
	}
	else if($mode == "delete"){
		//add to content profiles
		$objContent->RemoveProfileFromContent($contentId, $profile_id);

		header("Location: addprofile.php?id=".$contentId);
	}

	$contents = $objContent->getPublisherContentByPublisherId($objPublisher->userid, $contentId);
	if ($_GET['debug'] ==1) {
		echo "<pre>";
		print_r($contents);
		echo "</pre>";
	}
	
}

?>

<div class="page">
	<label style="width: 300px;"><?php echo $objContent->name; ?> Current profiles</label> <br>
	<table class="content">
		<tr>
			<th>Profile</th>
			<th>Resolution</th>
			<th>Video Bit-rate</th>
			<th>Video codec</th>
			<th>Video FPS</th>
			<th>Audio bit-rate</th>
			<th>Audio sampling rate</th>
			<th>Audio codec</th>
			<th>Audio channels</th>
			<th>&nbsp;</th>
		</tr>
		<?php
		
		
		foreach ($contents as $content) {
			echo "<tr>";
			echo "<td>" . $content['p_name'] . "</td>";
			echo "<td>" . $content['vid_res'] . "</td>";
			echo "<td>" . $content['vid_bit_rate'] . "</td>";
			echo "<td>" . $content['vid_codec'] . "</td>";
			echo "<td>" . $content['vid_fps'] . "</td>";
			echo "<td>" . $content['aud_bit_rate'] . "</td>";
			echo "<td>" . $content['aud_rate'] . "</td>";
			echo "<td>" . $content['aud_codec'] . "</td>";
			echo "<td>" . $content['aud_ch'] . "</td>";
			echo "<td><a href=\"addprofile.php?id=".$contentId."&mode=delete&profid=".$content['ppid']."\">Remove</a></td>";
			echo "</tr>";
		}
		?>
	</table>
	<?php
	$arrMissingProf = $objContent->getPubliserProfilesNotAddedToContent($contentId, $objPublisher->userid);
	if($arrMissingProf){
	?>

	<hr>
	<h6>Assign More profiles to the content</h6>
	<?php
	//show those profiles that are not added to the content yet
	

	/*
	echo "<pre>";
	print_r($arrMissingProf);
	echo "</pre>";
	*/
	?>
	<form name="profiles" method="GET" action="">
		<input type="hidden" name="id" value="<?php echo $contentId; ?>">
		<table class="genform" style="width:600px">
			<tr>
				<td valign="top">Select one or more profiles</td>
				<td>
					<select multiple="multiple" name="profid[]">
						<?php 
						foreach ($arrMissingProf as $key => $missingProf) {
							echo "<option value=".$missingProf['profile_id'].">".$missingProf['profile_name']."</option>";
						}
						?>
					</select>
				</td>
				<td valign="top">
					<input class="button" type="submit" name="mode" value="Add">
				</td>
			</tr>
		</table>		
	</form>
	<?php
	}
	?>
</div>
<?php
require 'footer.php'; 
?>