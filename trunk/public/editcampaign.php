<?php
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

$objUserInfo = new UserInfo();
$objUserInfo->getCurrentUserInfo();
$objCampaign = new Campaign();
if ($_GET['id']) {
    $showCampaignBool = $objCampaign->getAdvertiserCampaignById($objUserInfo->userid, $_GET['id']);
//    echo "<pre>";
//    print_r($objCampaign);
//    echo "</pre>";
//    echo "<pre>";
//    print_r(Utils::getAllGenres());
//    echo "</pre>";
    if ($showCampaignBool == false) {
        $message = "Unauthorized access";
    }
}

if ($_POST['submit']) {

   
    $objCampaign->end_date = $_POST['end_date'];
    $objCampaign->genre = $_POST['genre'];
    $objCampaign->region = $_POST['region'];
    $objCampaign->city = $_POST['city'];
    $objCampaign->platform = $_POST['platform'];
    $objCampaign->status = 'active';
    $objCampaign->ad_spots = $_POST['ad_spots'];

    if ($objCampaign->update()) {
        header('Location: showcampaign.php?id=' . $_GET['id']);
    }


    //$objAd = new Ad();
    //$objAd->handleNewUpload($objCampaign->id);
    //    echo "<pre>";
    //    print_r($_POST);
    //    echo "</pre>";
    //    echo "<pre>";
    //    print_r($_FILES);
    //    echo "</pre>";
}
?>
<script type="text/javascript">
function newPopup(url) {
    //popupWindow = window.open(url,'_blank','height=400,width=600,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no,status=yes');
    window.open(url,'_blank','location=no,directories=no,status=yes,width=320,height=240,scrollbars=yes');
}
</script>
<div class="page">
    <div class="campaignform">
        <h3>&nbsp;&nbsp;&nbsp;Change campaign parameters</h3>
        <form method="POST" action="">
            <?php
            $formObj = new UIForm();
            ?>
            <table>
                <tr>
                    <td valign="top"><label><?php echo $objCampaign->name ?>
                        </label>
                    </td>
                    <td>
                        <a
                href="JavaScript:newPopup('viewad.php?id=<?php echo $objCampaign->id; ?>')">View
                    Ad</a>
            
                    </td>
                </tr>

            </table>
            <hr>
            <h4>&nbsp;&nbsp;&nbsp;&nbsp;Campaign dates</h4>
            <table>
                <tr>
                    <td>
                        <div class="field">
                            <?php
                            echo $formObj->getElement('Start date: ', 'label');
                            echo $formObj->getElement($objCampaign->start_date, 'label');
                            ?>
                                <!--<input id="datepicker" type="text">-->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="field">
                            <script>
                                $(function() {
                                    $( "#end_datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
                                });
                            </script>
                            <?php
                            echo $formObj->getElement('End date: ', 'label');
                            echo $formObj->getElement('end_date', 'text', $objCampaign->end_date, '', 'end_datepicker');
                            ?>

                        </div>
                    </td>
                </tr>
            </table>
            <hr>
            <h4>&nbsp;&nbsp;&nbsp;&nbsp;Campaign parameters</h4>
            <table>
                <tr>
                    <td>
                        <div class="field">
                            <?php
                            echo $formObj->getElement('Genre: ', 'label');
                            ?>
                            <script>
                                function clearAll(select){
                                    for(k=1;k<select.options.length;k++)
                                    {
                                        select.options[k].selected = false;
                                    }
                                }
                                function clearfirst(select){
                                    select.options[0].selected = false;                                    
                                }
                            </script>
                            <?php
                            $arrGenre = Utils::getAllGenres();
                            ?>
                            <select style="width: 200px;" multiple="multiple" name="genre[]">
                                <?php
                                foreach ($arrGenre as $genre) {
                                    if ($objCampaign->checkCampaignParam('genre', $genre['genre']))
                                        echo "<option value='" . $genre['genre'] . "'  selected='selected'>" . $genre['genre_name'] . "</option>";
                                    else
                                        echo "<option value='" . $genre['genre'] . "' >" . $genre['genre_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="field">
                            <?php
                            echo $formObj->getElement('Region: ', 'label');
                            $arrRegion = Utils::getAllRegions();
                            ?>
                            <select style="width: 200px;" multiple="multiple" name="region[]">

                                <?php
                                foreach ($arrRegion as $region) {
                                    if ($objCampaign->checkCampaignParam('region', $region['region'])) {
                                        echo "<option value='" . $region['region'] . "' selected = 'selected' >" . $region['region_name'] . "</option>";
                                    } else {
                                        echo "<option value='" . $region['region'] . "' >" . $region['region_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <!--
                <tr>
                    <td>
                        <div class="field">

                            <?php
                            echo $formObj->getElement('City', 'label');
                            $arrCity = Utils::getAllCityTiers();
                            ?>
                            <select style="width: 200px;" multiple="multiple" name="city[]">

                                <?php
                                foreach ($arrCity as $city) {
                                    if ($objCampaign->checkCampaignParam('city', $city['tier'])) {
                                        echo "<option value='" . $city['tier'] . "' selected='selected'>" . $city['tier_name'] . "</option>";
                                    } else {
                                        echo "<option value='" . $city['tier'] . "' >" . $city['tier_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>

                        </div>
                    </td>
                </tr>
                -->
                <tr>
                    <td>
                        <div class="field">
                            <?php
                            echo $formObj->getElement('Platform: ', 'label');
                            $arrPlatform = Utils::getAllPlatforms();
                            ?>
                            <select style="width: 200px;" multiple="multiple"
                                    name="platform[]">

                                <?php
                                foreach ($arrPlatform as $platform) {
                                    if ($objCampaign->checkCampaignParam('platform', $platform['platform'])) {
                                        echo "<option value='" . $platform['platform'] . "' selected='selected'>" . $platform['platform_name'] . "</option>";
                                    } else {
                                        echo "<option value='" . $platform['platform'] . "' >" . $platform['platform_name'] . "</option>";
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="field">
                            <?php
                            echo $formObj->getElement('CPM', 'label');
                            echo $formObj->getElement("$" . $objCampaign->cpm, 'label');
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="field">
                            <?php
                            echo $formObj->getElement('Ad spots', 'label');
                            echo $formObj->getElement('ad_spots', 'text', $objCampaign->ad_spots);
                            ?>
                        </div>
                    </td>
                </tr>
            </table>
            <hr>

            <table style="margin-left: auto;">

                <tr>
                    <td>&nbsp;</td>
                    <td><?php
                            echo "<br><br>";
                            echo $formObj->getElement('submit', 'submit', 'Submit');
                            ?>

                    <td>


                <tr>

            </table>
        </form>
    </div>
</div>
<?php require 'footer.php'; ?>