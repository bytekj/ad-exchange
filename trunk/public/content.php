<?php
require 'header.php';

require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Content.php';
require_once '../Db/DataHandler.php';
require_once '../include/global.php';
require_once '../Classes/UIForm.php';

require_once '../Classes/Publisher.php';
$objSession = new Session();

$objPublisher = NULL;
//$objUserInfo = NULL;
if ($objSession->isUserLoggedIn()) {

    $objPublisher = new Publisher();
    $objPublisher->getCurrentPublisherInfo();
} else {

    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    header('Location: login.php?redirect_to=' . $pageURL);
}
?>
<script type="text/javascript">
    document.getElementById('content').visibility=false;
</script>
<div class="page">
    <b>Content</b>
    <?php
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

        <table id="content" class="content">
            <tr>
                <th style="width: 200px;">Name</th>
                <th style="width: 100px;">Genre</th>
                <?php /*<th style="width: 50px;">&nbsp;Ad Frequency (minutes)</th> */?>
                <th style="width: 100px;">Region</th>
                <th style="width: 100px;">Language</th>
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
                /*echo "<td>" . $contentrow['region'] . "</td>"; */
                echo "<td>".$contentrow['language']."</td>";
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
                <td><a href="content.php?o=<?php echo $prevoffset; ?>&l=<?php echo $limit ?>">Prev</a></td>
                <td><a href="content.php?o=<?php echo $nextoffset; ?>&l=<?php echo $limit ?>">Next</a></td>
            </tr>
        </table>

        <?php
    } else {
        echo "<label>No content!</label>";
    }
    ?>
    <br><a href="newcontent.php"><b>Add New Content</b></a><br><br>
    <a href="allstreams.php?o=0&l=9"><b>View All Streams</b></a>
</div>
<?php
require 'footer.php';
?>