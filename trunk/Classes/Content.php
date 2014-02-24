<?php

class Content {

    public $id;
    public $name;
    public $genre;
    public $ad_freq;
    public $publisher_id;
    public $region;
    public $content_profiles;
    public $language;

    public function Add(){

    	$sql = "INSERT INTO 
        `content`(`name`, `genre`, `publisher_id`, `ad_freq`, `region`, `lang`) 
        VALUES ('".$this->name."','".$this->genre."','".$this->publisher_id."','".$this->ad_freq."','".$this->region."','".$this->language."')";
        //_debug($sql);


        $objData = new DataHandler();
        $objData->connect();
        $res = $objData->NoConnectPutQuery($sql);
        if($res == -1){
            $objData->Disconnect();
            return false;
        }
        else{
            $contentId = $objData->LastInsertId();
            //echo $contentId;
            $publisherProfilesSql = "select id from publisher_profiles where publisher_id=".$this->publisher_id;
            $publisherProfiles = $objData->NoConnectGetQuery($publisherProfilesSql);
            /*
            echo "<pre>";
            print_r($publisherProfiles);
            echo "</pre>";
            */
            $contentProfSql = "insert into content_profile(`content_id`,`publisher_profile_id`) values ";
            $values = "";
            $size = sizeof($publisherProfiles);
            foreach ($publisherProfiles as $key => $publisherProfile) {
                $delim = ",";
                if($key == $size-1){
                    $delim = "";
                }
                $values .= "(".$contentId.",".$publisherProfile['id'].")".$delim;
            }
            $contentProfSql .= $values;
            
            $res = $objData->NoConnectPutQuery($contentProfSql);

            if($res == -1)
                return false;
            else
                return true;
        }
    }

    public function deleteChannel($contentId){

        $this->deleteStream($contentId);

        $deleteContenProfileSql = "delete from content_profile where content_id=".$contentId;
        $deleteContentSql = "delete from content where id=".$contentId;
        /*
        _debug($deleteContenProfileSql);
        _debug($deleteContentSql);
        */
        $objData = new DataHandler();
        $objData->connect();
        $objData->NoConnectPutQuery($deleteContenProfileSql);
        $objData->NoConnectPutQuery($deleteContentSql);
        $objData->Disconnect();
    }

    public function deleteStream($contentId){
        $deleteContentStream = "delete from channels where cid=".$contentId;
        //_debug($deleteContentStream);
        $objData = new DataHandler();
        $objData->PutQuery($deleteContentStream);
    }

    public function addStream($pubid,$name,$sourceIp, $path, $wowzaIp, $stream_status){
        if($stream_status == 'Enable'){
            $stream_status = 1;
        }
        else{
            $stream_status = 0;
        }

        $streamInsertSql = "INSERT INTO 
        `channels`(`pubid`, `cid`, `name`, `source_ip`, `path`, `ad_status`, `nz`,  `ad_directory`, `ingest_ip`, `chstatus`) 
        VALUES ('".$pubid."',".$this->id.",'".$name."','".$sourceIp."','".$path."','Enabled',20,'/usr/local/WowzaMediaServer/content/ads/','".$wowzaIp."','".$stream_status."')";
        

        $objData = new DataHandler();

        $res = $objData->PutQuery($streamInsertSql);
        if($res == -1){
            return false;
        }
        else{
            return $res;
        }
    }
    public function getAllStreamsByPublisherId($pubid, $offset, $length){
        $sql = "SELECT c.id as cid,c.name AS content, ch.`name` AS stream, ch.`source_ip` , ch.`path` , ch.`ingest_ip` , ch.`chstatus` 
        FROM content c, channels ch
        WHERE c.publisher_id =".$pubid." and c.id = ch.cid
        ORDER BY content";
        if($length != 0){
            $sql .= " LIMIT ".$offset." , ".$length;
        }

        $objData = new DataHandler();

        $res = $objData->GetQuery($sql);

        if($res == -1){
            return false;
        }
        else
            return $res;
        
    }
    public function getStreams($pubid, $contentId){
        $streamSql = "SELECT  `pubid` ,  `cid` ,  `name` ,  `source_ip` ,  `path` ,  `ad_status` ,  `nz` ,  `commport` ,  `ad_directory` ,  `ingest_ip` ,  `chstatus` 
        FROM  `channels` 
        WHERE pubid =  '".$pubid."'
        AND cid =  '".$contentId."'";
        $objData = new DataHandler();

        $res = $objData->GetQuery($streamSql);

        if($res == -1){
            return false;
        }
        else{
            return $res;
        }
    }

