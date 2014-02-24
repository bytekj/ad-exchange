<?php

require_once "../Classes/Content.php";
require_once "../Db/DataHandler.php";
require_once "../include/global.php";
$pubid = "";
$arrProfiles = "";
if (isset($_GET['pubid'])) {
    $pubid = $_GET['pubid'];
}

if ($pubid == "") {
    $arrProfiles = Content::getContentProfiles();
} else {
    $arrProfiles = Content::getContentProfiles($pubid);
}
//echo "<pre>";
echo json_encode($arrProfiles);
//echo "</pre>";
?>
