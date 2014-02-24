<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<?php
require_once '../Classes/Session.php';
require_once '../Classes/UserInfo.php';
require_once '../Classes/Content.php';
require_once '../Db/DataHandler.php';
$message = "";
$objSession = new Session();
if ($objSession->isUserLoggedIn()) {

    $objUserInfo = new UserInfo();
    $objUserInfo->getCurrentUserInfo();
} else {

    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    header('Location: login.php?redirect_to=' . $pageURL);
}

if ($_POST['text_submit']) {
    //TODO text update
    $objContent = new Content();

    if ($objContent->handleTextUpdate()) {
        $message = "<label class='uploadsuccess'>Text updated</label>";
    } else {
        $message = "<label class='uploadfail'>Update failed</label>";
    }
} else if ($_POST['media_upload']) {
    $objContent = new Content();
    if ($objContent->handleNewUpload()) {
        $message = "<label class='uploadsuccess'>File uploaded</label>";
    } else {
        $message = "<label class='uploadfail'>Upload Failed</label>";
    }
}
?>
