<?php

class Session {
    public function __construct() {
        session_start();
    }
    public function isUserLoggedIn() {
        //TODO check for login session variable
        
        if (isset($_SESSION['auth'])) {
            
            return true;
        } else {
            return false;
        }
    }
}

?>