<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author kiran
 */
define("PROGRAM_START", 0);
define("PROGRAM_RUNNING", 1);
define("PROGRAM_END", 2);

class Log {

    //put your code here
    var $id;
    var $cpm;
    var $campaign_id;
    var $content_id;
    var $encoded_ad_name;
    var $client_ip;
    var $protocol;
    var $req_filename;
    var $client_hit_timestamp;
    var $device;
    var $ad_served_timestamp;
    var $ad_played_timestamp;
    var $program_flag;
    var $client_id;
    var $ssid;
    var $ua;

    public function add() {

        //        $sql = "call log(@id,
        //            '" . $this->campaign_id . "',
        //            '" . $this->content_id . "',
        //            '" . $this->encoded_ad_name . "',
        //            '" . $this->client_ip . "',
        //            '" . $this->protocol . "',
        //            '" . $this->req_filename . "',
        //            '" . $this->client_hit_timestamp . "',
        //            '" . $this->device . "',
        //            '" . $this->ad_served_timestamp . "',
        //            '" . $this->ad_played_timestamp . "'
        //            )";
        $sql = "call log(@id, '" . $this->cpm . "','" . $this->client_id . "','" . $this->campaign_id . "','" . $this->content_id . "', '" . $this->encoded_ad_name . "','" . $this->client_ip . "', '" . $this->req_filename . "',  NOW(), '" . $this->device . "', '" . $this->ad_served_timestamp . "', '" . $this->ad_played_timestamp . "', '" . $this->ssid . "', '" . $this->ua . "')";
        $sql = "call log(@id,'" . $this->cpm . "','" . $this->client_id . "','" . $this->campaign_id . "','" . $this->content_id . "','" . $this->encoded_ad_name . "','" . $this->client_ip . "','" . $this->protocol . "','" . $this->req_filename . "',NOW(),'" . $this->device . "','" . $this->ad_served_timestamp . "','" . $this->ad_played_timestamp . "','" . $this->ssid . "','" . $this->ua . "')";

        $return = 0;
        if ($_GET['debug'] == 1) {
            echo "<br>" . $sql;
            //return;
        }
        $objData = new DataHandler();
        $objData->connect();
        $res = $objData->Proc($sql);
        //        echo "-------<pre>";
        //        print_r($res);
        //        echo "</pre>-------";

        if ($res == 1) {
            //echo "here??";
            $outvars = "select @id as id";
            $outVarData = $objData->GetProcOutput($outvars);
            if ($_GET['debug'] == 1) {
                echo "------<pre>";
                print_r($outVarData);
                echo "</pre>-----";
            }
            $return = $outVarData[0]['id'];
        }
        $objData->Disconnect();
        //$this->updateContentLog();

        return $return;
    }
    public function t_add() {

        //        $sql = "call log(@id,
        //            '" . $this->campaign_id . "',
        //            '" . $this->content_id . "',
        //            '" . $this->encoded_ad_name . "',
        //            '" . $this->client_ip . "',
        //            '" . $this->protocol . "',
        //            '" . $this->req_filename . "',
        //            '" . $this->client_hit_timestamp . "',
        //            '" . $this->device . "',
        //            '" . $this->ad_served_timestamp . "',
        //            '" . $this->ad_played_timestamp . "'
        //            )";
        $sql = "call log(@id, '" . $this->cpm . "', '" . $this->client_id . "','" . $this->campaign_id . "','" . $this->content_id . "','" . $this->encoded_ad_name . "','" . $this->client_ip . "','" . $this->protocol . "','" . $this->req_filename . "','" . $this->client_hit_timestamp . "','" . $this->device . "','" . $this->ad_served_timestamp . "','" . $this->ad_played_timestamp . "','" . $this->ssid . "','" . $this->ua . "')";

        if ($_GET['debug'] == 1) {
            echo "<br>" . $sql;
            //return;
        }
        $objData = new DataHandler();
        $objData->connect();
        $res = $objData->Proc($sql);
        //        echo "-------<pre>";
        //        print_r($res);
        //        echo "</pre>-------";

        if ($res == 1) {
            //echo "here??";
            $outvars = "select @id as id";
            $outVarData = $objData->GetProcOutput($outvars);
            if ($_GET['debug'] == 1) {
                echo "------<pre>";
                print_r($outVarData);
                echo "</pre>-----";
            }
            $return = $outVarData[0]['id'];
        }
        $objData->Disconnect();
        //$this->updateContentLog();

        return $return;
    }

