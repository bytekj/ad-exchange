<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'header.php';
require_once '../Classes/UIForm.php';
require_once '../Classes/Advertiser.php';
require_once '../include/global.php';

if ($_POST['submit']) {
    $objAdvertiser = new Advertiser();
    $objAdvertiser->getCurrentAdvertiserInfo();
    $objAdvertiser->walletTotal += $_POST['amount'];
    $objAdvertiser->walletBalance += $_POST['amount'];
    $objAdvertiser->updateBalance();
}
?>
<div class="page">
    <form action="" method="POST">
        <?php
        $objUIForm = new UIForm();
        ?>
        <table>

            <tr>
                <td>
                    <div class="field">
                        <?php
                        echo $objUIForm->getElement('Enter amount ', 'label');
                        echo $objUIForm->getElement('amount', 'text');
                        ?>
                    </div>
                    <p>
                        <?php
                        echo $objUIForm->getElement('submit', 'submit', 'Submit');
                        ?>
                    </p>    
                </td>
            </tr>

        </table>
    </form>
</div>

<?php
require_once 'footer.php';
?>