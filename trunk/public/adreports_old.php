<?php
/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/
require_once '../include/global.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/UserInfo.php';
require_once '../Classes/Campaign.php';
require_once '../Classes/Session.php';
require_once '../Classes/Advertiser.php';
require_once '../Classes/Utils.php';
require_once '../Classes/Reports.php';


require_once 'header.php';
$objAdvertiser = new Advertiser();
$objAdvertiser->getCurrentAdvertiserInfo();


$id = NULL;
$message = "Reports";
$default_dates = true;
$start_date = NULL;
$end_date = NULL;

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


	$objCampaign = new Campaign();

	$showStatsBool = false;

	if ($objAdvertiser->usertype == "advertiser") {
		$showStatsBool = $objCampaign->getAdvertiserCampaignById($objAdvertiser->userid, $id);
	}
	//echo "<pre>";
	//print_r($objCampaign);
	//echo "</pre>";
	if ($showStatsBool == false) {
		$message = "Unauthorized access";
	} else {
		$message = "Reports";
		

		$objReport = new Reports();


		$tmp_start_time = strtotime($objCampaign->start_date);
		$tmp_end_time = strtotime($objCampaign->end_date);

		$tmp_cur_date = strtotime(date("Y-m-d")) ;



		if($default_dates == true){
			$start_date = $objCampaign->start_date;
			$end_date = $objCampaign->end_date;

		}

			
		$topCountryData = $objReport->CampaignReportGetTopCountryData($id, 10, $start_date, $end_date);
			
		$topCityData = $objReport->CampaignReportGetTopCityData($id, 10, $start_date, $end_date);

		$dailyImpressions = $objReport->CampaignGetDailyImpressions($id, $objAdvertiser->userid, $start_date, $end_date);
			
		$hourlyImpressions = $objReport->CampaignGetHourlyImpressions($id, $objAdvertiser->userid, $start_date, $end_date);
			


		if($hourlyImpressions){
			$formattedHourlyData = array();
			for ($i = 0; $i < 24; $i++) {

				$formattedHourlyData[$i]['hour'] = $i;
				$formattedHourlyData[$i]['count'] = 0;

				foreach ($yesterDayData['hourly'] as $key => $oneHourData) {

					if ($oneHourData['hour'] == $i) {
						$formattedHourlyData[$i]['hour'] = $i;
						$formattedHourlyData[$i]['count'] = $oneHourData['count'];
						break;
					}
				}
			}
		}
		//$currentYearData = $objReport->CampaignGetCurrentYearData($id, $objAdvertiser->userid);
	}
} else {
	$message = "Unauthorized access";
}
/*
 * {
"chart":{
"caption":"My Chart Caption"
},
"data":[
{  "value":"100" },
{  "value":"200" }
]
}
);

*/
?>
<div class="page">

	<script type="text/javascript" src="http://www.google.com/jsapi"></script>

	<script type="text/javascript">
        google.load('visualization', '1');
    </script>
	<script type="text/javascript">
        var yestData = null;
        var monthlydata = null;
        var yearlydata = null;
	
        function drawYesterdayChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Hour');
            data.addColumn('number', 'Impressions');
<?php
foreach ($formattedHourlyData as $data) {
    echo "data.addRow(['" . $data['hour'] . "', " . $data['count'] . "]);";
}
?>
			
        var wrapper = new google.visualization.ChartWrapper({
            chartType: 'LineChart',
            dataTable: data,
            options: {'title': 'Yesterday\'s hourly  Distribution'},
            containerId: 'yesterdaydiv',
            chartArea:{left:20,top:0,width:"100%",height:"75%"}
        });
        yestData = data;
        wrapper.draw();
    }

    function drawMonthlyChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Day');
        data.addColumn('number', 'Impressions');
