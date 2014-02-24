<!DOCTYPE html>
<html>
    <title>Ad$parx</title>
    <head>
        <link rel="stylesheet" href="../CSS/main.css" type="text/css" />
        <link rel="stylesheet" href="../CSS/smoothness/jquery-ui-1.9.0.custom.min.css" type="text/css" />
        <script src="../js/jquery-1.7.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.0.custom.min.js"></script>
    </head>
    <body>
        <?php
        require_once '../Classes/UserInfo.php';
        require_once '../Classes/Session.php';
        require_once '../Classes/Content.php';
        require_once '../Db/DataHandler.php';
        require_once '../include/global.php';
        require_once '../Classes/UIForm.php';
        require_once '../Classes/Advertiser.php';
        require_once '../Classes/Campaign.php';

        $objSession = new Session();
        $objUserInfo = NULL;
        //$objUserInfo = NULL;
        if ($objSession->isUserLoggedIn()) {
            //TODO show add upload form
            $objUserInfo = new UserInfo();
            $objUserInfo->getCurrentUserInfo();
        } else {
            if ($_SERVER['SCRIPT_NAME'] != '/adex/public/login.php' && $_SERVER['SCRIPT_NAME'] != '/adex/public/register.php') {
                header("Location: /adex/public/login.php?redirect_to=" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
            }
        }
        ?>

        <div class="base">
            <div class="header">
                <table style="width: 100%">
                    <tr>
                        <td rowspan="2"><a href="<?php echo PATH ?>"><img
                                    src="../CSS/images/adsparx_logo.png" height="75px" width="240px">
                            </a></td>

                        <td align="right">
                            
                            <?php
                            if ($objUserInfo!=NULL) {
                                echo "Welcome";
                            }
                            ?>
                            <a href="account.php"> 
                            <?php
                            if ($objUserInfo!=NULL) {
                                $usertype = "";
                                if ($objUserInfo->usertype == 'advertiser') {
                                    $usertype = "Advertiser";
                                } else if ($objUserInfo->usertype == 'publisher') {
                                    $usertype = "Publisher";
                                }
                                echo $objUserInfo->firstname . ", " . $usertype;
                            }
                            ?>
                                </a>                            
                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            if ($objUserInfo) {
                                if ($objUserInfo->usertype == 'publisher') {
                                    $objContent = new Content();
                                    $contentId = $objContent->GetLastAddedContent($objUserInfo->userid);
                                    ?>

                                    <table class="menu">
                                        <tr class="menuicon">

                                            <td id="home_icon">
                                                <a href="<?php echo PATH ?>"><img class="icon" src="../CSS/icon/home.png" alt="Home">Home </a>
                                            </td>
                                            <td id="content_icon"><a href="<?php echo "content.php" ?>"><img
                                                        class="icon" src="../CSS/icon/movie.png" alt="Content">Content </a></td>

                                            <td id="chart_icon"><a href="<?php echo "reports.php?id=" . $contentId ?>"><img
                                                        class="icon" src="../CSS/icon/chart.png" alt="Statistics">Reports </a></td>
                                            <td id="account_icon"><a href="<?php echo "account.php" ?>"><img
                                                        class="icon" src="../CSS/icon/user.png" alt="Account Info">Account </a>
                                            </td>
                                            <td id="logout_icon"><a
                                                    href="logout.php?redirect_to=<?php echo $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]; ?>"><img
                                                        class="icon" src="../CSS/icon/logoff.png">Logout </a></td>
                                        </tr>
                                        
                                    </table>


                                    <?php
                                } else if ($objUserInfo->usertype == 'advertiser') {

                                    $objCampaign = new Campaign();

                                    $campaignId = $objCampaign->getLastAddedCampaign($objUserInfo->userid);
                                    ?>



                                    <table class="menu">
                                        <tr class="menuicon">

                                            <td id="home_icon"><a href="<?php echo PATH ?>">
                                                    <img class="icon" src="../CSS/icon/home.png" alt="Home"><br>Home </a></td>
                                            <td id="campaign_icon"><a href="<?php echo "campaign.php" ?>"><img
                                                        class="icon" src="../CSS/icon/campaign.png" alt="Home">Campaigns </a></td>

                                            <td id="chart_icon"><a href="<?php echo "reports.php?id=" . $campaignId; ?>"><img
                                                        class="icon" src="../CSS/icon/chart.png" alt="Statistics">Reports </a></td>
                                            <td id="account_icon"><a href="<?php echo "account.php" ?>"><img
                                                        class="icon" src="../CSS/icon/user.png" alt="Account Info"> Account</a>
                                            </td>
                                            <td id="logout_icon"><a
                                                    href="logout.php?redirect_to=<?php echo $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]; ?>"><img
                                                        alt="Logout" class="icon" src="../CSS/icon/logoff.png">Logout </a></td>
                                        </tr>

                                    </table>

                                    <?php
                                } else if ($objUserInfo->usertype == 'admin') {
                                    ?>

                                    <table class="menu">
                                        <tr class="menuicon">
                                            <td id="logout_icon"><a
                                                    href="logout.php?redirect_to=<?php echo $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]; ?>"><img
                                                        class="icon" src="../CSS/icon/logoff.png"> </a></td>
                                        </tr>
                                        <tr class="menuicon">
                                            <td>Logout</td>
                                        </tr>
                                    </table>

                                    <?php
                                }
                            }
                            ?>
                        </td>

                    </tr>
                </table>

            </div>