    public function updateContentLog() {
        $contentLogSql = "";
        $biSql = "";
        $objData = new DataHandler();
        switch ($this->program_flag) {
            case PROGRAM_START:

            $contentLogSql = "INSERT INTO `log_content_usage`
            (`id`,`content_id`,`client_id`,`client_start_time`,`client_stop_time`,`req_filename`,`client_ip`,`device`,`ssid`)
            VALUES
            (NULL,'" . $this->content_id . "','" . $this->client_id . "',NOW(),NULL,'" . $this->req_filename . "','" . $this->client_ip . "','" . $this->device . "','" . $this->ssid . "')";

            $ipDetail = $this->getIpDetails($this->client_ip);
            $biSql = "INSERT INTO `bi_content`
            (`id`, `content_id`, `country`, `city`, `client_hit_time`, `client_stop_time`, `device`) VALUES
            (NULL,'" . $this->content_id . "','" . $ipDetail->country_code . "','" . $ipDetail->city . "',NOW(),NULL,'" . $this->device . "')";

            break;
            case PROGRAM_RUNNING:
                //this means program is being watched
            break;
            case PROGRAM_END:
            $contentLogSql = "update log_content_usage set client_stop_time=NOW() where client_id=" . $this->client_id;

            break;
        }
        if ($_GET['debug'] == 1) {
            echo "<br>" . $contentLogSql;
            echo "<br>" . $biSql;
            return;
        }
        $objData->PutQuery($contentLogSql);
        $objData->PutQuery($biSql);
    }

    public function update() {
        if ($this->id) {
            $objData = new DataHandler();
            $objData->connect();
            //TODO convert this to stored procedure
            $logDetailsSql = "select content_id,campaign_id,client_ip,client_id,ssid,ua from log where id=" . $this->id;
            $logDetails = $objData->NoConnectGetQuery($logDetailsSql);

            $this->campaign_id = $logDetails[0]['campaign_id'];
            $this->client_ip = $logDetails[0]['client_ip'];
            $this->client_id = $logDetails[0]['client_id'];
            $this->conent_id = $logDetails[0]['content_id'];
            $this->device = Utils::getDeviceType($logDetails[0]['ua']);
            $this->ssid = $logDetails[0]['ssid'];

            $sql = "UPDATE log set ad_played_timestamp=NOW() where id='" . $this->id . "'";
            $ret = $objData->NoConnectPutQuery($sql);
         
            $ipDetails = $this->getIpDetails($this->client_ip);

            $strBI = "INSERT INTO `bi_users`
            (`id`,`campaign_id`, `country`, `city`, `client_hit_time`, `platform`)
            values(NULL,'" . $this->campaign_id . "','" . $ipDetails->country_name . "','" . $ipDetails->city . "',NOW() , '".$this->device."')";
            if ($_GET['debug'] == 1) {
                echo "<br>" . $strBI;
            }

            $objData->NoConnectPutQuery($strBI);

            $contentLogSql = "INSERT INTO `log_content_usage`
            (`id`,`content_id`,`client_id`,`client_start_time`,`client_stop_time`,`req_filename`,`client_ip`,`device`,`ssid`)
            VALUES
            (NULL,'" . $this->content_id . "','" . $this->client_id . "',NOW(),NULL,'" . $this->req_filename . "','" . $this->client_ip . "','" . $this->device . "','" . $this->ssid . "')";
            
            $biSql = "INSERT INTO `bi_content`
            (`id`, `content_id`, `country`, `city`, `client_hit_time`, `client_stop_time`, `device`) VALUES
            (NULL,'" . $this->content_id . "','" . $ipDetails->country_code . "','" . $ipDetails->city . "',NOW(),NULL,'" . $this->device . "')";

            if ($_GET['debug'] == 1) {
                echo "<br>" . $contentLogSql;
                echo "<br>" . $biSql;
                return;
            }

            $objData->NoConnectPutQuery($contentLogSql);
            $objData->NoConnectPutQuery($biSql);
            $objData->Disconnect();
        }
        return $ret;
    }
    public function t_update() {

        //$sql = "UPDATE log set ad_played_timestamp='" . $this->ad_played_timestamp . "' where id=" . $this->id;
        if ($this->id) {
            $objData = new DataHandler();

            //TODO convert this to stored procedure
            $logDetailsSql = "select campaign_id,client_ip from log where id=" . $this->id;
            $logDetails = $objData->GetQuery($logDetailsSql);


            $this->campaign_id = $logDetails[0]['campaign_id'];
            $this->client_ip = $logDetails[0]['client_ip'];



            $sql = "UPDATE log set ad_played_timestamp='".$this->ad_played_timestamp."' where id='" . $this->id . "'";
            $ret = $objData->PutQuery($sql);
            $ipDetails = $this->getIpDetails($this->client_ip);

            $strBI = "INSERT INTO `bi_users`
            (`id`,`campaign_id`, `country`, `city`, `client_hit_time`, `platform`)
            values(NULL,'" . $this->campaign_id . "','" . $ipDetails->country_name . "','" . $ipDetails->city . "','".$this->ad_played_timestamp."' , '".$this->device."')";
            if ($_GET['debug1'] == 1) {
                echo "<br>" . $strBI;
            }
            $objData->PutQuery($strBI);
        }


        return $ret;
    }


