<?php
require_once '../Classes/UploadHandler.php';
require_once '../include/global.php';
$allowedExtensions = array("3gp", "jpeg");

$sizeLimit = 10 * 1024 * 1024;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

$result = $uploader->handleUpload(ORIG_AD_FILES);

echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

?>
