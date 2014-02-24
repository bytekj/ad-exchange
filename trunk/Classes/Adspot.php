<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Adspot
 *
 * @author kiran
 */
class Adspot {

    //put your code here
    public function getCurrentAdspots() {
        $sql = "SELECT `current_spots` FROM `ad_spots` limit 0,1";
        $objData = new DataHandler();
        $result = $objData->GetQuery($sql);


        return $result[0]['current_spots'];
    }

    public function getAvailableAdspots() {
        $sql = "SELECT `available_spots` FROM `ad_spots` limit 0,1";
        $objData = new DataHandler();
        $result = $objData->GetQuery($sql);
        return $result[0]['available_spots'];
    }

    public function consumeAdspots($numSpots) {
        $sql = "update ad_spots set available_spots=available_spots-" . $numSpots . " where 1";
        $objData = new DataHandler();
        $result = $objData->PutQuery($sql);
        return $this->getAvailableAdspots();
    }

    public function addAdSpots($numSpots) {
        $sql = "update ad_spots set current_spots=current_spots+" . $numSpots . " where 1";
        $objData = new DataHandler();
        $result = $objData->PutQuery($sql);
        return $this->getAvailableAdspots();
    }

    public function getCurrentBidRate() {
        $sql = "SELECT `current_rate` FROM `ad_spots` limit 0,1";
        $objData = new DataHandler();
        $result = $objData->GetQuery($sql);
        return $result[0]['current_rate'];
    }

    public function increaseCurrentBidRate() {
        $sql = "update ad_spots set current_spots=current_spots+1 where 1";
        $objData = new DataHandler();
        $result = $objData->PutQuery($sql);
        return $this->getCurrentBidRate();
    }

}

?>
