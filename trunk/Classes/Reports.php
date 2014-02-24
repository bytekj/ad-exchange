<?php

/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/

/**
 * Description of Reports
 *
 * @author kj
 */
class Reports {
	/*
	 *
	
	*/
	var $memcached;

	public function __construct() {
		$this->memcached = new Memcached;
		$this->memcached->addServer('localhost', 11211);
		$this->memcached->setOption(Memcached::OPT_CONNECT_TIMEOUT, 1000);
	}

	private function get($key){
		return $this->memcached->get($key);
	}

	private function set($key, $value){
		return $this->memcached->set($key, $value, 600);
	}

	public function getPublisherRevenueByMonthYearGroupedByDevice($pubid, $month, $year){
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($pubid);		

		$revenueSql = "SELECT log.content_id AS id, device, SUM( cpm ) /1000 AS revenue
		FROM log
		JOIN content ON log.content_id = content.id
		JOIN users ON content.publisher_id = users.id
		WHERE users.userid =  '".$pubid."'
		AND MONTH( convert_tz(client_hit_timestamp,  '".$server_tz."', '".$user_tz."' )) =  '".$month."'
		AND YEAR( convert_tz(client_hit_timestamp, '".$server_tz."', '".$user_tz."')) =  '".$year."'
		AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'
		GROUP BY device";

		$objData = new DataHandler();
		$res = $objData->GetQuery($revenueSql);
		/*
		_debug($revenueSql);
		_debug($res);
		*/

		if($res == -1){
			return false;
		}
		else{
			return $res;
		}
	}
	
	public function getPublisherRevenueByMonthYearGroupedByContent($pubid, $month, $year){
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($pubid);

		$revenueSql = "SELECT log.content_id AS id, content.name AS name, SUM( cpm ) /1000 AS revenue
		FROM log
		JOIN content ON log.content_id = content.id
		JOIN users ON content.publisher_id = users.id
		WHERE users.userid =  '".$pubid."'
		AND MONTH( convert_tz(client_hit_timestamp,  '".$server_tz."', '".$user_tz."' )) =  '".$month."'
		AND YEAR( convert_tz(client_hit_timestamp, '".$server_tz."', '".$user_tz."')) =  '".$year."'
		AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'
		GROUP BY log.content_id";
		//_debug($revenueSql);
		$objData = new DataHandler();
		$res = $objData->GetQuery($revenueSql);
		if($res == -1){
			return false;
		}
		else{
			return $res;
		}
	}

	public function getPublisherRevenueByMonthYearTotal($pubid, $month, $year){
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($pubid);

		$revenueSql = "SELECT SUM( cpm ) /1000 AS revenue
		FROM log
		JOIN content ON log.content_id = content.id
		JOIN users ON content.publisher_id = users.id
		WHERE users.userid =  '".$pubid."'
		AND MONTH( convert_tz(client_hit_timestamp,  '".$server_tz."', '".$user_tz."' )) =  '".$month."'
		AND YEAR( convert_tz(client_hit_timestamp, '".$server_tz."', '".$user_tz."')) =  '".$year."'
		AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'";
		//_debug($revenueSql);
		$objData = new DataHandler();
		$res = $objData->GetQuery($revenueSql);
		if($res == -1){
			return false;
		}
		else{
			return $res[0]['revenue'];
		}
	}

