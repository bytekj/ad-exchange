<?php

/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/

/**
 * Description of Advertiser
 *
 * @author kiran
 */

class Advertiser extends UserInfo {

	//put your code here


	public function __construct($objUserInfo=NULL){
		if($objUserInfo!=NULL){
			$this->userid = $objUserInfo->userid;
			$this->password = $objUserInfo->password;
			$this->usertype = $objUserInfo->usertype;
			$this->registration_date = $objUserInfo->registration_date;
			$this->last_login = $objUserInfo->last_login;
			$this->status = $objUserInfo->status;
			$this->email = $objUserInfo->email;
			$this->firstname = $objUserInfo->firstname;
			$this->lastname = $objUserInfo->lastname;
			$this->uploaddir = $objUserInfo->uploaddir;
			$this->brand_name = $objUserInfo->brand_name;
			$this->brand_logo = $objUserInfo->brand_logo;
			$this->company_name = $objUserInfo->company_name;
			$this->company_address = $objUserInfo->company_address;
		}
	}
	public function getCurrentAdvertiserInfo() {
		$this->getCurrentUserInfo();
		$sql = "SELECT * FROM `advertiser` WHERE userid='" . $this->userid . "'";
		$objData = new DataHandler();
		$res = $objData->GetQuery($sql);
		if ($res != -1) {
			$this->walletBalance = $res[0]['wallet_balance'];
			$this->walletTotal = $res[0]['wallet_total'];
		}
	}
	public function updateBalance(){
		$sql = "update advertiser set wallet_total='".$this->walletTotal."', wallet_balance='".$this->walletBalance."' where userid like '".$this->userid."'";
		$objData = new DataHandler();
		return $objData->PutQuery($sql);
	}
	public function getCampaignsByUserId(){
		$sql = "select id,name from campaign where advertiser_id like '".$this->userid."' and status not like 'deleted'";
		//_debug($sql);
		$objData = new DataHandler();
		$res = $objData->GetQuery($sql);
		return $res;
	}
}

?>
