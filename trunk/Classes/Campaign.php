<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Campaign
 *
 * @author kiran
 */
class Campaign {

    //put your code here
    var $id;
    var $name;
    var $description;
    var $advertiser_id;
    var $status;
    var $start_date;
    var $end_date;
    var $genre;
    var $region;
    var $city;
    var $platform;
    var $cpm;
    var $ad_spots;
    var $preroll;
    var $type;

    public function update() {
        $objData = new DataHandler();
        $sql = "update campaign set end_date='" . $this->end_date . "', cpm=" . $this->cpm . ", ad_spots=" . $this->ad_spots." WHERE id=".$this->id;
        
        $ret = $objData->PutQuery($sql);
        if ($ret == -1) {
            return false;
        } else {

            echo $deleteSql = "delete from `campaign_parameters` where `campaign_id`=" . $this->id;
            $objData->PutQuery($deleteSql);

            $this->updateCampaignCity();
            $this->updateCampaignGenres();
            $this->updateCampaignPlatform();
            $this->updateCampaignRegion();
            return true;
        }
    }

    public function add() {
        $objData = new DataHandler();
        $objData->connect();

        $sql = "call add_campaign(@id,'" . $this->name . "','" . $this->description . "','" . $this->advertiser_id . "','" . $this->status . "','" . $this->start_date . "','" . $this->end_date . "','" . $this->cpm . "','" . $this->ad_spots . "')";
        _debug($sql);

        $ret = $objData->Proc($sql);
        if ($ret == 1) {
            //echo "here??";
            $outvars = "select @id as id";
            $outVarData = $objData->GetProcOutput($outvars);
// 			echo "------<pre>";
// 			print_r($outVarData);
// 			echo "</pre>-----";
            $return = $outVarData[0]['id'];
        }
        $objData->Disconnect();
        /*
 		echo "<pre>";
 		print_r($return);
 		echo "</pre>";
         * 
         */
        $this->id = $return;


        $this->updateCampaignGenres();
        $this->updateCampaignRegion();
        $this->updateCampaignCity();
        $this->updateCampaignPlatform();
// 		exit();
        return $this->id;
    }

    public function getAll() {
        $sql = "SELECT
        `id`,
        `name`,
        `description`,
        `advertiser_id`,
        `status`,
        `start_date`,
        `end_date`
        FROM `campaign` WHERE 1";
        $objData = new DataHandler();
        $res = $objData->GetQuery($sql);
        if ($res == -1)
            return false;
        else
            return $res;
    }
    
    public function getActiveCampaignsByAdvertiserIdAndStatus($advertiserId, $status = 'active'){
        $sql = "SELECT
        `id`,
        `name`,
        `description`,
        `advertiser_id`,
        `status`,
        `start_date`,
        `end_date`,
        `cpm`,
        `ad_spots`
        FROM `campaign` WHERE `advertiser_id` like '" . $advertiserId . "' and `status` like '".$status."'";

        $objData = new DataHandler();
        $res = $objData->GetQuery($sql);
        if ($res == -1)
            return false;
        else
            return $res;
    }

    

    public function getByAdvertiser($advertiserId) {
        /*
         * SELECT `id` , `name` , `description` , `advertiser_id` , `status` , `start_date` , `end_date`
          FROM `campaign`
          WHERE `advertiser_id` LIKE 'kiran'
          AND `status` = 'active'
         */

          $sql = "SELECT
          `id`,
          `name`,
          `description`,
          `advertiser_id`,
          `status`,
          `start_date`,
          `end_date`,
          `cpm`,
          `ad_spots`
          FROM `campaign` WHERE `advertiser_id` like '" . $advertiserId . "'";

          $objData = new DataHandler();
          $res = $objData->GetQuery($sql);
          if ($res == -1)
            return false;
        else
            return $res;
    }

    public function getByDate($startDate, $endDate) {

    }

    public function getAdvertiserCampaignById($advertiserId, $campaignId) {
        $sql =
        "SELECT
        c.`id`,
        c.`name`,
        c.`description`,
        c.`advertiser_id`,
        c.`status`,
        c.`start_date`,
        c.`end_date`,
        c.`cpm`,
        c.`ad_spots`,
        cp.`pref`,
        cp.`value`
        FROM `campaign` c, `campaign_parameters` cp
        WHERE
        c.`id`=" . $campaignId . " and
        c.`id`=cp.`campaign_id` and
        c.`advertiser_id` like '" . $advertiserId . "'";
        $objData = new DataHandler();
        $res = $objData->GetQuery($sql);
//         echo "<pre>";
//         print_r($res);
//         echo "</pre>";
        if ($res == -1) {
            return false;
        } else {
            $this->advertiser_id = $res[0]['advertiser_id'];
            $this->description = $res[0]['description'];
            $this->end_date = $res[0]['end_date'];
            $this->id = $res[0]['id'];
            $this->name = $res[0]['name'];
            $this->start_date = $res[0]['start_date'];

            $this->status = $res[0]['status'];
            $this->cpm = $res[0]['cpm'];
            $this->ad_spots = $res[0]['ad_spots'];

            foreach ($res as $key => $result) {
                //                echo "<pre>";
                //                print_r($result);
                //                echo "</pre>";
                switch ($result['pref']) {
                    case 'genre':
                    $this->genre[] = $result['value'];
                    break;
                    case 'region':
                    $this->region[] = $result['value'];
                    break;
                    case 'city':
                    $this->city[] = $result['value'];
                    break;
                    case 'platform':
                    $this->platform[] = $result['value'];
                    break;
                    case 'preroll':
                    $this->preroll = $result['value'];
                }
            }

            return true;
        }
    }