	public function ContentGetDashReport($pubid){


		$objData = new DataHandler();

		$liveChannelSql = "select count(id) as count from content where publisher_id=".$pubid;

		$totalChannels = $objData->GetQuery($liveChannelSql);
		$totalChannels = $totalChannels[0]['count'];

		$res['channel_count'] = $totalChannels;

		$currentMonthInventorySql="SELECT count(DISTINCT (l.client_id)) as count
		FROM  `log` l, content c
		WHERE l.content_id = c.id
		AND c.publisher_id = ".$pubid."
		AND MONTH( l.client_hit_timestamp ) = MONTH( NOW( ) ) ";

		$totalInventory = $this->get($pubid."_totalInventory");
		if($totalInventory == false){
			$totalInventory = $objData->GetQuery($currentMonthInventorySql);
			$this->set($pubid."_totalInventory", $totalInventory);
		}


		$soldInventorySql = "SELECT count(DISTINCT (l.client_id)) as count
		FROM  `log` l, content c
		WHERE l.content_id = c.id
		AND c.publisher_id = ".$pubid."
		AND MONTH( l.client_hit_timestamp ) = MONTH( NOW( ) ) 
		AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'";

		$soldInventory = $this->get($pubid."_soldInventory");
		if($soldInventory == false){
			$soldInventory = $objData->GetQuery($soldInventorySql);
			$this->set($pubid."_soldInventory", $soldInventory);
		}

		//$soldInventory = $objData->GetQuery($soldInventorySql);

		$avgCPMSql = "SELECT SUM( cpm ) / COUNT( clid ) as avg_cpm
		FROM (SELECT DISTINCT (	l.client_id	) AS clid, l.cpm AS cpm FROM  `log` l, content c, users u WHERE l.content_id = c.id AND c.publisher_id =".$pubid." AND MONTH( l.client_hit_timestamp ) = MONTH( NOW( ) )  AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00' ) AS cpm";
		//_debug($avgCPMSql);
		$avgCPM = $this->get($pubid."_avgCPM");
		if($avgCPM == false){
			$avgCPM = $objData->GetQuery($avgCPMSql);
			$this->set($pubid."_avgCPM", $avgCPM);
		}

		//$avgCPM = $objData->GetQuery($avgCPMSql);

		$res['avg_cpm'] = $avgCPM[0]['avg_cpm'];

		$res['total_inventory'] = $totalInventory[0]['count'];

		$res['sold_inventory'] = $soldInventory[0]['count'];

		return $res;
	}

	public function getAvgCPMByDate($pubid, $contentId, $start_date, $end_date){

		$avgCPMSql = "SELECT SUM( cpm ) / COUNT( clid ) as avg_cpm
		FROM (SELECT l.client_id AS clid, l.cpm AS cpm FROM  `log` l, content c, users u WHERE l.content_id = c.id AND c.publisher_id =".$pubid." AND c.id in(".$contentId.") AND date( l.client_hit_timestamp ) <= '".$end_date."'	AND date( l.client_hit_timestamp ) >= '".$start_date."'  AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00' ORDER BY  `l`.`cpm` DESC ) AS cpm";
		//_debug($avgCPMSql);		
		$objData = new DataHandler();
		$avgCPM = $objData->GetQuery($avgCPMSql);
		if($avgCPM == -1){
			return false;
		}
		else{
			return $avgCPM[0]['avg_cpm'];	
		}

	}

	public function getAvgCPM($pubid, $contentId){
		$avgCPMSql = "SELECT SUM( cpm ) / COUNT( clid ) as avg_cpm
		FROM (SELECT l.client_id AS clid, l.cpm AS cpm FROM  `log` l, content c, users u WHERE l.content_id = c.id AND c.publisher_id =".$pubid." AND c.id=".$contentId." AND MONTH( l.client_hit_timestamp ) = MONTH( NOW( ) )  AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00' ORDER BY  `l`.`cpm` DESC ) AS cpm";
		$objData = new DataHandler();
		$avgCPM = $objData->GetQuery($avgCPMSql);
		if($avgCPM == -1){
			return false;
		}
		else{
			return $avgCPM[0]['avg_cpm'];	
		}

	}

	public function getPublisherInventoryTotalGroupedByDevice($pubid, $start_date, $end_date, $contentId=-1){
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($pubid);
		if($contentId == -1){
			$sql = 
			"SELECT COUNT( log.id ) AS count, log.device AS device
			FROM log
			JOIN content ON log.content_id = content.id
			JOIN users ON content.publisher_id = users.id
			WHERE users.userid =  '".$pubid."'
			AND date( convert_tz(client_hit_timestamp,'".$server_tz."' ,'".$user_tz."' ) ) >= '".$start_date."'
			AND date( convert_tz(client_hit_timestamp,'".$server_tz."' ,'".$user_tz."' ) ) <= '".$end_date."'
			GROUP BY device";	
		}
		else{
			$sql = 
			"SELECT COUNT( log.id ) AS count, log.device AS device
			FROM log
			JOIN content ON log.content_id = content.id
			JOIN users ON content.publisher_id = users.id
			WHERE users.userid =  '".$pubid."'
			AND date( convert_tz(client_hit_timestamp,'".$server_tz."' ,'".$user_tz."' ) ) >= '".$start_date."'
			AND date( convert_tz(client_hit_timestamp,'".$server_tz."' ,'".$user_tz."' ) ) <= '".$end_date."'
			AND content.id in (".$contentId.")
			GROUP BY device";	

		}	
		//_debug($sql);
		$objData = new DataHandler();

		$result = $objData->GetQuery($sql);
		/*
		_debug(__FUNCTION__);
		_debug($sql);
		_debug($result);
		*/
		if($result == -1){
			return false;
		}
		else{
			return $result;
		}
	}


	public function getPublisherInventorySoldGroupedByDevice($pubid, $start_date, $end_date, $contentId=-1){
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($pubid);
		if($contentId == -1){
			$sql = 
			"SELECT COUNT( log.id ) AS count, log.device AS device
			FROM log
			JOIN content ON log.content_id = content.id
			JOIN users ON content.publisher_id = users.id
			WHERE users.userid =  '".$pubid."'
			AND date( convert_tz(client_hit_timestamp,'".$server_tz."' ,'".$user_tz."' ) ) >= '".$start_date."'
			AND date( convert_tz(client_hit_timestamp,'".$server_tz."' ,'".$user_tz."' ) ) <= '".$end_date."' 
			AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'
			GROUP BY device";		
		}
		else{
			$sql = 
			"SELECT COUNT( log.id ) AS count, log.device AS device
			FROM log
			JOIN content ON log.content_id = content.id
			JOIN users ON content.publisher_id = users.id
			WHERE users.userid =  '".$pubid."'
			AND date( convert_tz(client_hit_timestamp,'".$server_tz."' ,'".$user_tz."' ) ) >= '".$start_date."'
			AND date( convert_tz(client_hit_timestamp,'".$server_tz."' ,'".$user_tz."' ) ) <= '".$end_date."' 
			AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'
			AND content.id in(".$contentId.")
			GROUP BY device";			
		}
		$objData = new DataHandler();

		$result = $objData->GetQuery($sql);
		/*
		_debug(__FUNCTION__);
		_debug($sql);
		_debug($result);
		*/
		if($result == -1){
			return false;
		}
		else{
			return $result;
		}
	}

	public function getPublisherInventorySoldGroupByContent($pubid, $start_date, $end_date){
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($pubid);

		$sql = "select  content.id as id,content.name as name,count(log.id) as count from 
		log join content on (log.content_id = content.id)
		where 
		date(convert_tz(log.client_hit_timestamp, '".$server_tz."', '".$user_tz."')) <= '".$end_date."' and
		date(convert_tz(log.client_hit_timestamp, '".$server_tz."', '".$user_tz."')) >= '".$start_date."' and
		log.ad_played_timestamp not like '0000-00-00 00:00:00'
		group by content.name
		order by content.id";

		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($pubid);

		$objData = new DataHandler();

		$result = $objData->GetQuery($sql);

		if($result == -1){
			return false;
		}
		else{
			return $result;
		}
	}

	public function getPublisherInventoryTotalGroupByContent($pubid, $start_date, $end_date){
		//_debug($sql);
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($pubid);

		$sql = "select content.id as id,content.name as name,count(log.client_id) as count  from 
		log join content on (log.content_id = content.id)
		where 
		date(convert_tz(log.client_hit_timestamp, '".$server_tz."', '".$user_tz."')) <= '".$end_date."' and
		date(convert_tz(log.client_hit_timestamp, '".$server_tz."', '".$user_tz."')) >= '".$start_date."' 
		group by content.name
		order by content.id";

		$objData = new DataHandler();

		$result = $objData->GetQuery($sql);

		if($result == -1){
			return false;
		}
		else{
			return $result;
		}	
	}

	public function getContentInventoryTotalByDate($pubid, $contentId, $start_date, $end_date){
		$inventorySql = "SELECT count(l.client_id) as count
		FROM  `log` l, content c
		WHERE l.content_id = c.id
		AND c.publisher_id = ".$pubid."
		AND c.id in (".$contentId.")
		AND date( l.client_hit_timestamp ) <= '".$end_date."'
		AND date( l.client_hit_timestamp ) >= '".$start_date."'";

		//_debug($inventorySql);
		$objData = new DataHandler();

		$res = $objData->GetQuery($inventorySql);

		if($res == -1){
			return false;
		}
		else
			return $res[0]['count'];
	}

	public function getContentInventoryTotal($pubid, $contentId){
		$inventorySql = "SELECT count(l.client_id) as count
		FROM  `log` l, content c
		WHERE l.content_id = c.id
		AND c.publisher_id = ".$pubid."
		AND c.id = ".$contentId."
		AND MONTH( l.client_hit_timestamp ) = MONTH( NOW( ) ) ";

		$objData = new DataHandler();

		$res = $objData->GetQuery($inventorySql);

		if($res == -1){
			return false;
		}
		else
			return $res[0]['count'];
	}

	public function getContentInventorySoldByDate($pubid, $contentId, $start_date, $end_date){
		$inventorySql = "SELECT count(l.client_id) as count
		FROM  `log` l, content c
		WHERE l.content_id = c.id
		AND c.publisher_id = ".$pubid."
		AND c.id in (".$contentId.")
		AND date( l.client_hit_timestamp ) <= '".$end_date."'
		AND date( l.client_hit_timestamp ) >= '".$start_date."'
		AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'";

		$objData = new DataHandler();

		$res = $objData->GetQuery($inventorySql);

		if($res == -1){
			return false;
		}
		else
			return $res[0]['count'];
	}

	public function getContentInventorySold($pubid, $contentId){
		$inventorySql = "SELECT count(l.client_id) as count
		FROM  `log` l, content c
		WHERE l.content_id = c.id
		AND c.publisher_id = ".$pubid."
		AND c.id = ".$contentId."
		AND MONTH( l.client_hit_timestamp ) = MONTH( NOW( ) ) 
		AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'";

		$objData = new DataHandler();

		$res = $objData->GetQuery($inventorySql);

		if($res == -1){
			return false;
		}
		else
			return $res[0]['count'];
	}

	public function getContentInventorySoldGroupedByContent($pubid){
		$inventorySql = "SELECT l.content_id as _content_id,count(l.client_id) as count
		FROM  `log` l, content c
		WHERE l.content_id = c.id
		AND c.publisher_id = ".$pubid."
		AND MONTH( l.client_hit_timestamp ) = MONTH( NOW( ) ) 
		AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00'
		group by _content_id";
		//_debug($inventorySql);
		$objData = new DataHandler();

		$res = $objData->GetQuery($inventorySql);

		if($res == -1){
			return false;
		}
		else
			return $res[0]['count'];
	}

	public function getContentInventoryTotalByDateGrouped($contentId, $start_date, $end_date, $groupby = ""){
		$dailyInventorySql = "";

		if($groupby == ""){
			$dailyInventorySql = "SELECT DAY( cal.dt ) as day, MONTH( cal.dt ) as month,year(cal.dt) as year, COUNT( l.client_id ) as count 
			FROM (select dt from calendar  where date(dt) <= '".$end_date."' and date (dt) >= '".$start_date."') as cal 
			left join ( select client_id as client_id  ,client_hit_timestamp, content_id from log where date(client_hit_timestamp) <= '".$end_date."' and date (client_hit_timestamp) >= '".$start_date."' and content_id in (".$contentId.") ) as l on date(cal.dt)=date(l.client_hit_timestamp) WHERE 1 
			group by(cal.dt)";	
		}
		else if($groupby == 'month'){
			$dailyInventorySql = "SELECT MONTH( cal.dt ) as month,year(cal.dt) as year, COUNT( l.client_id ) as count 
			FROM (select dt from calendar  where date(dt) <= '".$end_date."' and date (dt) >= '".$start_date."') as cal 
			left join ( select client_id as client_id  ,client_hit_timestamp, content_id from log where date(client_hit_timestamp) <= '".$end_date."' and date (client_hit_timestamp) >= '".$start_date."' and content_id in (".$contentId.") ) as l on date(cal.dt)=date(l.client_hit_timestamp) WHERE 1 
			group by month";
		}

		/*
		SELECT DAY( cal.dt ) as day, MONTH( cal.dt ) as month, COUNT( l.client_id ) as count
		FROM (SELECT dt FROM calendar WHERE DATE( dt ) <  '2012-10-22' AND DATE( dt ) >=  '2012-10-01' ) AS cal
		LEFT JOIN (SELECT DISTINCT (client_id) AS client_id, client_hit_timestamp, content_id FROM log WHERE DATE( client_hit_timestamp ) <  '2012-10-22'
		AND DATE( client_hit_timestamp ) >=  '2012-10-01' AND content_id =22) AS l ON DATE( cal.dt ) = DATE( l.client_hit_timestamp ) 
		WHERE 1 GROUP BY (	cal.dt)
		*/

		//_debug($dailyInventorySql);
		$objData = new DataHandler();

		$res = $objData->GetQuery($dailyInventorySql);

		if($res == -1){
			return false;
		}
		else{
			return $res;
		}

	}

	public function getContentInventorySoldByDateGrouped($contentId, $start_date, $end_date, $groupby=""){
		$dailyInventorySql = "";
		if($groupby == ""){
			$dailyInventorySql = "SELECT day(cal.dt) as day, month(cal.dt) as month,year(cal.dt) as year, count(l.client_id) as count
			FROM (select dt from calendar  where date(dt) <= '".$end_date."' and date (dt) >= '".$start_date."') as cal 
			left join ( select client_id as client_id  ,client_hit_timestamp, content_id from log where date(client_hit_timestamp) <= '".$end_date."' and date (client_hit_timestamp) >= '".$start_date."' and content_id in (".$contentId.") AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00') as l 
			on date(cal.dt)=date(l.client_hit_timestamp) 
			WHERE 1 
			group by(cal.dt)";
		}
		else if($groupby == "month"){
			$dailyInventorySql = "SELECT month(cal.dt) as month,year(cal.dt) as year, count(l.client_id) as count
			FROM (select dt from calendar  where date(dt) <= '".$end_date."' and date (dt) >= '".$start_date."') as cal 
			left join ( select client_id as client_id  ,client_hit_timestamp, content_id from log where date(client_hit_timestamp) <= '".$end_date."' and date (client_hit_timestamp) >= '".$start_date."' and content_id in (".$contentId.") AND ad_played_timestamp NOT LIKE  '0000-00-00 00:00:00') as l 
			on date(cal.dt)=date(l.client_hit_timestamp) 
			WHERE 1 
			group by month";	
		}

		//_debug($dailyInventorySql);
		$objData = new DataHandler();

		$res = $objData->GetQuery($dailyInventorySql);

		if($res == -1){
			return false;
		}
		else{
			return $res;
		}
	}

	public function getContentDailyReport($contentId) {
		$sql = "SELECT count(id) as count, day(client_hit_time) as day, month(client_hit_time) as month FROM `bi_content`
		WHERE content_id = " . $contentId . " and client_hit_time > DATE_ADD(NOW(), INTERVAL -15 DAY)
		group by day
		order by month, day asc";

		$objData = new DataHandler();
		$res = $objData->GetQuery($sql);

		if ($res == -1) {
			return false;
		} else {
			return $res;
		}
	}

	//put your code here
	public function getContentRevenueReport($contentId) {
		/*
		 echo $sql =
		"SELECT
		distinct(log.client_id),
		log.cpm as cpm,
		COUNT( log.client_id ) as count,
		SUM( log.cpm ) /1000 as revenue,
		MONTH( lcu.client_start_time ) as month
		FROM  `log` log,  `log_content_usage` lcu
		WHERE lcu.client_id = log.client_id
		AND lcu.content_id =".$contentId."
		GROUP BY MONTH( lcu.client_start_time ) ";
		*/
		$sql = "SELECT
		count( lcu.client_id ) AS count,
		log.cpm AS cpm,
		MONTH( lcu.client_start_time ) AS month
		FROM `log` log JOIN `log_content_usage` lcu
		ON lcu.client_id = log.client_id
		AND lcu.content_id =".$contentId."
		GROUP BY cpm,month";

		$objData = new DataHandler();
		$res = $objData->GetQuery($sql);
		if ($res == -1) {
			return NULL;
		} else {
			return $res;
		}
	}

	//this function gets last 15 days data for the campaign;
	public function CampaignReportGetDayData($id, $userid) {
		$timeZoneSql = "select timezone from users where userid like '" . $userid . "'";

		$objData = new DataHandler();

		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($userid);
		if ($_GET['debug']) {
			print_r($user_tz);
		}
		//$objCampaign = new Campaign();
		//$objCampaign->

		$sql = "SELECT count( derived_bi_users.id) AS count, DAY( dt) as DAY , month( dt) as MONTH "
		. "FROM calendar left join (select * from bi_users where campaign_id=" . $id . ") as derived_bi_users on date(dt)=date(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' ))"
		. "WHERE dt > DATE_ADD(NOW(), INTERVAL -15 DAY) and dt <= now() "
		. "GROUP BY day(dt) "
		. "ORDER BY month(dt),day(dt) ASC";

		if ($_GET['debug'] == 1) {
			echo "<pre>";
			print_r($sql);
			echo "</pre>";
		}

		$dayData = $objData->GetQuery($sql);

		if ($dayData == -1) {
			return FALSE;
		} else {
			return $dayData;
		}
	}

	public function CampaignReportGetHourlyData($campaignId, $userId) {

		$user_tz = UserInfo::GetUserTimeZone($userId);

		if ($user_tz == FALSE) {
			return "";
		}

		$server_tz = SERVER_TZ;

		$objData = new DataHandler();

		$hourlysql = "SELECT hour(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' )) as hour, count(id) as count FROM `bi_users` WHERE campaign_id=" . $campaignId . " group by hour(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' ))";
		$hourlyData = $objData->GetQuery($hourlysql);

		if ($hourlyData == -1) {
			return FALSE;
		} else {
			return $hourlyData;
		}
	}

	public function CampaignGetRequestedImpressions($campaignIds){
		$reqImpressionsSql = "SELECT sum(ad_spots) as sum FROM `campaign` where id in(".$campaignIds.")";

		$objData = new DataHandler();
		$res = $objData->GetQuery($reqImpressionsSql);

		if($res == -1){
			return false;
		}
		else{
			return $res[0]['sum'];
		}

	}

	public function CampaignGetTotalImpressionsByDate($userid, $campaignId, $start_date, $end_date){
		$user_tz = UserInfo::GetUserTimeZone($userid);
		
		if ($user_tz == FALSE) {
			return "";
		}
		
		$server_tz = SERVER_TZ;
		
		$sql = "select count(id) as total from log 
		where campaign_id in (".$campaignId.") 
		and ad_played_timestamp not like '0000-00-00 00:00:00'
		AND DATE( CONVERT_TZ( ad_played_timestamp,  '" . $server_tz . "',  '" . $user_tz . "' ) ) <= DATE( CONVERT_TZ( '".$end_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ) )
		AND DATE( CONVERT_TZ( ad_played_timestamp,  '" . $server_tz . "',  '" . $user_tz . "' ) ) >= DATE( CONVERT_TZ( '".$start_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ) )		";
		//_debug($sql);
		$objData = new DataHandler();
		
		$res = $objData->GetQuery($sql);
		if($res == -1){
			return 0;
		}
		else{
			return $res[0]['total'];
		}
	}

	public function CampaignGetTotalImpressions($campaignId){
		$sql = "select count(id) as total from log where campaign_id=".$campaignId." and ad_played_timestamp not like '0000-00-00 00:00:00'";
		$objData = new DataHandler();
		
		$res = $objData->GetQuery($sql);
		if($res == -1){
			return 0;
		}
		else{
			return $res[0]['total'];
		}
	}

	public function CampaignReportGetTopCountryData($campaignIds, $limit, $start_date, $end_date) {
		$objData = new DataHandler();

		$sql = "select country , count(id) as count from bi_users
		where campaign_id in (".$campaignIds.") and country not like ''
		and date(client_hit_time) <= '".$end_date."' and date(client_hit_time) >='".$start_date."'
		group by country
		order by count desc limit 0,".$limit;

		/*
		$sql = "select country , count(id) as count from bi_users
		where campaign_id='" . $campaignId . "' and country not like ''
		group by country
		order by count desc limit 0," . $limit;
		*/
		_debug($sql);

		$countryData = $objData->GetQuery($sql);

		if ($countryData == -1) {
			return "";
		} else {
			return $countryData;
		}
	}
	
	public function CampaignReportGetTopCityData($campaignIds, $limit, $start_date, $end_date) {
		$objData = new DataHandler();

		$sql = "SELECT city, count( id ) AS count
		FROM bi_users
		WHERE campaign_id in (" . $campaignIds . ")
		AND city NOT LIKE ''
		and date(client_hit_time) <= '".$end_date."' and date(client_hit_time) >='".$start_date."'
		GROUP BY city
		ORDER BY count DESC
		LIMIT 0 , " . $limit;

		
		$cityData = $objData->GetQuery($sql);

		if ($cityData == -1) {
			return "";
		} else {
			return $cityData;
		}
	}
	
	public function CampaignGetHourlyImpressions($campaignIds, $userId, $start_date, $end_date) {
		$user_tz = UserInfo::GetUserTimeZone($userId);

		if ($user_tz == FALSE) {
			return "";
		}

		$server_tz = SERVER_TZ;

		$objData = new DataHandler();

		$totalCountSql = "SELECT COUNT( id ) AS total
		FROM  `log`
		WHERE campaign_id in (".$campaignIds.")
		AND ad_played_timestamp not like '0000-00-00 00:00:00'
		AND DATE( CONVERT_TZ( client_hit_timestamp,  '" . $server_tz . "',  '" . $user_tz . "' ) ) <= DATE( CONVERT_TZ( '".$end_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ) )
		AND DATE( CONVERT_TZ( client_hit_timestamp,  '" . $server_tz . "',  '" . $user_tz . "' ) ) >= DATE( CONVERT_TZ( '".$start_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ) )		";

		$hourlysql = "SELECT
		hour(convert_tz(client_hit_timestamp,'" . $server_tz . "', '" . $user_tz . "' )) as hour,
		count(id) as count
		FROM `log`
		WHERE campaign_id in (" . $campaignIds . ")
		AND ad_played_timestamp not like '0000-00-00 00:00:00'
		AND date(convert_tz(client_hit_timestamp,'" . $server_tz . "', '" . $user_tz . "' )) >=  DATE( CONVERT_TZ( '".$start_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ))
		AND date(convert_tz(client_hit_timestamp,'" . $server_tz . "', '" . $user_tz . "' )) <=  DATE( CONVERT_TZ( '".$end_date."',  '" . $server_tz . "',  '" . $user_tz . "' ))		
		group by hour";

		$totalCount = $objData->GetQuery($totalCountSql);

		$hourlyData = array();

		if ($totalCount == -1) {
			return FALSE;
		} else {
			$hourlyData = $objData->GetQuery($hourlysql);
			$res['total'] = $totalCount[0]['total'];
			$res['hourly'] = $hourlyData;

			return $res;
		}
	}
	
	public function CampaignGetDailyImpressions($id, $userid, $start_date, $end_date) {	

		$objData = new DataHandler();

		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($userid);
		if ($_GET['debug']) {
			print_r($user_tz);
		}
		//$objCampaign = new Campaign();
		//$objCampaign->

		$currentMonthTotalSql = "SELECT COUNT( id ) AS total
		FROM log
		WHERE campaign_id in (".$id.")
		AND ad_played_timestamp not like '0000-00-00 00:00:00'
		AND DATE( CONVERT_TZ( client_hit_timestamp,  '" . $server_tz . "',  '" . $user_tz . "' ) ) <= DATE( CONVERT_TZ( '".$end_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ) )
		AND DATE( CONVERT_TZ( client_hit_timestamp,  '" . $server_tz . "',  '" . $user_tz . "' ) ) >= DATE( CONVERT_TZ( '".$start_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ) )";

		$sql = "SELECT count( derived_bi_users.id) AS count, DAY( dt) as DAY , month( dt) as MONTH, year(dt) as YEAR
		FROM calendar
		left join
		(select id, campaign_id, client_hit_timestamp from log where campaign_id in (" . $id . ") and ad_played_timestamp not like '0000-00-00 00:00:00') as derived_bi_users
		on date(dt)=date(convert_tz(client_hit_timestamp,'" . $server_tz . "', '" . $user_tz . "' ))
		WHERE DATE( CONVERT_TZ( dt,  '" . $server_tz . "',  '" . $user_tz . "' ) ) <= DATE( CONVERT_TZ( '".$end_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ) )
		AND DATE( CONVERT_TZ( dt,  '" . $server_tz . "',  '" . $user_tz . "' ) ) >= DATE( CONVERT_TZ( '".$start_date."' ,  '" . $server_tz . "',  '" . $user_tz . "' ) )
		GROUP BY day(dt)
		ORDER BY month(dt),day(dt) ASC";
		
		if ($_GET['debug'] == 1) {
			echo "<pre>";
			print_r($sql);
			echo "</pre>";
		}

		$totalCount = $objData->GetQuery($currentMonthTotalSql);
		if ($totalCount == -1) {
			return FALSE;
		} else {

			$dayData = $objData->GetQuery($sql);
			$ret['total'] = $totalCount[0]['total'];
			$ret['daily'] = $dayData;
			return $ret;
		}
	}

	public function CampaignReportGetAllCityData($campaignId) {
		$objData = new DataHandler();

		$sql = "SELECT city, count( id ) AS count
		FROM bi_users
		WHERE campaign_id = " . $campaignId . "
		AND city NOT LIKE ''
		GROUP BY city
		ORDER BY count DESC";

		$cityData = $objData->GetQuery($sql);

		if ($cityData == -1) {
			return "";
		} else {
			return $cityData;
		}
	}

	public function CampaignReportGetAllCountryData($campaignId) {
		$objData = new DataHandler();

		$sql = "select country , count(id) as count from bi_users
		where campaign_id='" . $campaignId . "' and country not like ''
		group by country
		order by count desc";

		$countryData = $objData->GetQuery($sql);

		if ($countryData == -1) {
			return "";
		} else {
			return $countryData;
		}
	}
	
	public function CampaignReportGetAllCityDataByCountry($campaignId, $country) {
		$objData = new DataHandler();

		$sql = "SELECT city, count( id ) AS count
		FROM bi_users
		WHERE campaign_id = " . $campaignId . " and country like '" . $country . "'
		AND city NOT LIKE ''
		GROUP BY city
		ORDER BY count DESC";

		$cityData = $objData->GetQuery($sql);

		if ($cityData == -1) {
			return "";
		} else {
			return $cityData;
		}
	}
	
	public function CampaignReportGetAllCityDataByCountryByDate($campaignId, $country, $start_date, $end_date) {
		$objData = new DataHandler();

		$sql = "SELECT city, count( id ) AS count
		FROM bi_users
		WHERE campaign_id in(" . $campaignId . ") and country like '" . $country . "'
		AND city NOT LIKE ''
		and date(client_hit_time) <= '".$end_date."' and date(client_hit_time) >='".$start_date."'
		GROUP BY city
		ORDER BY count DESC";

		$cityData = $objData->GetQuery($sql);

		if ($cityData == -1) {
			return "";
		} else {
			return $cityData;
		}
	}

	public function CampaignGetYesterdayImpressions($campaignId, $userId) {


		$user_tz = UserInfo::GetUserTimeZone($userId);

		if ($user_tz == FALSE) {
			return "";
		}

		$server_tz = SERVER_TZ;

		$objData = new DataHandler();

		$totalCountSql = "SELECT COUNT( id ) AS total
		FROM  `bi_users`
		WHERE campaign_id =".$campaignId."
		AND DATE( CONVERT_TZ( client_hit_time,  '" . $server_tz . "',  '" . $user_tz . "' ) ) = DATE( DATE_SUB( CONVERT_TZ( NOW( ) ,  '" . $server_tz . "',  '" . $user_tz . "' ) , INTERVAL 1 DAY ))";

		$hourlysql = "SELECT
		hour(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' )) as hour,
		count(id) as count
		FROM `bi_users`
		WHERE campaign_id=" . $campaignId . " AND
		date(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' )) =  DATE( DATE_SUB( CONVERT_TZ( NOW( ) ,  '" . $server_tz . "',  '" . $user_tz . "' ) , INTERVAL 1 DAY ))
		group by hour";

		$totalCount = $objData->GetQuery($totalCountSql);

		$hourlyData = array();

		if ($totalCount == -1) {

			return FALSE;
		} else {
			$hourlyData = $objData->GetQuery($hourlysql);
			$res['total'] = $totalCount[0]['total'];
			$res['hourly'] = $hourlyData;

			return $res;
		}
	}

	public function CampaignGetTodayImpressions($campaignId, $userId) {


		$user_tz = UserInfo::GetUserTimeZone($userId);

		if ($user_tz == FALSE) {
			return "";
		}

		$server_tz = SERVER_TZ;

		$objData = new DataHandler();

		$totalCountSql = "SELECT COUNT( id ) AS total
		FROM  `bi_users`
		WHERE campaign_id =".$campaignId."
		AND DATE( CONVERT_TZ( client_hit_time,  '" . $server_tz . "',  '" . $user_tz . "' ) ) = DATE( CONVERT_TZ( NOW( ) ,  '" . $server_tz . "',  '" . $user_tz . "' ) )";

		$hourlysql = "SELECT
		hour(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' )) as hour,
		count(id) as count
		FROM `bi_users`
		WHERE campaign_id=" . $campaignId . " AND
		date(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' )) =  DATE( CONVERT_TZ( NOW( ) ,  '" . $server_tz . "',  '" . $user_tz . "' ) )
		group by hour";

		$totalCount = $objData->GetQuery($totalCountSql);

		$hourlyData = array();

		if ($totalCount == -1) {

			return FALSE;
		} else {
			$hourlyData = $objData->GetQuery($hourlysql);
			$res['total'] = $totalCount[0]['total'];
			$res['hourly'] = $hourlyData;

			return $res;
		}
	}

	public function CampaignGetCurrentMonthData($id, $userid) {


		$objData = new DataHandler();

		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($userid);
		if ($_GET['debug']) {
			print_r($user_tz);
		}
		//$objCampaign = new Campaign();
		//$objCampaign->

		$currentMonthTotalSql = "SELECT COUNT( id ) AS total
		FROM bi_users
		WHERE MONTH( client_hit_time ) = MONTH( NOW( ) )
		AND campaign_id =" . $id;

		$sql = "SELECT count( derived_bi_users.id) AS count, DAY( dt) as DAY , month( dt) as MONTH
		FROM calendar
		left join
		(select id, campaign_id, client_hit_time from bi_users where campaign_id=" . $id . ") as derived_bi_users
		on date(dt)=date(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' ))
		WHERE month(dt)= month(NOW()) and date(dt) <= date(now())
		GROUP BY day(dt)
		ORDER BY month(dt),day(dt) ASC";

		if ($_GET['debug'] == 1) {
			echo "<pre>";
			print_r($sql);
			echo "</pre>";
		}

		$totalCount = $objData->GetQuery($currentMonthTotalSql);
		if ($totalCount == -1) {
			return FALSE;
		} else {

			$dayData = $objData->GetQuery($sql);
			$ret['total'] = $totalCount[0]['total'];
			$ret['daily'] = $dayData;
			return $ret;
		}
	}

	public function CampaignGetCurrentYearData($id, $userid) {


		$objData = new DataHandler();

		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($userid);
		if ($_GET['debug']) {
			print_r($user_tz);
		}
		//$objCampaign = new Campaign();
		//$objCampaign->

		$currentYearTotalSql = "SELECT COUNT( id ) AS total
		FROM bi_users
		WHERE year( client_hit_time ) = year( NOW( ) )
		AND campaign_id =" . $id;

		$sql = "SELECT MONTH( dt ) AS
		MONTH , COUNT( derived_bi_users.id ) AS count
		FROM calendar
		LEFT JOIN (	SELECT id, campaign_id, client_hit_time	FROM bi_users WHERE campaign_id =" . $id . ") AS derived_bi_users
		ON DATE( dt ) = DATE( CONVERT_TZ( client_hit_time,  '" . $server_tz . "',  '" . $user_tz . "' ) )
		WHERE YEAR( dt ) = YEAR( NOW( ) )
		AND MONTH( dt ) <= MONTH( NOW( ) )
		GROUP BY MONTH( dt )
		ORDER BY MONTH( dt ) ";

		if ($_GET['debug'] == 1) {
			echo "<pre>";
			print_r($sql);
			echo "</pre>";
		}

		$totalCount = $objData->GetQuery($currentYearTotalSql);
		if ($totalCount == -1) {
			return FALSE;
		} else {

			$dayData = $objData->GetQuery($sql);
			$ret['total'] = $totalCount[0]['total'];
			$ret['monthly'] = $dayData;
			return $ret;
		}
	}



	//content report functions start hereon


	public function ContentReportGetTopCountryData($contentId, $limit = 0) {
		$sql = "SELECT country, COUNT( id ) AS count
		FROM  `bi_content`
		WHERE content_id =" . $contentId . "
		AND country NOT LIKE  ''
		GROUP BY country
		ORDER BY count DESC";
		if ($limit != 0) {
			$sql = $sql . " LIMIT 0," . $limit;
		}
		if($_GET['debug'] == 1){
			echo "<br>".$sql;
		}
		$objData = new DataHandler();
		$result = $objData->GetQuery($sql);
		if ($result == -1) {
			return "";
		} else {
			return $result;
		}
	}

	public function ContentReportGetTopCityData($contentId, $limit = 0) {
		$sql = "SELECT city, COUNT( id ) AS count
		FROM  `bi_content`
		WHERE content_id =" . $contentId . "
		AND city NOT LIKE  ''
		GROUP BY city
		ORDER BY count DESC";
		if ($limit != 0) {
			$sql = $sql . " LIMIT 0," . $limit;
		}

		$objData = new DataHandler();
		$result = $objData->GetQuery($sql);
		if ($result == -1) {
			return "";
		} else {
			return $result;
		}
	}

	public function ContentReportGetAllCityDataByCountry($contentId, $country) {
		$objData = new DataHandler();

		$sql = "SELECT city, count( id ) AS count
		FROM bi_content
		WHERE content_id = " . $contentId . " and country like '" . $country . "'
		AND city NOT LIKE ''
		GROUP BY city
		ORDER BY count DESC";

		$cityData = $objData->GetQuery($sql);

		if ($cityData == -1) {
			return "";
		} else {
			return $cityData;
		}
	}

	public function ContentGetPrimeTimeData($ContentId, $userid) {
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($userid);

		$objData = new DataHandler();

		$primeTimeSql = "SELECT
		hour(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' )) as hour,
		count(id) as count
		FROM `bi_content`
		WHERE content_id='" . $ContentId . "'
		group by hour(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' ))";
		$arrPrimeTime = $objData->GetQuery($primeTimeSql);

		if ($arrPrimeTime == -1) {
			$arrPrimeTime = "";
		} else {
			return $arrPrimeTime;
		}
	}

	public function ContentGetYesterdayViewers($contentId, $userid) {
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($userid);

		$objData = new DataHandler();

		$yestTotalViewersSql = "SELECT COUNT( id ) as total
		FROM bi_content
		WHERE DATE(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' ) ) = DATE( DATE_SUB( convert_tz(NOW(),'" . $server_tz . "', '" . $user_tz . "' ) , INTERVAL 1 DAY ) )
		AND content_id =" . $contentId;

		$yestViewersSql = "SELECT
		hour(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' )) as hour,
		count(id) as count
		FROM `bi_content`
		WHERE content_id='" . $contentId . "' AND
		DATE(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' )) =  DATE( DATE_SUB( CONVERT_TZ( NOW( ) ,  '" . $server_tz . "',  '" . $user_tz . "' ) , INTERVAL 1 DAY ))
		group by hour(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' ))";

		$totalViewers = $objData->GetQuery($yestTotalViewersSql);



		if ($totalViewers != 0) {
			$arrViewers = $objData->GetQuery($yestViewersSql);
			if ($arrViewers == -1) {
				$arrViewers = array();
			}


			$ret['total'] = $totalViewers[0]['total'];
			$ret['hourly'] = $arrViewers;
			return $ret;
		}
	}

	public function ContentGetCurrentMonthViewers($contentId, $userid) {
		$server_tz = SERVER_TZ;

		$user_tz = UserInfo::GetUserTimeZone($userid);

		$objData = new DataHandler();

		$currentMonthTotalViewersSql = "SELECT COUNT( id ) as total
		FROM bi_content
		WHERE month(convert_tz(client_hit_time,'" . $server_tz . "', '" . $user_tz . "' ) ) = month( DATE_SUB( convert_tz(NOW(),'" . $server_tz . "', '" . $user_tz . "' ) , INTERVAL 1 DAY ) )
		AND content_id =" . $contentId;

		//echo "<br><br>";

		$currentMonthViewersSql = "SELECT MONTH( d_cal.dt ) , DAY( d_cal.dt ) AS DAY ,
		COUNT( d_bi_content.id ) AS count
		FROM (SELECT dt FROM calendar WHERE MONTH( dt ) = MONTH( CONVERT_TZ( NOW( ) ,  '".$server_tz."',  '".$user_tz."' ) )	) AS d_cal
		LEFT JOIN (SELECT id, content_id, client_hit_time	FROM bi_content	WHERE content_id =".$contentId.") AS d_bi_content
		ON DATE( d_cal.dt ) = DATE( CONVERT_TZ( d_bi_content.client_hit_time,  '".$server_tz."',  '".$user_tz."' ) )
		WHERE
		MONTH( d_cal.dt ) = MONTH( NOW( ) )
		AND DATE( d_cal.dt ) <= DATE( CONVERT_TZ( NOW( ) ,  '".$server_tz."',  '".$user_tz."' ) )
		GROUP BY DAY";

		$totalViewers = $objData->GetQuery($currentMonthTotalViewersSql);

		if ($totalViewers != 0) {
			$arrViewers = $objData->GetQuery($currentMonthViewersSql);
			if ($arrViewers == -1) {
				$arrViewers = array();
			}

			$ret['total'] = $totalViewers[0]['total'];
			$ret['daily'] = $arrViewers;
			return $ret;
		}
	}
}
?>