    public function updateStreamStatus($pubid, $contentId, $stream, $status){
        if($status == 'Enable'){
            $status = 1;
        }
        else{
            $status = 0;
        }
        $updateStreamSql = "update channels set chstatus=".$status." where pubid='".$pubid."' and name='".$stream."' and cid=".$contentId;
        $objData = new DataHandler();

        $res = $objData->PutQuery($updateStreamSql);
        if($res == -1){
            return false;
        }
        else{
            return true;
        }
    }


    public function updateStreamStatusByStreamName($pubid, $stream, $status){
        if($status == 'Enable'){
            $status = 1;
        }
        else{
            $status = 0;
        }
        $updateStreamSql = "update channels set chstatus=".$status." where pubid='".$pubid."' and name='".$stream."'";
        $objData = new DataHandler();

        $res = $objData->PutQuery($updateStreamSql);
        if($res == -1){
            return false;
        }
        else{
            return true;
        }
    }

    public function getConfigFiles() {
        $sql = "select config from profiles_master where 1";
        $objData = new DataHandler();
        $res = $objData->GetQuery($sql);
        return $res;
    }

    public function getEncodingProfiles() {
        $sql = "select id,config from profiles_master where 1";
        $objData = new DataHandler();
        $res = $objData->GetQuery($sql);
        return $res;
    }

    public function getAdSpots($regions = '', $genres = '') {
        $regionStr = '';
        $genreStr = '';

        $regionLength = sizeof($regions);
        //echo $regionLength;
        for ($i = 0; $i < $regionLength; $i++) {
            if ($i == ($regionLength - 1)) {
                $regionStr.="'" . $regions[$i] . "'";
            } else {
                $regionStr.="'" . $regions[$i] . "',";
            }
        }

        $genreLength = sizeof($genres);
        for ($i = 0; $i < $genreLength; $i++) {
            if ($i == ($genreLength - 1)) {
                $genreStr.="'" . $genres[$i] . "'";
            } else {
                $genreStr.="'" . $genres[$i] . "',";
            }
        }

        if ($genreLength != 0 && $regionLength != 0) {
            $sql = "SELECT sum( ad_spots ) AS ad_spots FROM content WHERE region IN (" . $regionStr . ") AND genre IN (" . $genreStr . ")";
        } else if ($regionLength == 0 && $genreLength != 0) {
            $sql = "SELECT sum( ad_spots ) AS ad_spots FROM content WHERE  genre IN (" . $genreStr . ")";
        } else if ($regionLength != 0 && $genreLength == 0) {
            $sql = "SELECT sum( ad_spots ) AS ad_spots FROM content WHERE region IN (" . $regionStr . ")";
        } else {
            $sql = "SELECT sum( ad_spots ) AS ad_spots FROM content WHERE 1";
        }

        //echo $sql;
        $objData = new DataHandler();
        $res = $objData->GetQuery($sql);
        //        echo "<pre>";
        //        print_r($res);
        //        echo "</pre>";
        if ($res[0]['ad_spots'] == NULL) {
            return 0;
        } else {
            return $res[0]['ad_spots'];
        }
    }

