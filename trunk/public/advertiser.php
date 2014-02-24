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
new Session();

$objAdSpot = new Adspot();



$objUserInfo = new UserInfo();
$objUserInfo->getCurrentUserInfo();

if ($_POST['submit']) {
    $objCampaign = new Campaign();
    $objCampaign->name = $_POST['name'];
    $objCampaign->description = $_POST['description'];
    $objCampaign->advertiser_id = $objUserInfo->userid;
    $objCampaign->start_date = $_POST['start_date'];
    $objCampaign->end_date = $_POST['end_date'];
    $objCampaign->ad_content_id = $_POST['ad_content_id'];
    $objCampaign->genre = $_POST['genre'];
    $objCampaign->country = $_POST['country'];
    $objCampaign->state = $_POST['state'];
    $objCampaign->city = $_POST['city'];
    $objCampaign->platform = $_POST['platform'];
    $objCampaign->status = 'active';

    $objCampaign->add();
}
?>
<div class="page">
    <h6>
        Current ad-spots: <?php echo $objAdSpot->getCurrentAdspots(); ?> mille | 
        Available ad-spots: <?php echo $objAdSpot->getAvailableAdspots(); ?> mille |
        Current bid rate: $<?php echo $objAdSpot->getCurrentBidRate() ?>/mille 
    </h6>

    <h3>Current campaigns</h3>
    <table class="campaign">
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Start date</th>
            <th>End date</th>
            <th>ad_id</th>
            <th>Genre</th>
            <th>Country </th>
            <th>State</th>
            <th>City</th>
            <th>Platform    </th>
            
        </tr>
        <?php
        $objCampaign = new Campaign();
        $currentCampaigns = $objCampaign->getByAdvertiser($objUserInfo->userid);
        
        if ($currentCampaigns) {
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
                echo "<td>" . $campaign['name'] . "</td>";
                echo "<td>" . $campaign['description'] . "</td>";
                
                echo "<td>" . $campaign['status'] . "</td>";
                echo "<td>" . $campaign['start_date'] . "</td>";
                echo "<td>" . $campaign['end_date'] . "</td>";
                echo "<td>" . $campaign['ad_content_id'] . "</td>";
                echo "<td>" . $campaign['genre'] . "</td>";
                echo "<td>" . $campaign['country'] . "</td>";
                echo "<td>" . $campaign['state'] . "</td>";
                echo "<td>" . $campaign['city'] . "</td>";
                echo "<td>" . $campaign['platform'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<h5>No campaigns found!</h5>";
        }
        ?>
    </table>
</div>

