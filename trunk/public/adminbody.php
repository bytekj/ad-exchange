<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<?php
require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Content.php';
require_once '../Db/DataHandler.php';
$objSession = new Session();

if ($objSession->isUserLoggedIn()) {
    //TODO show add upload form
    $objUserInfo = new UserInfo();
    $objUserInfo->getCurrentUserInfo();
}
if ($objUserInfo->usertype == 'superadmin') {
    $arrProfiles = Content::getContentProfiles();
//    echo "<pre>";
//    print_r($arrProfiles);
//    echo "</pre>";
    if ($_POST['id']) {
        $id = $_POST['id'];
        $prof = Content::getContentProfilesById($id);
        $file = fopen(TRANSCODE_CONF.$prof[0]['config'],'w') or die('Cannot open file for writing');
        
        $data = json_encode($prof[0]);
        fwrite($file, $data);
        fclose($file);
    }
    ?>

    <h3>Admin</h3>
    <!-------- add user form ------------->
    <label>Profiles</label>

    <table class="admin_content">
        <tr>
            <th> id </th>
            <th> video resolution </th>
            <th> video bit rate </th>
            <th> video codec </th>
            <th> video fps </th>
            <th> audio bit rate </th>
            <th> audio sampling rate </th>
            <th> audio channels </th>
            <th> audio codec </th>
            <th> config </th>
            <th> stream type </th>
            <th> pixel aspect ratio </th>
            <th> key int max </th>
            <th> cabac flag </th>
            <th> audio codec profile </th>
            <th>&nbsp;</th>

        </tr>
    <?php
    foreach ($arrProfiles as $profile) {
        echo "<tr>";
        echo "<td>" . $profile['id'] . "</td>";
        echo "<td>" . $profile['video_resolution'] . "</td>";
        echo "<td>" . $profile['video_bit_rate'] . "</td>";
        echo "<td>" . $profile['video_codec'] . "</td>";
        echo "<td>" . $profile['video_fps'] . "</td>";
        echo "<td>" . $profile['audio_bit_rate'] . "</td>";
        echo "<td>" . $profile['audio_sampling_rate'] . "</td>";
        echo "<td>" . $profile['audio_channels'] . "</td>";
        echo "<td>" . $profile['audio_codec'] . "</td>";
        echo "<td>" . $profile['config'] . "</td>";
        echo "<td>" . $profile['stream_type'] . "</td>";
        echo "<td>" . $profile['pixel_aspect_ratio'] . "</td>";
        echo "<td>" . $profile['key_int_max'] . "</td>";
        echo "<td>" . $profile['cabac_flag'] . "</td>";
        echo "<td>" . $profile['audio_codec_profile'] . "</td>";
        echo "<td>";
        echo "<form action='' method='POST'><input type='hidden' name='id' value='" . $profile['id'] . "'>
                        <input type='submit' class='admin_button' value='conf'></form>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
    </table>    
        <?php
    }
    ?>

    