    public function getContentByPublisherId($publisherId) {
        $sql = "select
        c.id as id,
        c.name as name,
        c.ad_freq as ad_freq, 
        g.genre_name as genre
        from 
        users u, 
        content c, 
        genre_master g
        where 
        u.userid like '" . $publisherId . "' and 
        u.id=c.publisher_id and 
        c.genre=g.id ";

        //$sql = "select * from content where publisher_id like '" . $publisherId . "'";
        $objData = new DataHandler();

        $res = $objData->GetQuery($sql);
        
        if ($res == -1) {
            return false;
        } else {

            return $res;


        }
    }
    public function getContentByPublisherIdWithLimits($publisherId, $offset, $limit) {

        $sql = "select
        c.id as id,
        c.name as name,
        c.ad_freq as ad_freq, 
        g.genre_name as genre,
        c.region,
        l.lang_name as language
        from 
        users u, 
        content c, 
        genre_master g ,
        language l
        where 
        u.userid like '" . $publisherId . "' and 
        u.id=c.publisher_id and 
        c.genre=g.id and
        c.lang=l.id
        order by name 
        limit ".$offset.",".$limit;
        //_debug($sql);
        //$sql = "select * from content where publisher_id like '" . $publisherId . "'";
        $objData = new DataHandler();
        $objData->connect();

        $res = $objData->NoConnectGetQuery($sql);
        if ($res == -1) {
            return false;
        } else {
            $regionSql = "SELECT `id`, `region_name`, `region` FROM `region_master` WHERE 1";
            $arrRegions = $objData->NoConnectGetQuery($regionSql);
            //_debug($arrRegions);
            foreach ($res as $key => $content) {
                $intRegions = explode(",",$content['region']) ;
                $content['region'] = "";
                //_debug(sizeof($arrRegions));
                foreach ($intRegions as $k => $intRegion) {
                    $delim = ",";
                    //_debug($i);
                    if((sizeof($intRegions))-1 == $k){
                        $delim = "";
                    }
                    foreach ($arrRegions as $i => $region) {

                        if($intRegion == $region['id']){
                            //_debug($intRegion);

                            $content['region'] .= $region['region_name'].$delim;

                        }
                    }
                }
                $res[$key] = $content;
            }
            //_debug($res);
            $objData->Disconnect();
            return $res;
        }
    }

    public function AddProfileToContent($contentId, $publisherProfId){

        if(is_array($publisherProfId)){
            /*
            echo "<pre>";
            print_r($publisherProfId);
            echo "</pre>";
            */
            $sql = "INSERT INTO `content_profile`(`content_id`, `publisher_profile_id`) 
            VALUES ";

            $value = "";
            $size = sizeof($publisherProfId);
            foreach ($publisherProfId as $key => $profile_id) {
                $delim = ",";
                if($key == ($size-1))
                    $delim = "";
                $value .= "(".$contentId.",".$publisherProfId[$key].")".$delim;
            }

            $sql = $sql.$value;
            
        }
        else{
            $sql = "INSERT INTO `content_profile`(`content_id`, `publisher_profile_id`) 
            VALUES (".$contentId.",".$publisherProfId.")";

        }
        $objData = new DataHandler();

        $res = $objData->PutQuery($sql);
        if($res == -1){
            return FALSE;
        }
    }

    public function RemoveProfileFromContent($contentId, $publisherProfId){
        $sql = "delete from `content_profile` where `content_id`=".$contentId." AND `publisher_profile_id`=".$publisherProfId;

        $objData = new DataHandler();

        $res = $objData->PutQuery($sql);

        if($res == -1){
            return FALSE;
        }
    }

    public function getPubliserProfilesNotAddedToContent($contentId, $publisherId){

        $publisherProfSql = "SELECT pp.id as profile_id, pp.profile_name as profile_name 
        FROM publisher_profiles pp, users u WHERE u.userid like '".$publisherId."' and u.id=pp.publisher_id";

        $contentProfSql = "SELECT cp.content_id, cp.publisher_profile_id as profile_id 
        FROM content_profile cp, publisher_profiles pp
        WHERE cp.publisher_profile_id = pp.id
        AND cp.content_id =".$contentId;

        $objData = new DataHandler();

        $arrPublisherProf = $objData->GetQuery($publisherProfSql);

        /*
        echo "<pre>";
        print_r($arrPublisherProf);
        echo "</pre>";
        echo "---------------------------";
        */

        $arrContentProf = $objData->GetQuery($contentProfSql);
        
        /*
        echo "<pre>";
        print_r($arrContentProf);
        echo "</pre>";
        */

        $arrMissingProfs = array();

        $flag = FALSE;
        if($arrPublisherProf == -1 ){
            return FALSE;
        }
        else{
            foreach ($arrPublisherProf as $key=> $publisherProf) {
                $flag = FALSE;
                foreach ($arrContentProf as $contentProf) {
                    # code...
                    if($publisherProf['profile_id'] == $contentProf['profile_id']){
                        $flag = true;  
                        break;
                    }
                    
                }
                if($flag == FALSE){
                    $arrMissingProfs[$key]['profile_id'] = $publisherProf['profile_id'];
                    $arrMissingProfs[$key]['profile_name'] = $publisherProf['profile_name'];
                }
                
            }
        }
        return $arrMissingProfs;

    }
    public function getPublisherContentByPublisherId($publisherId, $contentId) {
        $sql = "select
        c.`id` as id,
        c.`name` as name, 
        c.`ad_freq` as ad_freq,
        c.`preroll` as preroll,
        gm.`genre_name` as genre,
        rm.`region_name` as region,
        pp.`id` as ppid,	
        cp.`id` as cpid,
        pp.`profile_name` as p_name, 
        pm.`video_resolution` as vid_res, 
        pm.`video_bit_rate` as vid_bit_rate, 
        pm.`video_codec` as vid_codec, 
        pm.`video_fps` as vid_fps, 
        pm.`audio_bit_rate` as aud_bit_rate, 
        pm.`audio_sampling_rate` as aud_rate, 
        pm.`audio_channels` as aud_ch, 
        pm.`audio_codec` as aud_codec 
        from 
        content c, 
        publisher_profiles pp, 
        profiles_master pm, 
        users u, 
        content_profile cp,
        genre_master gm,
        region_master rm
        where 
        u.userid = '" . $publisherId . "' and 
        u.id=c.publisher_id and 
        c.id=cp.content_id and 
        cp.publisher_profile_id=pp.id and 
        pp.profile_id=pm.id and
        gm.id=c.genre and
        rm.id=c.region and
        c.id='" . $contentId . "'";
        $objData = new DataHandler();
        $ret = $objData->GetQuery($sql);

        $this->id = $ret[0]['id'];
        $this->name = $ret[0]['name'];
        $this->ad_freq = $ret[0]['ad_freq'];
        $this->publisher_id = $publisherId;
        $this->region = $ret[0]['region'];
        $this->genre = $ret[0]['genre'];


        if ($ret == -1) {
            return false;
        } else {
            return $ret;
        }
    }

