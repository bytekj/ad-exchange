
<?php
/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/
require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Content.php';
require_once '../Classes/UIForm.php';
require_once '../Classes/Adspot.php';
require_once '../Classes/Campaign.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/Ad.php';
require_once '../Classes/Utils.php';

require_once '../include/global.php';
require 'header.php';

if ($objSession->isUserLoggedIn()) {
	//TODO show add upload form
	$objUserInfo = new UserInfo();
	$objUserInfo->getCurrentUserInfo();
}


$objCampaign = new Campaign();

$objCampaign = $_SESSION['objcampaign'];

echo "<pre>";
print_r($objCampaign);
echo "</pre>";

echo "<br>" . $objCampaign->advertiser_id;
?>
<link href="../CSS/fileuploader.css" rel="stylesheet" type="text/css">	
<h6>Upload your files here!</h6>

<div id="file-uploader">
	<noscript>
		<p>Please enable JavaScript to use file uploader.</p>
		<!-- or put a simple form for upload here -->
	</noscript>
</div>
<script
	src="../js/fileuploader.js" type="text/javascript"></script>
<script>
            
    var uploader = new qq.FileUploader({
        // pass the dom node (ex. $(selector)[0] for jQuery users)
        element: document.getElementById('file-uploader'),
        // path to server-side upload script
        action: 'uploadxhr.php',
        allowedExtensions: ['3gp', 'mp4'],
        debug:true
    });
    window.onload = createUploader;  
</script>

<?php 
require 'footer.php';
?>