    public function updateCampaignGenres() {
        $sql = "INSERT INTO `campaign_parameters` (`id`, `campaign_id`, `pref`, `value`) VALUES";
        $objData = new DataHandler();

        /*
          echo "<pre>";
          print_r($this);
          echo "</pre>";
         */

          if ($this->genre[0] == "All" || $this->genre[0] == "all") {
            $genreSql = "select genre from genre_master where 1";
            $arrGenre = $objData->GetQuery($genreSql);

            foreach ($arrGenre as $key => $genre) {
                if ($key + 1 == sizeof($arrGenre)) {
                    $delim = "";
                }
                else
                    $delim = ",";
                $sql .= "(NULL, " . $this->id . ", 'genre', '" . $genre['genre'] . "')" . $delim;
            }
        }
        else {
            foreach ($this->genre as $key => $genre) {
                if ($key + 1 == sizeof($this->genre)) {
                    $delim = "";
                } else {
                    $delim = ",";
                }
                $sql .= "(NULL, " . $this->id . ", 'genre', '" . $genre . "')" . $delim;
            }
        }
        //echo "<br>".$sql;
        return $objData->PutQuery($sql);
    }

    public function updateCampaignRegion() {
        $sql = "INSERT INTO `campaign_parameters` (`id`, `campaign_id`, `pref`, `value`) VALUES";
        $objData = new DataHandler();
        if ($this->region[0] == "All" || $this->region[0] == "all") {
            $regionSql = "select region from region_master where 1";
            $arrRegion = $objData->GetQuery($regionSql);
            foreach ($arrRegion as $key => $region) {
                if ($key + 1 == sizeof($arrRegion)) {
                    $delim = "";
                } else {
                    $delim = ",";
                }
                $sql .= "(NULL, " . $this->id . ", 'region', '" . $region['region'] . "')" . $delim;
            }
        } else {
            foreach ($this->region as $key => $region) {
                if ($key + 1 == sizeof($this->region)) {
                    $delim = "";
                } else {
                    $delim = ",";
                }
                $sql .= "(NULL, " . $this->id . ", 'region', '" . $region . "')" . $delim;
            }
        }
        //echo $sql;
        return $objData->PutQuery($sql);
    }

    public function updateCampaignCity() {
        if($this->city != ""){
            $objData = new DataHandler();
            $sql = "INSERT INTO `campaign_parameters` (`id`, `campaign_id`, `pref`, `value`) VALUES";

            if ($this->city[0] == "All" || $this->city[0] == "all") {
                $citySql = "select tier from city_tier_master where 1";
                $arrCity = $objData->GetQuery($citySql);
// 			echo "<pre>";
// 			print_r($arrCity);
// 			echo "</pre>";
                foreach ($arrCity as $key => $city) {
                    if ($key + 1 == sizeof($arrCity)) {
                        $delim = "";
                    } else {
                        $delim = ",";
                    }
                    $sql .= "(NULL, " . $this->id . ", 'city', '" . $city['tier'] . "')" . $delim;
                }
            } else {
                foreach ($this->city as $key => $city) {
                    if ($key + 1 == sizeof($this->city)) {
                        $delim = "";
                    } else {
                        $delim = ",";
                    }
                    $sql .= "(NULL, " . $this->id . ", 'city', '" . $city . "')" . $delim;
                }
            }
// 		echo $sql;
            return $objData->PutQuery($sql);
        }
    }

    public function updateCampaignPlatform() {
        $sql = "INSERT INTO `campaign_parameters` (`id`, `campaign_id`, `pref`, `value`) VALUES";
        $objData = new DataHandler();

        if ($this->platform[0] == "All" || $this->platform[0] == "all") {
            $platformSql = "select platform from platform_master";
            $arrPlatform = $objData->GetQuery($platformSql);
            foreach ($arrPlatform as $key => $platform) {
                if ($key + 1 == sizeof($arrPlatform)) {
                    $delim = "";
                } else {
                    $delim = ",";
                }
                $sql .= "(NULL, " . $this->id . ", 'platform', '" . $platform['platform'] . "')" . $delim;
            }
        } else {
            foreach ($this->platform as $key => $platform) {
                if ($key + 1 == sizeof($this->platform)) {
                    $delim = "";
                } else {
                    $delim = ",";
                }
                $sql .= "(NULL, " . $this->id . ", 'platform', '" . $platform . "')" . $delim;
            }
        }

        return $objData->PutQuery($sql);
    }