    public function checkPublisherContentByPublisherId($publisherId, $contentId){
        $sql = "select
        c.`id` as id,
        c.`name` as name, 
        c.`ad_freq` as ad_freq,
        c.`preroll` as preroll,
        c.region,
        l.lang_name as language,
        gm.`genre_name` as genre
        from 
        content c, 
        users u, 
        genre_master gm,
        language l
        where 
        u.userid = '".$publisherId."' and 
        u.id=c.publisher_id and 
        gm.id=c.genre and
        l.id=c.lang and
        c.id in (".$contentId.")";
        //_debug($sql);
        $objData = new DataHandler();
        $ret = $objData->GetQuery($sql);

        if ($ret == -1) {
            return false;
        } else {
            return $ret;
        }
    }

    public function getPublisherContentByPublisherIdWithoutprofs($publisherId, $contentId){
        $sql = "select
        c.`id` as id,
        c.`name` as name, 
        c.`ad_freq` as ad_freq,
        c.`preroll` as preroll,
        c.region,
        l.lang_name as language,
        gm.`genre_name` as genre
        from 
        content c, 
        users u, 
        genre_master gm,
        language l
        where 
        u.userid = '".$publisherId."' and 
        u.id=c.publisher_id and 
        gm.id=c.genre and
        l.id=c.lang and
        c.id in (".$contentId.")";
        //_debug($sql);
        $objData = new DataHandler();
        $ret = $objData->GetQuery($sql);

        $this->id = $ret[0]['id'];
        $this->name = $ret[0]['name'];
        $this->ad_freq = $ret[0]['ad_freq'];
        $this->publisher_id = $publisherId;
        $this->region = $ret[0]['region'];
        $this->genre = $ret[0]['genre'];


        if ($ret == -1) {
            return false;
        } else {
            return $ret;
        }

    }
    public function getPublisherContentByPublisherIdOld($publisherId, $contentId) {

        echo $contentSql = "SELECT
        `id`, 
        `name`, 
        `genre`, 
        `publisher_id`, 
        `ad_freq`, 
        `region` 
        FROM `content` 
        WHERE `publisher_id` like '" . $publisherId . "' and 
        id='" . $contentId . "'";

        $objData = new DataHandler();
        $content = $objData->GetQuery($contentSql);
        if ($content == -1) {
            return false;
        } else {

            $profileSql = "SELECT
            `id`, 
            `filename`, 
            `content_id`, 
            `publisher_id`, 
            `video_resolution`, 
            `video_bit_rate`, 
            `video_codec`, 
            `video_fps`, 
            `audio_bit_rate`, 
            `audio_sampling_rate`, 
            `audio_channels`, 
            `audio_codec`, 
            `config`, 
            `ssid`, 
            `ssip` 
            FROM `content_profiles` WHERE content_id like '" . $contentId . "'";
            $profiles = $objData->GetQuery($profileSql);
            $content[0]['profiles'] = $profiles;
            return $content;
        }
    }

