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
require_once '../Classes/Advertiser.php';
require_once '../Db/DataHandler.php';
require_once '../include/global.php';
new Session();

$objAdSpot = new Adspot();

$objAdvertiser = new Advertiser();
$objAdvertiser->getCurrentAdvertiserInfo();

$objData = new DataHandler();
?>
<div class="page">

    <!--<h6>
        Current ad-spots: <?php echo $objAdSpot->getCurrentAdspots(); ?> mille | 
        Available ad-spots: <?php echo $objAdSpot->getAvailableAdspots(); ?> mille |
        Current bid rate: $<?php echo $objAdSpot->getCurrentBidRate() ?>/mille 
    </h6>-->
    <!--<table class="campaign">
        <tr>
            <td>Current Wallet</td><td><?php echo $objAdvertiser->walletTotal ?></td>
        </tr>
        <tr>
            <td>Wallet balance</td><td><?php echo $objAdvertiser->walletBalance ?></td>
        </tr>

    </table>-->
    <?php
    if ($_SESSION['regmsg']) {
        echo "<h6>" . $_SESSION['regmsg'] . "</h6>";
    }
    ?>
    <?php
    if(file_exists(LOGO_URL . $objAdvertiser->brand_logo)){
       ?>
       <div class="logo">
        <img width="auto" height="100%" src="<?php echo LOGO_URL . $objAdvertiser->brand_logo ?>"/>
    </div>
    <?php
}
?>
<h5>Current campaigns</h5>

<?php
$objCampaign = new Campaign();
$currentCampaigns = $objCampaign->getActiveCampaignsByAdvertiserIdAndStatus($objAdvertiser->userid);

if ($currentCampaigns) {
    ?>
    <table class="campaign">
        <tr>
            <th style="width:300px;">Name</th>
            <th style="width:120px;">Start Date</th>
            <th style="width:120px;">End date</th>
            <th style="width: 20px;">CPM($)</th>
            <th style="width: 80px;">Requested Impressions</th>



        </tr>
        <?php
        foreach ($currentCampaigns as $campaign) {
                /*
                  `id`,
                  `advertiser_id`,
                  `status`,
                  `start_date`,
                  `end_date`,
                  `ad_content_id`,
                  `genre`,
                  `country`,
                  `state`,
                  `city`,
                  `platform`
                 */
                  echo "<tr>";
                  echo "<td><a href='showcampaign.php?id=" . $campaign['id'] . "'>" . $campaign['name'] . "</a></td>";
                  echo "<td>" . $campaign['start_date'] . "</td>";

                  echo "<td>" . $campaign['end_date'] . "</td>";
                  echo "<td>" . $campaign['cpm'] . "</td>";
                  echo "<td>" . number_format($campaign['ad_spots']) . "</td>";
                  echo "</tr>";
              }
              ?>
          </table>
          <?php
      } else {
        echo "<h6>No campaigns found!</h6>";
    }
    ?>
    <h6><a href="newcampaign.php">Create new campaign</a></h6>

</div>

