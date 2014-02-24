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
require_once '../Classes/Publisher.php';
require_once '../Classes/Reports.php';
require_once '../Classes/Utils.php';

require 'header.php';
$objSession = new Session();

$streamAddError = "";

//$objUserInfo = NULL;
if ($objSession->isUserLoggedIn()) {
	$message = '';

	$contentId = $_GET['id'];
	$objPublisher = new Publisher();
	$objPublisher->getCurrentPublisherInfo();

	$objContent = new Content();
	$arrStreams = array();
	$contents = $objContent->getPublisherContentByPublisherIdWithoutprofs($objPublisher->userid, $contentId);
	if ($_GET['debug'] ==1) {
		echo "<pre>";
		print_r($contents);
		echo "</pre>";
	}

	if($contents != false){
		$arrStreams = $objContent->getStreams($objPublisher->userid, $objContent->id);
	}
	if(isset($_POST['Submit'])){
		//
		$mode = $_POST['Submit'];
		
		if($mode == "Add"){
			
			$name = $_POST['name'];
			$sourceIp = $_POST['source_ip'];
			$path = $_POST['path'];
			$wowzaIp = $_POST['wowza_ip'];
			$stream_status = $_POST['s_status'];

			if($name == "" || $sourceIp == "" ||$path == "" || $wowzaIp == ""|| $stream_status == "")
			{
				$message = "Error adding stream! All the fields are compulsary";
			}
			else{
				$ret = $objContent->addStream($objPublisher->userid, $name,$sourceIp, $path, $wowzaIp, $stream_status);
				if($ret == true)
				{
					header("Location: showcontent.php?id=".$objContent->id);
				}
				else{
					$streamAddError = $ret;
				}
			}
		}
	}
	else if(isset($_POST['s_status'])){
		$status = $_POST['s_status'];
		$stream = $_POST['stream'];

		if($objContent->updateStreamStatus($objPublisher->userid, $objContent->id, $stream, $status)){
			header("Location: showcontent.php?id=".$objContent->id);
		}
	}
	else if(isset($_POST['status'])){
		$status = "Disable";
		if($_POST['status'] == "Activate"){
			$status = "Enable";
		}

		foreach ($arrStreams as $key => $oneStream) {
			$objContent->updateStreamStatus($objPublisher->userid,$objContent->id, $oneStream['name'], $status);
		}
		/*
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		*/
		header("Location: showcontent.php?id=".$objContent->id);
	}
	else if(isset($_POST['delete'])){

		_debug($_POST);
		$objContent->deleteChannel($contentId);
		header("Location: content.php");
	}
	
	/*

	if ($_POST['submit']) {

	//          echo "<pre>";
	//          print_r($_POST);
	//          echo "</pre>";

		$objContent->updateAdFreq($contents[0]['id'], $_POST['freq']);
		header("Location: " . $_SERVER["REQUEST_URI"]);
	}
	if($_POST['preroll']){
		$objContent->updatePrerollOption($contents[0]['id'], $_POST['preroll']);
		header("Location: " . $_SERVER["REQUEST_URI"]);
	}

	*/
	if ($contents == false) {
		$message = "Unauthorized access";
	}
}
?>
<script type="text/javascript">
function confirmDelete(name) {
	if (confirm("Are you sure you want to delete "+name+"?")) {
		return true;
	}
	else 
		return false;
}
function autoSubmit(key,stream)
{
	console.log(stream);
	var formObject = document.forms['change_status_'+key];
	formObject.submit();
}

function updateTags(){
	var XHR = new XMLHttpRequest();
	var tags = document.getElementById('tags');
	XHR.open("GET", "updatetags.php?<?php echo "conid=".$contentId; ?>&tags="+tags.value, true);
	XHR.send();
}
$(function(){
	$("#update_tags").click(updateTags);
});