    public function updatePrerollOption($id, $value) {

        $v = 0;
        if ($value == "enabled") {
            $v = 1;
        }
        else
            $v = 0;
        $sql = "update content set preroll='" . $v . "' where id='" . $id . "'";


        $objData = new DataHandler();
        $res = $objData->PutQuery($sql);
        if ($res == -1) {
            return false;
        } else {
            return true;
        }
    }

    public function updateAdFreq($id, $freq) {
        $sql = "update content set ad_freq='" . $freq . "' where id='" . $id . "'";
        $objData = new DataHandler();
        $res = $objData->PutQuery($sql);
        if ($res == -1) {
            return false;
        } else {
            return true;
        }
    }

    public static function getContentProfilesById($id) {
        $sql = "select * from profiles_master where id=" . $id;
        $objData = new DataHandler();
        $ret = $objData->GetQuery($sql);
        if ($ret == -1) {
            return false;
        } else {
            return $ret;
        }
    }

    public static function getContentProfiles($pubid = "") {
        if ($_GET['debug']) {
            echo "<pre>";
            print_r($pubid);
            echo "</pre>";
        }
        $sql = "";

        if ($pubid == "") {


            $sql = "SELECT 
            pp.`id`, 
            pm.`video_resolution`, 
            pm.`video_bit_rate`, 
            pm.`video_codec`, 
            pm.`video_fps`, 
            pm.`audio_bit_rate`, 
            pm.`audio_sampling_rate`, 
            pm.`audio_channels`, 
            pm.`audio_codec`, 
            pm.`config`, 
            pm.`stream_type`, 
            pm.`pixel_aspect_ratio`, 
            pm.`key_int_max`, 
            pm.`cabac_flag`, 
            pm.`audio_codec_profile` ,
            pm.`aacformat`,
            pm.`format`
            FROM 
            `profiles_master` pm , `publisher_profiles` pp 
            WHERE pp.profile_id = pm.id";
        } else {

            $sql = "SELECT
            pp.`id`,
            pm.`video_resolution`,
            pm.`video_bit_rate`,
            pm.`video_codec`,
            pm.`video_fps`,
            pm.`audio_bit_rate`,
            pm.`audio_sampling_rate`,
            pm.`audio_channels`,
            pm.`audio_codec`,
            pm.`config`,
            pm.`stream_type`,
            pm.`pixel_aspect_ratio`,
            pm.`key_int_max`,
            pm.`cabac_flag`,
            pm.`audio_codec_profile`,
            pm.`aacformat`,
            pm.`format`
            FROM
            `profiles_master` pm , `publisher_profiles` pp, `users` u
            WHERE pp.profile_id = pm.id AND
            pp.publisher_id = u.id AND
            u.userid='" . $pubid . "'";
        }
        if ($_GET['debug']) {
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
        }
        $objData = new DataHandler();
        $ret = $objData->GetQuery($sql);
        if ($ret == -1) {
            return false;
        } else {
            return $ret;
        }
    }

    public function GetLastAddedContent($pubid) {
        $lastContentSql = "SELECT MAX( c.id ) as id
        FROM content c, users u
        WHERE c.publisher_id = u.id
        AND u.userid =  '" . $pubid . "'";

        $objData = new DataHandler();
        $res = $objData->GetQuery($lastContentSql);

        if ($res == -1) {
            return FALSE;
        } else {
            return $res[0]['id'];
        }
    }

    public function GetPublisherByPublisherProfile($pubProfile) {
        $ppSql = "SELECT u.userid as userid
        FROM users u,  `publisher_profiles` pp
        WHERE pp.publisher_id = u.id
        AND pp.id =" . $pubProfile;
        $objData = new DataHandler();

        $res = $objData->GetQuery($ppSql);

        if ($res == -1) {
            return FALSE;
        } else {
            return $res['0']['userid'];
        }
    }
    public function updateContentTags($contentId, $tags){
        $updateSql = "update content set tags='".$tags."' where id=".$contentId;
        $objData = new DataHandler();
        $objData->PutQuery($updateSql);
        
    }
    public function getContentTagsById($contentId) {
        $tagsSql = "select tags from content where id=" . $contentId;
        $objData = new DataHandler();
        $res = $objData->GetQuery($tagsSql);
        if ($res == -1) {
            return FALSE;
        } else {
            return $res[0]['tags'];
        }
    }

}

?>
