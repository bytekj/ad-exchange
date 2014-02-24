<?php

require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Content.php';
require_once '../Db/DataHandler.php';
require_once '../include/global.php';

// echo "<pre>";
// print_r($_SERVER);
// echo "</pre>";
// exit;
$objSession = new Session();

if ($objSession->isUserLoggedIn()) {
	//TODO show add upload form
	$objUserInfo = new UserInfo();
	$objUserInfo->getCurrentUserInfo();
} else {
	//TODO show login form
	$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	header('Location: login.php?redirect_to=' . $pageURL);
}
?>


<?php

require 'header.php';

if($objUserInfo->usertype == 'superadmin'){
	
}
else{
	switch ($objUserInfo->usertype) {
		case "agent":
			require 'contentbody.php';
			break;
		case "advertiser":
			require 'advertiserdash.php';
			break;
		case "publisher":
			require 'publisherdash.php';
			break;
	}
}
require 'footer.php';
?>
