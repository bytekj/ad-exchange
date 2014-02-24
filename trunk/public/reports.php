<?php
require_once '../include/global.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/UserInfo.php';
require_once '../Classes/Campaign.php';
require_once '../Classes/Session.php';
require_once '../Classes/Advertiser.php';
require_once '../Classes/Utils.php';
require_once '../Classes/Reports.php';
require_once '../Classes/Content.php';


require_once 'header.php';

$id = $_GET['id'];

$objUserInfo = new UserInfo();
$objUserInfo->getCurrentUserInfo();


?>
<script type="text/javascript">
<!--

//-->
function isDate(txtDate, separator) {
    var aoDate,           // needed for creating array and object
        ms,               // date in milliseconds
        month, day, year; // (integer) month, day and year
    // if separator is not defined then set '/'
    if (separator === undefined) {
    	separator = '/';
    }
    // split input date to month, day and year
    aoDate = txtDate.split(separator);
    // array length should be exactly 3 (no more no less)
    if (aoDate.length !== 3) {
    	return false;
    }
    // define month, day and year from array (expected format is m/d/yyyy)
    // subtraction will cast variables to integer implicitly
    year = aoDate[0] - 0; // because months in JS start from 0
    month = aoDate[1] - 1;
    day = aoDate[2] - 0;
    // test year range
    if (year < 1000 || year > 3000) {
    	return false;
    }
    // convert input date to milliseconds
    ms = (new Date(year, month, day)).getTime();
    // initialize Date() object from milliseconds (reuse aoDate variable)
    aoDate = new Date();
    aoDate.setTime(ms);
    // compare input date and parts from Date() object
    // if difference exists then input date is not valid
    if (aoDate.getFullYear() !== year ||
    	aoDate.getMonth() !== month ||
    	aoDate.getDate() !== day) {
    	return false;
}
    // date is OK, return true
    return true;
}

function open_reports(report){
	var selectEle = document.getElementById('campaigns').selectedOptions;
	var selection = "";
	for (i=0; i<selectEle.length; i++){
		var sep = ",";
		if(i == (selectEle.length-1)){
			sep = "";
		}
		selection=selection+selectEle[i].value+sep;
	}
	//var selection = selectEle.children[selectEle.selectedIndex].value;
	if(report=='perf'){
		if(document.getElementById("duration_checkbox").checked == false){
			if(isDate(document.getElementById("start_datepicker").value,"-")){
				if(isDate(document.getElementById("start_datepicker").value,"-")){
					window.location.href = "adreports.php?id="+selection+
					"&st_dt="+document.getElementById("start_datepicker").value+
					"&ed_dt="+document.getElementById("end_datepicker").value;
				}
				else{
					alert("Select valid date");
				}
			}
			else{
				alert("Select valid date");
			}
		}
		
		else if(document.getElementById("duration_checkbox").checked == true){
			<?php 
			if($objUserInfo->usertype == 'advertiser'){
				echo 'window.location.href = "adreports.php?id="+selection;';			
			}
			else if($objUserInfo->usertype == 'publisher'){
				echo 'window.location.href = "contentreports.php?id="+selection;';			
			}
			?>
		}
		else{
			alert("Select valid options");
		}
	}
	else if(report=='fin'){
		window.location.href = "finreport.php?id="+selection;
	} 
}

