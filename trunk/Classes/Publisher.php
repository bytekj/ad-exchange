<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Publisher
 *
 * @author kiran
 */
class Publisher extends UserInfo{
    var $content;
    //put your code here
    public function getCurrentPublisherInfo() {
        $this->getCurrentUserInfo();
    }
    
    public function getContent(){
        $objContent = new Content();
        return $objContent->getContentByPublisherId($this->userid);
    }
    
}

?>
