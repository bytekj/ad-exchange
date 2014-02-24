<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/Content.php';
require_once '../Classes/UIForm.php';
require_once '../Db/DataHandler.php';
require_once '../include/global.php';

require 'header.php';

$message = '';
//    echo "<pre>";
//    print_r($_POST);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($_FILES);
//    echo "</pre>";
$objUserInfo = new UserInfo();


$objUserInfo->getCurrentUserInfo();

if ($_POST['submit']) {

    $objUserInfo->firstname = $_POST['firstname'];
    $objUserInfo->lastname = $_POST['lastname'];
    $objUserInfo->email = $_POST['email'];
    $objUserInfo->company_address = $_POST['company_address'];
    $objUserInfo->brand_name = $_POST['brand_name'];


    if ($objUserInfo->firstname == '') {
        $message .= "'Name' can't be empty!<br>";
    } else if ($objUserInfo->email == '') {
        $message .="'Email' can't be empty!<br>";
    } else if ($objUserInfo->lastname == '') {
        $message .="'Lastname' can't be empty!<br>";
    } else if ($objUserInfo->company_address == '') {
        $message .="'Company address' can't be empty!<br>";
    } else {
        $result = $objUserInfo->editUser();

        if ($result == true)
            header('Location: account.php');
        else
            $message .= "Error in updating account";
    }
}
?>



<div class="page">
    <div class="register">
        <div class="registerform">
            <h6 style="color: red;">
                <?php echo $message; ?>
            </h6>
            <form action="" method="POST" enctype="multipart/form-data">
                <table>

                    <?php
                    $formObj = new UIForm();
                    ?>
                    <tr>

                        <?php
                        echo "<td>" . $formObj->getElement('Role', 'label') . "</td>";
                        ?>
                        <td><?php echo $objUserInfo->usertype ?>
                        </td>


                    </tr>

                    <tr>

                        <?php
                        echo "<td>" . $formObj->getElement('Name', 'label') . "</td>";
                        echo "<td><input name='firstname' type='text' value='" . $objUserInfo->firstname . "'></td>";
                        ?>

                    </tr>
                    <tr>

                        <?php
                        echo "<td>" . $formObj->getElement('Last name', 'label') . "</td>";
                        echo "<td><input name='lastname' type='text' value='" . $objUserInfo->lastname . "'></td>";
                        ?>

                    </tr>
                    <tr>

                        <?php
                        echo "<td>" . $formObj->getElement('Email', 'label') . "</td>";
                        echo "<td><input name='email' type='text' value='" . $objUserInfo->email . "'></td>";
                        ?>

                    </tr>
                    <tr>

                        <?php
                        echo "<td>" . $formObj->getElement('Userid', 'label') . "</td>";
                        echo "<td><input type='text' readonly='readoonly' value='" . $objUserInfo->userid . "'></td>";
                        ?>

                    </tr>
                    <tr>

                        <?php
                        echo "<td>" . $formObj->getElement('Brand name', 'label') . "</td>";
                        echo "<td><input name='brand_name' type='text' value='" . $objUserInfo->brand_name . "'></td>";
                        ?>

                    </tr>

                    <tr>
                        <td>

                            <?php
                            echo $formObj->getElement('Brand logo', 'label');
                            ?>


                        </td>
                        <td><?php
                            echo $formObj->getElement('logo', 'file');
                            ?>
                        </td>
                        <td>

                            <div class="logo">
                                <img width="auto" height="100%"
                                     src="<?php echo LOGO_URL . $objUserInfo->brand_logo ?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>

                        <?php
                        echo "<td>" . $formObj->getElement('Company name', 'label') . "</td>";
                        echo "<td><input name='company_name' type='text' readonly='readoonly' value='" . $objUserInfo->company_name . "'></td>";
                        ?>

                    </tr>
                    <tr>

                        <?php
                        echo "<td>" . $formObj->getElement('Company address', 'label') . "</td>";
                        echo "<td><textarea name='company_address'>" . $objUserInfo->company_address . "</textarea></td>";
                        ?>

                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>

                            <?php
                            echo "<br><br><br><br>";
                            echo $formObj->getElement('submit', 'submit', 'Submit');
                            ?>

                        </td>
                    </tr>

                </table>
            </form>
        </div>
    </div>
</div>
<?php
require 'footer.php';
?>
