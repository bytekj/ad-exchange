<?php
require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Campaign.php';
require_once '../Db/DataHandler.php';
require_once '../include/global.php';

$session = new Session();

$campaign_id = $_GET['id'];
$objCampaign = new Campaign();
?>


<div id="adplayer" style="margin-left: auto; margin-right: auto;"></div>
<?php
$filename = $objCampaign->getLastExcodedAd($campaign_id);
?>
<script src="../js/jwplayer.js"></script>

<script type="text/javascript">
                        jwplayer("adplayer").setup({
                            flashplayer: "../js/player.swf",
                            type: "video",
                            width: "300",
                            height: "220",
                            controlbar: "none",
                            file: "<?php echo "../resource/ads/encoded/" . $filename; ?>"
                        });
</script>
