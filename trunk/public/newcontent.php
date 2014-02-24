<?php
require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Content.php';
require_once '../Classes/UIForm.php';
require_once '../Db/DataHandler.php';
require_once '../Classes/Utils.php';

require_once '../include/global.php';
require 'header.php';

$objUserInfo = new UserInfo();

$objUserInfo->getCurrentUserInfo();

/*
Array
(
    [name] => mtv
    [genre] => Array
        (
            [0] => 1
        )

    [region] => Array
        (
            [0] => 1
            [1] => 3
            [2] => 4
        )

    [ad_freq] => 10
    [submit] => Submit
)

*/

if($_POST['submit']){


    $objContent = new Content();

    $objContent->name = $_POST['name'];
    $objContent->genre =$_POST['genre'][0];
    $objContent->language = $_POST['language'];
    //$objContent->ad_freq = $_POST['ad_freq'];
    $objContent->ad_freq = 1000;
    //correct this later
    $objContent->region = implode(",", $_POST['region']);
    $objContent->publisher_id = $objUserInfo->id;


    if($objContent->add()){
        header("Location: content.php");
    }

}

?>
<div class="page">
    <div class="campaignform">
        <h3>&nbsp;&nbsp;&nbsp;Add content details</h3>
        <form method="POST" action="">
            <?php $formObj = new UIForm(); ?>
            <table style="width: 100%;">
                <tr>
                    <td>
                        <?php
                        echo $formObj->getElement('Name: ', 'label');
                        echo $formObj->getElement('name', 'text');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo $formObj->getElement('Genre', 'label');
                        $genres = Utils::getAllGenres();
                        //_debug($genres);
                        echo "<select style='width: 200px;' name='genre[]'>";
                        foreach ($genres as $genre) {
                            echo "<option value='" . $genre['id'] . "'>" . $genre['genre_name'] . "</option>";
                        }
                        echo "</select>";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        
                        echo $formObj->getElement('Region', 'label');
                        $regions = Utils::getAllRegions();
//                    echo "<pre>";
//                    print_r($regions);
//                    echo "</pre>";
                        echo "<select style='width: 200px;' multiple='multiple'  name='region[]'>";

                        foreach ($regions as $region) {
                            echo "<option value='" . $region['id'] . "'>" . $region['region_name'] . "</option>";
                        }
                        echo "</select>";

                        ?>
                    </td>
                </tr>
                <?php
                        /*
                <tr>
                    <td>
                        
                        echo $formObj->getElement('Ad frequency', 'label');
                        echo $formObj->getElement('ad_freq', 'text');
                        
                    </td>
                </tr>
                */
                ?>
                <tr>
                    <td>
                        <?php
                        
                        echo $formObj->getElement('Language', 'label');
                        $languages = Utils::getAllLanguages();

                        echo "<select style='width: 200px;'  name='language'>";

                        foreach ($languages as $language) {
                            echo "<option value='" . $language['id'] . "'>" . $language['lang_name'] . "</option>";
                        }
                        echo "</select>";

                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        &nbsp;<br>&nbsp;<br>&nbsp;<br>
                        <div style="float: right;">
                            <?php
                            echo $formObj->getElement('submit', 'submit', 'Submit');
                            ?>
                        </div>
                    </td>
                </tr>

            </table>
        </form>
    </div>
</div>

<?php
require 'footer.php'
?>

