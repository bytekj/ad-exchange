<?php 
require_once "../Classes/Content.php";
require_once "../Db/DataHandler.php";
require_once "../include/global.php";


$arrProfiles = Content::getContentProfiles();
//echo "<pre>";
echo json_encode($arrProfiles);
//echo "</pre>";

?>