    public function t_updateContentLog() {
        $contentLogSql = "";
        $biSql = "";
        $objData = new DataHandler();
        switch ($this->program_flag) {
            case PROGRAM_START:

            $contentLogSql = "INSERT INTO `log_content_usage`
            (`id`,`content_id`,`client_id`,`client_start_time`,`client_stop_time`,`req_filename`,`client_ip`,`device`,`ssid`)
            VALUES
            (NULL,'" . $this->content_id . "','" . $this->client_id . "','".$this->client_hit_timestamp."',NULL,'" . $this->req_filename . "','" . $this->client_ip . "','" . $this->device . "','" . $this->ssid . "')";

            $ipDetail = $this->getIpDetails($this->client_ip);
            $biSql = "INSERT INTO `bi_content`
            (`id`, `content_id`, `country`, `city`, `client_hit_time`, `client_stop_time`, `device`) VALUES
            (NULL,'" . $this->content_id . "','" . $ipDetail->country_code . "','" . $ipDetail->city . "','".$this->client_hit_timestamp."',NULL,'" . $this->device . "')";

            break;
            case PROGRAM_RUNNING:
                //this means program is being watched
            break;
            case PROGRAM_END:
            $contentLogSql = "update log_content_usage set client_stop_time=NOW() where client_id=" . $this->client_id;

            break;
        }
        if ($_GET['debug'] == 1) {
            echo "<br>" . $contentLogSql;
            echo "<br>" . $biSql;
            return;
        }
        $objData->PutQuery($contentLogSql);
        $objData->PutQuery($biSql);
    }



    function getIpDetails($ipaddress) {
        global $gi;

        if ($_GET['debug'] == 1) {
            echo "<br>" . $ipaddress;
        }
        $result = GeoIP_record_by_addr($gi, $ipaddress);

        if ($result == NULL) {
            return false;
        } else {
            return $result;
        }
    }

    public function getImpressionsByCampaignId($campaignId) {
        $sql = "select count(id) as impressions from log where campaign_id=" . $campaignId;
        $objData = new DataHandler();
        $result = $objData->GetQuery($sql);

        if ($result == -1) {
            return false;
        } else {
            return $result[0]['impressions'];
        }
    }
}

?>
