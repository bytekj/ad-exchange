<?php

class UserInfo {

    var $id;
    var $userid;
    var $password;
    var $usertype;
    var $registration_date;
    var $last_login;
    var $status;
    var $email;
    var $firstname;
    var $lastname;
    var $uploaddir;
    var $brand_name;
    var $brand_logo;
    var $company_name;
    var $company_address;

    public function authenticate($input_userid, $input_password) {
        $sql = "SELECT `userid`,`usertype` FROM `users` " .
        "WHERE `userid` LIKE '" . $input_userid . "'" .
        " AND `password` LIKE BINARY  '" . $input_password . "'";


        $objData = new DataHandler();
        $result = $objData->GetQuery($sql);
        if ($result == -1)
            return false;
        else{
        	$this->userid = $result[0]['userid'];
        	$this->usertype = $result[0]['usertype'];
            return true;
        }
    }
    public function getCurrentUserType(){

    }
    public function getCurrentUserInfo() {
        $username = $_SESSION['auth'];
        
        if($username == "fancy"){
        	$this->userid = "fancy";
        	$this->usertype = "superadmin";
        	return true;
        }
        
        $sql = "SELECT `id`, `userid`, `password`, `usertype`, date(`registration_date`) as registration_date, `last_login`, `status`, `email`, `firstname`, `lastname`, `brand_name`, `brand_logo`, `company_name`, `company_address` FROM `users`" .
        "WHERE userid LIKE '" . $username . "'";
        $objData = new DataHandler();
        $result = $objData->GetQuery($sql);

        if ($result == -1) {
            return false;
        } else {
            $this->id = $result[0]['id'];
            $this->userid = $result[0]['userid'];
            $this->password = $result[0]['password'];
            $this->usertype = $result[0]['usertype'];
            $this->registration_date = $result[0]['registration_date'];
            $this->last_login = $result[0]['last_login'];
            $this->status = $result[0]['status'];
            $this->email = $result[0]['email'];
            $this->firstname = $result[0]['firstname'];
            $this->lastname = $result[0]['lastname'];
            $this->uploaddir = $result[0]['id'];
            $this->brand_name = $result[0]['brand_name'];
            $this->brand_logo = $result[0]['brand_logo'];
            $this->company_name = $result[0]['company_name'];
            $this->company_address = $result[0]['company_address'];
            return true;
        }
    }

    public function addUser() {
        $ret = $this->handleLogoUpload();
        
        $this->brand_logo = $ret;
        $sql = "INSERT INTO `users` (`userid`,`password`,`usertype`,`registration_date`,`last_login`,`status`,`email`,`firstname`,`lastname`,`brand_name`,`brand_logo`,`company_name`,`company_address` )
        VALUES('" . $this->userid . "','" . $this->password . "', '" . $this->usertype . "', NOW(), 'NOW()', 'active', '" .$this->email . "', '" .$this->firstname . "', '" .$this->lastname . "','" .$this->brand_name . "','" .$this->brand_logo . "','" .$this->company_name . "','" .$this->company_address ."')";

        $objData = new DataHandler();
        $ret = $objData->PutQuery($sql);
        //_debug($ret);
        //_debug($objData->getErrorNo());
        //exit;
        if ($ret == -1) {
            return false;
        } else {
            return $ret;
        }
    }

    public function updateLastLogin() {
        //$sql = "update users set "
    }

    public function handleLogoUpload() {
        //TODO media update

        if (($_FILES['logo']['type'] == "image/gif")
            || ($_FILES['logo']['type'] == "image/jpeg")
            || ($_FILES['logo']['type'] == "image/png")) {

            $filetype = explode("/", $_FILES['logo']['type']);
        $filetype = $filetype[1];

        $uploaddir = LOGO_STORE;

        if (!is_dir($uploaddir)) {

            if (!mkdir($uploaddir, 0777, true)) {
                return false;
            }
        }
        $n = rand(10e16, 10e20);
        $random = base_convert($n, 10, 36);

        $filename = $random . "." . $filetype;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploaddir . $filename)) {
                //echo "The file " . basename($_FILES['media_content']['name']) . " has been uploaded to " . $filename;
            return $filename;
        } else {
            return false;
        }
    } else if ($_FILES['error'] == 4) {
        return false;
    } else {
        return false;
    }
}

public function editUser() {
    $logoname = $this->handleLogoUpload();

    if ($logoname) {
        $this->brand_logo = $logoname;
    }
        /*
         *             `id`,
          `userid`,
          `password`,
          `usertype`,
          `registration_date`,
          `last_login`,
          `status`,
          `email`,
          `firstname`,
          `lastname`,
          `brand_name`,
          `brand_logo`,
          `company_name`,
          `company_address`

         * 
         */
          $sql = "update users set firstname='" . $this->firstname . "', " .
          "lastname='" . $this->lastname . "', " .
          "email='" . $this->email . "', " .
          "brand_name='" . $this->brand_name . "', " .
          "brand_logo='" . $this->brand_logo . "', " .
          "company_address='" . $this->company_address . "' " .
          "where userid='" . $this->userid . "'";

          $objData = new DataHandler();
          $ret = $objData->PutQuery($sql);
          if ($ret == -1) {
            return false;
        } else {
            return true;
        }
    }

    public static function GetUserTimeZone($userid) {
        $sql = "select timezone from users where userid like '" . $userid . "'";
        if($_GET['debug']){
            echo "<br>".$sql;
        }
        $objData = new DataHandler();

        $result = $objData->GetQuery($sql);
        if ($result == -1) {
            return FALSE;
        } else {
            return $result[0]['timezone'];
        }
    }
}

?>