</script>
<div class="page">
	<?php
	if($objUserInfo->usertype == 'advertiser'){
		?>
		<form name="campaigns" action="adreports.php" method="GET">
			<table>
				<tr>
					<td valign="top">
						<b>Select one or more Campaigns</b>
					</td>
					<td>
						<?php

						$objAdvertiser = new Advertiser($objUserInfo);

						$campaigns = $objAdvertiser->getCampaignsByUserId();
						?>
						<select id="campaigns" name="id[]" multiple="multiple" class="select">
							<?php
							
							foreach ($campaigns as $campaign) {
								if ($campaign['id'] == $id) {
									echo "<option value='" . $campaign['id'] . "'selected='selected'>" . $campaign['name'] . "</option>";
								} else {
									echo "<option value='" . $campaign['id'] . "'>" . $campaign['name'] . "</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>
			</table>
		</form>
		<br> <br> 
		<script type="text/javascript">
		$(function() {
			$( "#start_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
			$( "#start_datepicker" ).click(function(){
				document.getElementById("duration_checkbox").checked=false;
			});
			$( "#end_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
			$("#duration_checkbox").click(function(){
				document.getElementById("start_datepicker").value = "Start Date";
				document.getElementById("end_datepicker").value = "End Date";
			});
		});

		</script>
		<div class="report_border report_info"> 
			<b>Performance Report</b><br> 
			<input value="Start Date" id="start_datepicker" type="text">
			<input value="End Date" id="end_datepicker" type="text"><br><br>Or  
			<input id="duration_checkbox" type="checkbox">Campaign duration

			<a class="button" style="float:right;width:30px;height:20px;"
			title="View Campaign performance reports"
			href="javascript:open_reports('perf');">Go</a>
		</div>
		<br><br>
		<div class="report_border report_info">
			<b>Financial Report</b>
			<a class="button" style="float:right;width:30px;height:20px;" title="View Campaign Financial Report"
			href="javascript:open_reports('fin');">Go</a>
		</div>

		<?php 

	}
	else if($objUserInfo->usertype == 'publisher'){
		$start_date = "";
		$end_date =  "";
		//_debug($_GET);
		if(isset($_GET['st_dt']) && isset($_GET['ed_dt'])){
			$start_date = $_GET['st_dt'];
			$end_date = $_GET['ed_dt'];
		}
		else{
			$start_date = date('Y-m-').'01';
			$end_date = date('Y-m-d');
		}
		//echo $start_date." ".$end_date;
		?>
		<script type="text/javascript">
		$(function() {
			$( "#start_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
			
			$( "#end_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
			
			document.getElementById("start_datepicker").value = "<?php echo $start_date; ?>";
			document.getElementById("end_datepicker").value = "<?php echo $end_date; ?>";
		});
		function submit_content_form(){
			var selectEle = document.getElementById('content_id').selectedOptions;
			var selection = "";
			for (i=0; i<selectEle.length; i++){
				var sep = ",";
				if(i == (selectEle.length-1)){
					sep = "";
				}
				selection=selection+selectEle[i].value+sep;
			}

			start_date = document.getElementById("start_datepicker").value;

			end_date = document.getElementById("end_datepicker").value;

			window.location = "reports.php?id="+selection+"&st_dt="+start_date+"&ed_dt="+end_date;
			//document.form['content'].submit();
		}
		</script>
		<form name="content" method="GET" action="reports.php">
			<table>
				<tr>
					<td valign="top">Report For:</td>
					<td>
						<?php
						//get content by publisherid
						//echo $objUserInfo->userid;
						$objContent = new Content();
						$arrContent =  $objContent->getContentByPublisherId($objUserInfo->userid);
						/*
						echo "<pre>";
						print_r($arrContent);
						echo "</pre>";
						*/
						$ids = explode(",", $id);
						$objReport = new Reports();
						
						$totalInv = $objReport->getContentInventoryTotalByDate($objUserInfo->id, $id, $start_date, $end_date);
						
						$soldInv = $objReport->getContentInventorySoldByDate($objUserInfo->id, $id, $start_date, $end_date);

						$avgCPM = $objReport->getAvgCPMByDate($objUserInfo->id, $id,$start_date, $end_date);
						
						?>

						<select name="id" id="content_id" multiple="multiple" class="select" onchange="submit_content_form()"?>
							<?php
							foreach ($arrContent as $key => $content) {
								if(array_search($content['id'], $ids) === false){
									echo "<option value='".$content['id']."'>".$content['name']."</option>";
								}
								else{
									echo "<option value='".$content['id']."' selected='selected'>".$content['name']."</option>";	
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						Starting:<input name="st_dt" id="start_datepicker" onchange="submit_content_form()" type="text">
						Ending:<input name="ed_dt" id="end_datepicker" onchange="submit_content_form()" type="text">
					</td>

				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<div class="report_border report_info">
							<b>Summary</b>
							<table style="margin-left:50px;">
								<tr>
									<td>Total Inventory</td><td>: <?php echo number_format($totalInv); ?> Impressions</td>
								</tr>
								<tr>
									<td>Inventory Sold </td><td>: <?php echo number_format($soldInv); ?> Impressions</td>
								</tr>
								<tr>

									<td>Average CPM : </td><td>: $<?php echo round($avgCPM,2); ?></td>
								</tr>
								<?php
								/*
								<tr>

									<td>Top Earning platform: </td><td>:</td>
								</tr>
								*/
								?>
							</table>
						</div>
					</td>
				</tr>

			</table>
		</form>
		<br><br>
		<div class="report_border report_info" style="margin-left:72px;">
			<a href="contentreports.php?id=<?php echo $id; ?>&st_dt=<?php echo $start_date ?>&ed_dt=<?php echo $end_date ?>" >
				<b>Inventory & Performance Report</b>
			</a><br><br>
			<a href="finreport.php?id=<?php echo $id; ?>" >
				<b>Financial Report</b>
			</a>
		</div>

		<?php 
	}
	?>

</div>
<?php
require 'footer.php';
?>