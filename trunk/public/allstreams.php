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
	if(isset($_GET['o'])){
		$offset = $_GET['o'];	
	}
	if(isset($_GET['l'])){
		$limit = $_GET['l'];
	}


	$objPublisher = new Publisher();
	$objPublisher->getCurrentPublisherInfo();

	$objContent = new Content();
	$arrStreams = $objContent->getAllStreamsByPublisherId($objPublisher->id, $offset, $limit);
	/*
	echo "<pre>";
	print_r($arrStreams[0]);
	echo "</pre>";
	*/
	
	/*
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	$status = "";
	*/
	if(isset($_POST['Enable']) || isset($_POST['Disable'])){
		if(isset($_POST['Enable'])){
			$status = 'Enable';
		}
		else{
		//echo "here";
			$status = 'Disable';
		}

		$streams = $_POST['stream'];
		foreach ($streams as $key => $stream) {
			$arrStream = explode("@@", $stream);
			/*
			echo "<pre>";
			print_r($arrStream);
			echo "</pre>";
			*/
			echo $streamName=$arrStream[0];
			echo $contentId=$arrStream[1];
			$objContent->updateStreamStatus($objPublisher->userid,$contentId, $streamName, $status);
			//updateStreamStatus($pubid, $contentId, $stream, $status)
		}
		header("Location: ".$_SERVER['REQUEST_URI']);
	}

	
	?>
	<div class="page">
		<b>Content Streams</b>
		<form name="streams" method="post" action="">
			<table class="content" style="width:800px">
				<tr><th>&nbsp;</th><th>Content</th><th>Stream name</th><th>Source IP</th><th>Application name+Prefix</th><th>Wowza IP</th><th>Status</th></tr>
				<?php
				foreach ($arrStreams as $key => $stream) {
					echo "<tr>";
					$n = $key+$offset+1;
					echo "<td style='width:80px;'><input type='checkbox' name='stream[]	' value='".$stream['stream']."@@".$stream['cid']."'>".$n."</td>";
					echo "<td style='width:200px;'>".$stream['content']."</td>";
					echo "<td style='width:150px;'>".$stream['stream']."</td>";
					echo "<td style='width:100px;'>".$stream['source_ip']."</td>";
					echo "<td style='width:150px;'>".$stream['path']."</td>";
					echo "<td style='width:100px;'>".$stream['ingest_ip']."</td>";
					if($stream['chstatus'] == 1)
						echo "<td style='width:100px;'>Enabled</td>";	
					else
						echo "<td style='width:100px;'>Disabled</td>";	

					echo "</tr>";
				}


				?>

			</table>
			<?php
			$nextoffset = 0;
			$prevoffset = 0;

			if(sizeof($arrStreams) < $limit)
				$nextoffset = $offset;
			else
				$nextoffset = $offset+$limit;
			if($offset == 0){
				$prevoffset = 0;
			}
			else{
				$prevoffset = $offset-$limit;
			}

			?>

			<table width="700px" style="margin-left:auto;margin-right:auto;">
				<tr><td width="100px"><input type="submit" name="Enable" value="Enable"></td>
					<td width="100px"><input type="submit" name="Disable" value="Disable"></td>
					<td width="150px"></td><td width="150px"></td>
					<td><a href="allstreams.php?o=<?php echo $prevoffset; ?>&l=<?php echo $limit; ?>">Prev</a></td>
					<td><a href="allstreams.php?o=<?php echo $nextoffset; ?>&l=<?php echo $limit; ?>">Next</a></td></tr></table>


				</form>
			</div>
			<?php
		}
		require 'footer.php';
		?>