<?php
foreach ($currentMonthData['daily'] as $data) {
    echo "data.addRow(['" . $data['DAY'] . "', " . $data['count'] . "]);";
}
?>
			
        var wrapper = new google.visualization.ChartWrapper({
            chartType: 'LineChart',
            dataTable: data,
            options: {'title': 'Daily Distribution'},
            containerId: 'monthlydiv',
            chartArea:{left:20,top:0,width:"100%",height:"75%"},
            vAxis:{minValue:0}
        });

        monthlydata = data;
        wrapper.draw();
    }


    
       
    google.setOnLoadCallback(drawMonthlyChart);
		   
    google.setOnLoadCallback(drawYesterdayChart);
		             
    // Popup window code
    function newPopup(url) {
        popupWindow = window.open(
        url,'popUpWindow','height=700,width=600,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no,status=yes')
    }

    function exportdata(data){
        url = "<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/adex/test/export.php' ?>";
        //alert(data);
        
        var params = data;
        OpenWindowWithPost(url, '', 'report', params);
    }
    function OpenWindowWithPost(url, windowoption, name, params)
    {
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", url);
        form.setAttribute("target", name);
        /*
        for (var i in params) {
            if (params.hasOwnProperty(i)) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name =i;           
                var value = ;
                input.value = value;
                form.appendChild(input);
            }
        }
         */
       
       
       
        //note I am using a post.htm page since I did not want to make double request to the page 
        //it might have some Page_Load call which might screw things up.
                 
        window.target="_blank";
        window.open(url, name, windowoption);
        //window.location.href = url;
                 
      
        form.submit();
      
        document.body.removeChild(form);
    }
    
    </script>
	<table style="width: 100%;">
		<tr>
			<td><label style="width: 220px;"><?php echo $message; ?> </label>
			</td>
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>
				<form name="campaigns" action="" method="POST">
					<select name="id" onchange="this.form.submit();">

						<?php
						$campaigns = $objAdvertiser->getCampaignsByUserId();

						foreach ($campaigns as $campaign) {
							if ($campaign['id'] == $id) {
								echo "<option value='" . $campaign['id'] . "' selected='selected'>" . $campaign['name'] . "</option>";
							} else {
								echo "<option value='" . $campaign['id'] . "'>" . $campaign['name'] . "</option>";
							}
						}
						?>
					</select>
				</form>
			</td>
		</tr>

		<tr>
			<td class="report" valign="top" align="center">Top 10 Countries
				<table class="smallreport">
					<!-- <a href="JavaScript:newPopup('http://www.quackit.com/html/html_help.cfm');">Open a popup window</a> -->
					<tr>
						<th>Country</th>
						<th>Impressions</th>
					</tr>
					<?php
					foreach ($topCountryData as $coutryData) {
						echo '<tr><td><a href="JavaScript:newPopup(\'http://' . $_SERVER['SERVER_NAME'] . '/adex/public/countryreport.php?country=' . $coutryData['country'] . '&camp=' . $id . '\');">' . $coutryData['country'] . '</a></td>';
						echo "<td class='report_numbers'>" . $coutryData['count'] . "</td></tr>";
					}
					?>
				</table> <a style="float: right; height: 20px; width: 20px;"
				title="Download this report"
				href='JavaScript:exportdata(eval(<?php echo json_encode($topCountryData); ?>))'><img
					style="height: 20px; width: 20px;" src="../CSS/icon/export.png"> </a>

			</td>
			<td class="report" valign="top" align="center">Top 10 Cities
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
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td colspan="2">Yesterday's total impressions: <?php echo $yesterDayData['total'] ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="report"><script type="text/javascript">
			$("<div id=\"yesterdaydiv\" style=\"width: 100%; height: 400px;\"></div>").dialog();
			</script>
			</td>
		</tr>
		<tr>
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td colspan="2">Current Month's (<?php echo date("F"); ?>) Total
				Impressions: <?php echo $currentMonthData['total']; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="report">
				<div id="monthlydiv" style="width: 100%; height: 400px;"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr>
			</td>
		</tr>
		<tr>
			<td colspan="2">Current Year Total Impressions: <?php echo $currentYearData['total']; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="report">
				<div id="yearlydiv" style="width: 100%; height: 400px;"></div>
			</td>
		</tr>
	</table>

</div>
<?php
require_once 'footer.php';
?>