</script>
<div class="page">
	<?php
	//    echo "<pre>";
	//    print_r($contents);
	//    echo "</pre>";
	if ($contents != false) {

		$channelStatus = 0;
		foreach ($arrStreams as $key => $stream) {
			if($stream['chstatus'] == 1){
				$channelStatus = 1;
				break;
			}
		}


		?>
		<b><?php echo $contents[0]['name'] ?></b><br>
		<!-- inventory details and active deactive status -->

		<table class="content" style="width:500px">
			<?php /*
			<tr>
				<td>Name</td>
				<td><?php echo $contents[0]['name'] ?></td>
			</tr>
			 */ 
			?>
			<tr>
				<td>Status</td>
				<td><form name="channel_status" method="post" action="">
					<?php  
					$btName = "";
					if($channelStatus == 1){
						echo "Active";
						$btName = "Deactivate";
					}
					else{
						echo "Deactive";
						$btName = "Activate";
					}
					?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="submit" name="status" value="<?php echo $btName; ?>" class="button">
				</form>
			</td>
		</tr>
		<?php
		$objReport = new Reports();
		$totalInv = $objReport->getContentInventoryTotal($objPublisher->id, $objContent->id);
		$soldInv = $objReport->getContentInventorySold($objPublisher->id, $objContent->id);
		?>
		<tr>
			<td>Current Month Total Inventory</td>
			<td><?php echo $totalInv; ?> Impressions</td>
		</tr>
		<tr>
			<td>Current Month Inventory Sold</td>
			<td><?php echo $soldInv; ?> Impressions</td>
		</tr>
		<tr>
			<td>Genre</td>
			<td><?php
			//$arrGenre = explode(",",$contents[0]['genre']) ;
			echo $contents[0]['genre'];
			?></td>
		</tr>
		<?php
		/*
		<tr style="height: 50px;">
			<td><label style="width: 160px;">Ad frequency(minutes)</label></td>
			<td>
				<form name="change_ad_freq" method="POST" onsubmit="">
					<table style="font-size: 12px;">

						<tr>
							<td><?php echo $contents[0]['ad_freq'] ?>
							</td>
							<td><input name="freq" type="text" /></td>
							<td><input name="submit" value="change" type="submit"
								style="width: 70px">
							</td>
						</tr>

					</table>
				</form>
			</td>
		</tr>
		
		<tr>
			<td><label>Preroll</label>
			</td>
			<td><script type="text/javascript">
                        function autoSubmit()
                        {
                            var formObject = document.forms['change_preroll'];
                            formObject.submit();
                        }

                    </script>
				<form name="change_preroll" id="change_preroll" method="POST"
					onsubmit="">
					<table style="font-size: 12px;">
						<tr>
							<td><?php
							$enabled = "";
							$disabled = "";

							if ($contents[0]['preroll'] == 1) {
								$enabled = "checked";
								$disabled = "";
							} else {
								$disabled = "checked";
								$enabled = "";
							}
							echo '<input name="preroll" type="radio" value="enabled" ' . $enabled . ' onchange="autoSubmit();">Enabled</input><br>';
							echo '<input name="preroll" type="radio" value="disabled" ' . $disabled . ' onchange="autoSubmit();"/>Disabled</input>';
							?>
							</td>


						</tr>
					</table>
				</form>
			</td>
		</tr>
		*/
		?>
		<tr>
			<td>Region</td>
			<td>
				<?php 
				//echo $contents[0]['region'] ;
				$arrRegion = explode(",",$contents[0]['region']) ;
				$allRegion = Utils::getAllRegions();
				//_debug($allRegion);
				$size = sizeof($arrRegion);
				foreach ($arrRegion as $key => $region) {
					$delim = ",";
					if($key == ($size-1)){
						$delim = "";
					}
					foreach ($allRegion as $i => $r) {
						if($r['id'] == $region){
							echo $r['region_name'].$delim;
						}
					}
				}

				?>
			</td>
		</tr>
		<?php /*
		<tr>
			<td>
			<input id="tags" type="text"
				value="<?php echo $objContent->getContentTagsById($contentId); ?>" /><input
				type="button" id="update_tags" value="submit" /> 
			</td>
		</tr>
		*/?>
		<tr>
			<td>Language</td>
			<td><?php echo $contents[0]['language'] ?></td>
		</tr>
	</table>
	<a href="reports.php?id=<?php echo $objContent->id; ?>"><b>Detailed Report</b></a>
	<hr>
	<?php 
	/*
	<a href="addprofile.php?id=<?php echo $objContent->id; ?>">Content profiles</a><br>
	*/
	?>
	<br>

	<div class="campaignform" style="width:500px;">
		<?php echo $message; ?>
		<b>Add stream</b><br><br>
		<form name="add_stream" method="post" action="">
			<table>
				<tr>
					<td>Stream Name</td><td>: <input style="width:200px;" type="text" name="name"/></td>
				</tr>
				<tr>
					<td>Server Public IP</td><td>: <input style="width:300px;" type="text" name="source_ip"/></td>
				</tr>
				<tr>
					<td>Server Private IP</td><td>: <input style="width:300px;" type="text" name="wowza_ip"/></td>
				</tr>
				<tr>
					<td>Application name+Prefix</td><td>: <input style="width:300px;" title="Add your application name followed by the prefix" type="text" name="path"/></td>
				</tr>
				<tr>
					<td>Stream status</td>
					<td>: <input  type="radio" name="s_status" value="Enable">Enabled
						<input  type="radio" name="s_status" value="Disable">Disabled
					</td>
				</tr>

				<tr>
					<td>&nbsp;</td><td><input type="Submit" name="Submit" value="Add" class="button"/></td>
				</tr>

			</table>
		</form>
	</div>
	
	<br><hr><br>
	<div>
		<b>Enable/Disaable streams</b>
		<table class="campaign">
			<tr><th>Stream Name</th><th>Server Public IP</th><th>Server Private IP</th><th>Application name+Prefix</th><th>Stream status</th></tr>
			<?php
			foreach ($arrStreams as $key => $stream) {
				echo "<tr>";
				echo "<td>".$stream['name']."</td><td>".$stream['source_ip']."</td><td>".$stream['ingest_ip']."</td><td>".$stream['path']."</td>";
				echo '<td>';
				if($stream['chstatus'] == 0){
					echo '<form name="change_status_'.$key.'" method="post" action="" onsubmit=""><input type="radio" name="s_status" value="Enable" onchange="autoSubmit('.$key.',\''.$stream['name'].'\');">Enable<br>';
					echo '<input type="radio" name="s_status" checked="checked" value="Disable" onchange="autoSubmit('.$key.',\''.$stream['name'].'\');">Disable';
					echo '<input type="hidden" name="stream" value="'.$stream['name'].'"></form>';
				}
				else{
					echo '<form name="change_status_'.$key.'" method = "post" action="" onsubmit=""><input type="radio" name="s_status" checked="checked" value="Enable" onchange="autoSubmit('.$key.',\''.$stream['name'].'\');">Enable<br>';
					echo '<input type="radio" name="s_status"  value="Disable" onchange="autoSubmit('.$key.',\''.$stream['name'].'\');">Disable';
					echo '<input type="hidden" name="stream" value="'.$stream['name'].'"></form>';
				}
				echo '</td></tr>';

			}
			?>
		</table>
	</div>

	<?php 
	/*
	<iframe width="100%" src="addstream.php?id=<?php echo $objContent->id; ?>"></iframe>

	<a href="addstream.php?id=<?php echo $objContent->id; ?>">Add Streams</a><br>
	*/?>
	<br><hr><br>
	<div>
		<form name="delete_channel" method="post" action="" onSubmit="return confirmDelete('<?php echo $objContent->name; ?>')">			<b>Delete this channel</b>&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="hidden" name="id" value="<?php echo $contentId; ?>">
			<input type="submit" name="delete" value="Delete" class="button">
		</form>
	</div>
	<?php

} else {
	echo "<label>" . $message . "</label>";
}
?>
</div>


<?php
require 'footer.php';
?>
