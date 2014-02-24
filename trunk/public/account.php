<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require 'header.php';
$objSession = new Session();

if ($objSession->isUserLoggedIn()) {
    //TODO show add upload form
    $objUserInfo = new UserInfo();
    $objUserInfo->getCurrentUserInfo();
} else {
    //TODO show login form
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    header('Location: login.php?redirect_to=' . $pageURL);
}
?>
<div class="page">

    <table style="width: 800px;">
        <tr>

            <td>
                <table>
                    <tr>

                        <!--<div class="logo">
<img width="auto" height="100%" src="<?php echo LOGO_URL . $objUserInfo->brand_logo ?>"/>
</div>-->
                        <td><label> Account information </label>
                        </td>
                    </tr>

                </table>
            </td>
            <td style="width: 350px;">&nbsp;&nbsp;&nbsp;</td>
            <td><?php echo "Date of registration: " . $objUserInfo->registration_date; ?>
            </td>
        </tr>
    </table>
    <hr>
    <table class="account">
        <tr>
            
            <td><b>Username:</b></td>
            <td><?php echo $objUserInfo->userid; ?></td>
        </tr>
        <tr>
            
            <td><b>Name: </b></td>
            <td><?php
echo $objUserInfo->firstname . " " . $objUserInfo->lastname;
?>
            </td>
        </tr>
        <tr>
            
            <td><b>Email:</b></td>
            <td><?php echo $objUserInfo->email ?></td>
        </tr>

        <tr>
            
            <td><b>Company name:</b></td>
            <td><?php echo $objUserInfo->company_name; ?></td>
        </tr>
        <!--<tr>
<td style="width: 100px">&nbsp;&nbsp;&nbsp;</td>
<td><b>Brand name:</b></td>
<td><?php echo $objUserInfo->brand_name; ?></td>
</tr>-->
        <tr>
            
            <td><b>Company address:</b></td>
            <td><?php echo $objUserInfo->company_address; ?></td>
        </tr>
    </table>
    <table style="width: 800px">
        <tr>
            <td style="width: 450px;">&nbsp;</td>
            <td>
                <?php
                $objForm = new UIForm();
                echo $objForm->getElement('Edit', 'button', 'Edit', 'location.href=\'editaccount.php\'')
                ?>
            </td>
        </tr>
    </table>
    
</div>


<?php
require 'footer.php';
?>