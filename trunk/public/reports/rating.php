<!DOCTYPE html>
<html>
<title>Channel Ratings</title>
<head>
	<link rel="stylesheet" href="../../CSS/main.css" type="text/css" />
	<script type="text/javascript">
	function changeScreenSize(w,h)
	{
		window.resizeTo( w,h )
	}
	</script>
</head>
<body class='base' style="width:auto;" onload="changeScreenSize(500,500)">

	<?php
	require_once '../../include/global.php';
	require_once '../../Db/DataHandler.php';
	require_once '../../Classes/UserInfo.php';
	require_once '../../Classes/Campaign.php';
	require_once '../../Classes/Session.php';
	require_once '../../Classes/Advertiser.php';
	require_once '../../Classes/Utils.php';
	require_once '../../Classes/Reports.php';
	require_once '../../Classes/Content.php';


	$objSession = new Session();

	$objUser = new UserInfo();
	$objUser->getCurrentUserInfo();


	$id = NULL;
	$start_date = NULL;
	$end_date = NULL;
	$dailyImpressions = NULL;
	$objCampaign = NULL;
	$arrRating = array();

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
		if($objUser->usertype == 'publisher'){
			$objContent = new Content();

			$arrContent = $objContent->getContentByPublisherId($objUser->userid);
			//_debug($arrContent);
			$objReport = new Reports();
			$arrSoldInv = $objReport->getPublisherInventorySoldGroupByContent($objUser->userid, $start_date, $end_date);
			//_debug($arrSoldInv);
			$arrTotalInv = $objReport->getPublisherInventoryTotalGroupByContent($objUser->userid, $start_date, $end_date);
			//_debug($arrTotalInv);
			$totalInv = "";
			foreach ($arrTotalInv as $i => $channelTotalInv) {
				$arrRating[$i]['id'] = $channelTotalInv['id'];
				$arrRating[$i]['name'] = $channelTotalInv['name'];
				$arrRating[$i]['total_inv'] = $channelTotalInv['count'];
				$arrRating[$i]['sold_inv'] = 0;
				$totalInv[$i] = $channelTotalInv['count'];
				foreach ($arrSoldInv as $j => $channelSoldInv) {

					if($arrRating[$i]['id'] == $channelSoldInv['id']){
						$arrRating[$i]['sold_inv'] = $channelSoldInv['count'];
						break;
					}
				}

				$arrRating[$i]['rating'] = round($arrRating[$i]['sold_inv']/$arrRating[$i]['total_inv']*10);

			}
			
			array_multisort($totalInv, SORT_DESC, $arrRating);
			

			?>
			<div class='report_info report_border' style='margin-left:auto;margin-right:auto;width:400px;'>
				<b>Channel Rating</b>  (starting: <?php echo $start_date; ?> Ending: <?php echo $end_date; ?>)<br>
				<table>
					<tr align='left'>
						<th style='width:130px;'>Channel</th>
						<th style='width:120px;padding-right:10px;'>Sold Inventory</th>
						<th style='width:120px;padding-right:10px;'>Total Inventory</th>
						<th style="width:120px;padding-right:10px;">Efficiency (%)</th>
					</tr>
					<?php
					foreach ($arrRating as $key => $channelRating) {
						$s = "";
						if($channelRating['id'] == $id){
							$s = "background: #797979;color: #dfdfdf;";
						}
						echo "<tr style='".$s."''>";

						echo "<td>".$channelRating['name']."</td>";
						echo "<td align='right' style='padding-right:20px;'>".number_format($channelRating['sold_inv'])."</td>";
						echo "<td align='right' style='padding-right:20px;'>".number_format($channelRating['total_inv'])."</td>";
						
						echo "<td align='right' style='padding-right:40px;'>";
						/*
						for ($count	=0; $count < $channelRating['rating']; $count ++) { 
							echo '&#9733;';
						}
						for($i = $channelRating['rating']; $i < 10; $i++){
							echo '&#9734;';
						}
						*/
						echo round($channelRating['sold_inv']/$channelRating['total_inv']*100,0);
						echo "</td>";
						echo "</tr>";
					}
					?>
				</table>
			</div>
			<?php
		}

	}
	?>
</body>
</html>