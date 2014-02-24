<?php
require_once '../../Classes/UserInfo.php';
require_once '../../Classes/Session.php';
require_once '../../Classes/Content.php';
require_once '../../Classes/UIForm.php';
require_once '../../Classes/Adspot.php';
require_once '../../Classes/Campaign.php';
require_once '../../Classes/Advertiser.php';
require_once '../../Db/DataHandler.php';
require_once '../../include/global.php';
require_once '../../Classes/Reports.php';


if(isset($_GET['set'])){
	//generate invoices
	$objReport = new Reports();

	$objData = new DataHandler();
	$currency = "$";
	/*
	 INSERT INTO
	`invoice`(`id`, `type`, `item_serial`, `item_id`, `item_name`, `start_date`, `end_date`, `item_data`, `amount`, `currency`, `status`, `userid`)
	VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8],[value-9],[value-10],[value-11],[value-12])
	*/
	$arrCampaigns = array();
	echo date("d")."<br>";
	if(date("d") == "01"){
		//5th of every month
		$sql= "select id,cpm, name, start_date, end_date,advertiser_id, cpm from campaign where end_date >= date(date_sub(current_timestamp, interval 1 day)) ";
		_debug($sql);
		$arrCampaigns = $objData->GetQuery($sql);
		if($res == -1){

		}
		else{
			foreach($arrCampaigns as $campaign){

				$impressionSql = 
				"select max(date(dt)) as end_date, min(date(dt)) as start_date,
				count(log.id) as total
				from calendar join log on date(ad_played_timestamp)=dt
				where campaign_id=".$campaign['id']." and ad_played_timestamp not like '0000-00-00 00:00:00' and month(dt) = (month(now())-1)";
				_debug($impressionSql);						
				$impressions = $objData->GetQuery($impressionSql);
				_debug($impressions);

				$totalimpressions = $impressions[0]['total'];

				$start_date = $impressions[0]['start_date'];
				$end_date = $impressions[0]['end_date'];
				echo  "here";
				$a = str_split($campaign['advertiser_id'], 3);
				$b = date("y")."-".(date("y")-1);
				$c = $campaign['id'];
				$serial = $a[0].$b.$c;

				$amount = $totalimpressions/1000*$campaign['cpm'];
				echo $amount."<br>";
				

				$insertInvoiceSql = "INSERT INTO `invoice`
				(`type`, `item_serial`, `item_id`, `item_name`, `start_date`, `end_date`, `item_data`, `amount`, `currency`, `status`, `userid`, `date_of_generation`)
				VALUES('campaign','".$serial."','".$campaign['id']."','".$campaign['name']."','".$start_date."','".$end_date."','".$totalimpressions."','".$amount."','".$currency."','pending','".$campaign['advertiser_id']."',date(now()))";

				_debug($insertInvoiceSql);		

			}
		}

	}
	else{
		//campaigns ended yesterday
		echo $sql= "select  id,cpm, name, start_date, end_date,advertiser_id, cpm from campaign where end_date = date(current_timestamp) ";
		$arrCampaigns = $objData->GetQuery($sql);
		if($arrCampaigns == -1){

		}
		else{
			foreach($arrCampaigns as $campaign){
				echo "<br><br>".$impressionSql = "select
				max(date(dt)) as end_date, min(date(dt)) as start_date,
				count(log.id) as total
				from calendar join log on date(ad_played_timestamp)=dt
				where campaign_id=".$campaign['id']." and ad_played_timestamp not like '0000-00-00 00:00:00' and month(dt) = month(now())";

				$impressions = $objData->GetQuery($impressionSql);
				$impressions = $impressions[0]['total'];
				$start_date = $impressions[0]['start_date'];
				$end_date = $impressions[0]['end_date'];
				
				$a = str_split($campaign['advertiser_id'], 3);
				$b = date("y")."-".(date("y")-1);
				$c = $campaign['id'];
				$serial = $a[0]."/".$b."/".$c;

				$amount = $impressions/1000*$campaign['cpm'];


				echo "<br><br>". $insertInvoiceSql = "INSERT INTO `invoice`
				(`type`, `item_serial`, `item_id`, `item_name`, `start_date`, `end_date`, `item_data`, `amount`, `currency`, `status`, `userid`,`date_of_generation`)
				VALUES('campaign','".$serial."','".$campaign['id']."','".$campaign['name']."','".$start_date."','".$end_date."','".$impressions."','".$amount."','".$currency."','pending','".$campaign['advertiser_id']."',date(now()))";

			}
		}
	}

}

?>