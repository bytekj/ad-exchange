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
require_once '../Classes/Publisher.php';
require_once '../Classes/Reports.php';



//$objAdSpot = new Adspot();

$objPublisher = new Publisher();
$objPublisher->getCurrentPublisherInfo();
?>
<div class="page">


    <?php
    if ($_SESSION['regmsg']) {
        echo "<h6>" . $_SESSION['regmsg'] . "</h6>";
    }
    if(file_exists(LOGO_URL . $objPublisher->brand_logo)){
        ?>
        <div class="logo">
            <img width="auto" height="100%" src="<?php echo LOGO_URL . $objPublisher->brand_logo; ?>"/>
        </div>
        <?php
    }
    ?>
    <div class="report_info report_border" style="margin-left:auto;margin-right:auto;">
        <?php

        $objReport = new Reports();
        $report = $objReport->ContentGetDashReport($objPublisher->id);
        echo "Total Channels live: ".$report['channel_count']."<br><br>";
        echo "Current Month's Inventory: ".number_format($report['total_inventory'])." Impressions<br><br>";
        echo "Current Month's Inventory Sold: ".number_format($report['sold_inventory'])." Impressions<br><br>";
        echo "Average CPM: $".round($report['avg_cpm'],2)."<br>";       

        ?>
    </div>
    <br><br>
    <b>Content</b>
    <?php
    //$content = $objPublisher->getContent();
    if(isset($_GET['o'])){
        $offset = $_GET['o'];    
    }
    else{
        $offset = 0;
    }
    if(isset($_GET['l'])){
        $limit = $_GET['l'];    
    }
    else{
        $limit = 10;
    }
    
    $objContent = new Content();
    $content = $objContent->getContentByPublisherIdWithLimits($objPublisher->userid, $offset, $limit);


//    echo "<pre>";
//    print_r($content);
//    echo "</pre>";
    if ($content) {
        ?>
        
        <table class="content">
            <tr>
                <th style="width: 200px;">Name</th>
                <th style="width: 100px;">Genre</th>
                <?php /*<th style="width: 50px;">Ad Frequency(minutes)</th> */?>
                <th style="width: 100px;">Region</th>
                <th style="width: 100px;">Language</th>
                <th style="width: 100px;">&nbsp;</th>

            </tr>
            <?php
            foreach ($content as $contentrow) {
                echo "<tr>";
                echo "<td><a href='showcontent.php?id=" . $contentrow['id'] . "'>" . $contentrow['name'] . "</a></td>";
                echo "<td>" . $contentrow['genre'] . "</td>";
                /* echo "<td>" . $contentrow['ad_freq'] . "</td>"; */
                $dots = "...";
                if(strlen($contentrow['region']) < 10){
                    $dots = "";
                }
                echo "<td><a title='".$contentrow['region']."'>" . substr($contentrow['region'],0, 10) .$dots. "</a></td>";
                echo "<td>" . $contentrow['language'] . "</td>";
                echo "<td><a href=\"reports.php?id=".$contentrow['id']."\">Report</a></td>";
                echo "</tr>";
            }
            ?>

        </table>
        
        <?php
        $nextoffset = 0;
        $prevoffset = 0;

        if(sizeof($content) < $limit)
            $nextoffset = $offset;
        else
            $nextoffset = $offset+$limit;
        if($offset == 0){
            $prevoffset = 0;
        }
        else{
            $prevoffset = $offset-$limit;
        }

        ?>
        <table>
            <tr>
                <td style="width: 600px;">&nbsp;</td>
                <td><a href="index.php?o=<?php echo $prevoffset; ?>&l=<?php echo $limit ?>">Prev</a></td>
                <td><a href="index.php?o=<?php echo $nextoffset; ?>&l=<?php echo $limit ?>">Next</a></td>
            </tr>
        </table>

        <?php
    } else {
        echo "<label>No content</label>";
    }
    ?>
    <?php /*
    <a href="newcontent.php"><label>Add new content</label></a>
     */?>
 </div>
