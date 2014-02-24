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
	$contents = $objContent->getPublisherContentByPublisherIdWithoutprofs($objPublisher->userid, $contentId);
	$message = "";

	if(isset($_POST['Submit'])){
		//
		$mode = $_POST['Submit'];
		
		
		if($mode == "Add"){
			echo "<pre>";
			print_r($_POST);
			echo "</pre>";
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

				if($objContent->addStream($objPublisher->userid, $name,$sourceIp, $path, $wowzaIp, $stream_status))
				{
					header("Location: addstream.php?id=".$objContent->id);
				}
			}
		}
	}
	else if(isset($_POST['s_status'])){
		$status = $_POST['s_status'];
		$stream = $_POST['stream'];

		$objContent->updateStreamStatus($objPublisher->userid, $objContent->id, $stream, $status);
	}
	?>
	<script type="text/javascript">
	function autoSubmit(key,stream)
	{
		console.log(stream);
		var formObject = document.forms['change_status_'+key];
		formObject.submit();
	}
	</script>
	<div>
		Content: <b><?php echo $contents[0]['name']; ?></b>
		<div class="campaignform" style="width:500px;">
			<?php echo $message; ?>
			<b>Add stream</b><br><br>
			<form name="add_stream" method="post" action="">
				<table>
					<tr>
						<td>Stream Name</td><td>: <input style="width:200px;" type="text" name="name"/></td>
					</tr>
					<tr>
						<td>Source IP</td><td>: <input style="width:300px;" type="text" name="source_ip"/></td>
					</tr>
					<tr>
						<td>Application name+Prefix</td><td>: <input style="width:300px;" title="Add your application name followed by the prefix" type="text" name="path"/></td>
					</tr>
					<tr>
						<td>Wowza IP</td><td>: <input style="width:300px;" type="text" name="wowza_ip"/></td>
					</tr>
					<tr>
						<td>Stream status</td>
						<td>: <input  type="radio" name="s_status" value="Enable">Enabled
							<input  type="radio" name="s_status" value="Disable">Disabled
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td><td><input type="Submit" name="Submit" value="Add"/></td>
					</tr>

				</table>
			</form>
		</div>
		<?php
		$arrStreams = $objContent->getStreams($objPublisher->userid, $objContent->id);
		/*
		echo "<pre>";
		print_r($arrStreams);
		echo "</pre>";
		*/
		?>
		<br><hr><br>
		<div>
			<b>Enable/Disaable streams</b>
			<table class="campaign">
				<tr><th>Stream Name</th><th>Source IP</th><th>Application name+Prefix</th><th>Wowza IP</th><th>Stream status</th></tr>
				<?php
				foreach ($arrStreams as $key => $stream) {
					echo "<tr>";
					echo "<td>".$stream['name']."</td><td>".$stream['source_ip']."</td><td>".$stream['path']."</td><td>".$stream['ingest_ip']."</td>";
					
					echo '<td>';
					if($stream['chstatus'] == 0){
						echo '<form name="change_status_'.$key.'" method="post" action="" onsubmit=""><input type="radio" name="s_status" value="Enable" onchange="autoSubmit('.$key.',\''.$stream['name'].'\');">Enable<br>';
						echo '<input type="radio" name="s_status" checked="checked" value="Disable" onchange="autoSubmit('.$key.',\''.$stream['name'].'\');">Disable';
						echo '<input type="hidden" name="stream" value="'.$stream['name'].'"></form>';
					}
					else{
						echo '<form name="change_status_'.$key.'" method = "post" action="" onsubmit=""><input type="radio" name="s_status" checked="checked" value="Enable" onchange="autoSubmit('.$key.'\''.$stream['name'].'\');">Enable<br>';
						echo '<input type="radio" name="s_status"  value="Disable" onchange="autoSubmit('.$key.'\''.$stream['name'].'\');">Disable';
						echo '<input type="hidden" name="stream" value="'.$stream['name'].'"></form>';
					}
					echo '</td></tr>';

				}
				?>
			</table>
		</div>
	</div>

	<?php 
}
//require 'footer.php';
?>