    public function updateCampaignStatus($id, $status){
    	$sql == "";
    	if($status == 'delete'){
    		$sql = "UPDATE `campaign` SET `status` = 'deleted' WHERE `campaign`.`id` =" . $id;
    	}
    	else if($status == 'archive'){
    		$sql = "UPDATE `campaign` SET `status` = 'archived' WHERE `campaign`.`id` =" . $id;
    	}else if($status == 'pause'){
    		$sql = "UPDATE `campaign` SET `status` = 'paused' WHERE `campaign`.`id` =" . $id;
    	}else if($status == 'active'){
    		$sql = "UPDATE `campaign` SET `status` = 'active' WHERE `campaign`.`id` =" . $id;
    	}
    	
    	$objData = new DataHandler();
    	$ret = $objData->PutQuery($sql);
    	
    }
    
    public function pauseCampaign($id) {
        $sql = "UPDATE `campaign` SET `status` = 'paused' WHERE `campaign`.`id` =" . $id;

        $objData = new DataHandler();
        $ret = $objData->PutQuery($sql);
        if ($ret == -1) {
            return false;
        } else {
            return true;
        }
    }

    public function resumeCampaign($id) {
        $sql = "UPDATE `campaign` SET `status` = 'active' WHERE `campaign`.`id` =" . $id;

        $objData = new DataHandler();
        $ret = $objData->PutQuery($sql);
        if ($ret == -1) {
            return false;
        } else {
            return true;
        }
    }

    public function checkCampaignParam($paramType, $value) {
        $arrParam = array();

        switch ($paramType) {
            case 'genre':
            $arrParam = $this->genre;
            break;
            case 'platform':
            $arrParam = $this->platform;
            break;
            case 'region':
            $arrParam = $this->region;
            break;
            case 'city':
            $arrParam = $this->city;
            break;
        }
        foreach ($arrParam as $param) {
            if ($value == $param) {
                return true;
            }
        }
    }
    public function getiCampaignMinutes($campaignId){
        $sql = "select ad_spots from campaign where id=".$campaignId;
        $objData = new DataHandler();
        $result = $objData->GetQuery($sql);
        
        if($result == -1){
            return false;
        }
        else
        {
            return $result[0]['ad_spots'];
        }
    }
    
    public function getLastAddedCampaign($advertiserId){
        $lastAddedCampaignSql = "SELECT MAX( c.id ) as id
        FROM campaign c
        WHERE c.advertiser_id =  '".$advertiserId."'";
        $objData = new DataHandler();
        
        $res = $objData->GetQuery($lastAddedCampaignSql);
        if($res == -1){
            return FALSE;
        }else{
            return $res[0]['id'];
        }
    }
    public function getLastExcodedAd($campaignId){
    	$lastProfileAdSql = "SELECT ea.encoded_filename AS filename FROM encoded_ad ea, ad a WHERE a.campaign_id =".$campaignId."
        AND ea.original_filename = a.filename
        AND ea.profile_id = (SELECT max( id ) FROM profiles_master ) AND ea.encode_status =1";
        $objData = new DataHandler();
        $res = $objData->GetQuery($lastProfileAdSql);
        if($res == -1){
            return FALSE;
        }else{
            return $res[0]['filename'];
        }
    }
    public function getLowProfileXcodedAd($campaignId){

        $lowProXcodedAdSql = "SELECT MIN( pm.video_resolution ) , ea.encoded_filename as filename
        FROM encoded_ad ea, ad a, profiles_master pm
        WHERE a.campaign_id =".$campaignId."
        AND ea.original_filename = a.filename
        AND ea.profile_id = pm.id";

        $objData = new DataHandler();

        $res = $objData->GetQuery($lowProXcodedAdSql);

        if($res == -1){
            return FALSE;
        }else{
            return $res[0]['filename'];
        }
    }
    public function updateTags($campaignId, $tags){
        $sql = "delete from campaign_parameters where pref='tags'";
        $objData = new DataHandler();
        $objData->PutQuery($sql);

        $insertTagSql = "INSERT INTO `adex`.`campaign_parameters` (`campaign_id`, `pref`, `value`) VALUES ('".$campaignId."', 'tags', '".$tags."');";

        $objData->PutQuery($insertTagSql);
    }
    public function getCampaignTagsById(){
        $sql = "SELECT value
        FROM campaign_parameters
        WHERE campaign_id =".$this->id."
        AND pref = 'tags'";
        $objData = new DataHandler();
        $res = $objData->GetQuery($sql);
        if($res == -1){
            return "";
        }
        else{
            return $res[0]['value'];
        }
    }
}

?>
