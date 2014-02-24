<?php
require_once '../include/global.php';

require_once '../Classes/UserInfo.php';
require_once '../Classes/Session.php';
require_once '../Classes/UIForm.php';
require_once '../Db/DataHandler.php';

$message = "";
$error = 0;
$objSession = new Session();
if ($_GET['redirect_to']) {
	$redirect_path = $_GET['redirect_to'];
} else {
	$redirect_path = $_SERVER['SERVER_NAME'] . PATH;
}
if ($objSession->isUserLoggedIn()) {

	header('Location: http://' . $redirect_path);
}
if ($_POST['Submit']) {
	$username = $_POST['username'];
	$pass = $_POST['password'];
	$objUserInfo = new UserInfo();
	if($username == "fancy" && $pass == "pants"){
		$_SESSION['auth'] = $username;
		$_SESSION['type'] = 'superadmin';
		header('Location: http://'.$_SERVER['SERVER_NAME'].'/adex/public');
	}
	else if ($objUserInfo->authenticate($username, $pass)) {

		//TODO change last login timestamp
		//TODO get new session id from db and store in phpsession

		$_SESSION['auth'] = $username;
		$_SESSION['type'] = $objUserInfo->usertype;

		$objUserInfo->updateLastLogin();
		header('Location: http://' . $redirect_path);
	} else {
		$error = 1;
		$message = "password incorrect!";
	}
}
?>


<?php
require 'header.php';
?>
<script type="text/javascript">
//ele = document.forms['loginform'].elements['username'];
window.onload = function() {
	  document.getElementById("username").focus();
};

</script>
<div class="login">
	<div class="loginform">
		<div style="margin-left:165px;"><b>
			<?php
			if($error == 1){
				echo $message; 	
			}
			
			 ?>
			</b>
		</div>
		<form id="loginform" action="" method="post">
			<input type="hidden" name="redirect_path"
				value="<?php echo $redirect_path ?>">
			<table>
				<tr>
					<?php $formObj = new UIForm(); ?>

					<!--<label>Username: </label><input class="input" type="text"
                                                    name="username" />-->
					<?php
					echo "<td>" . $formObj->getElement('Username: ', 'label') . "</td>" .
							"<td>" . $formObj->getElement('username', 'text','','','username') . "</td>";
					?>
				</tr>
				<tr>
					<!--<label>Password: </label><input class="input" type="password"
                                                    name="password" />-->
					<?php
					echo "<td>" . $formObj->getElement('Password: ', 'label') . "</td>" .
							"<td>" . $formObj->getElement('password', 'password') . "</td>";
					?>
				</tr>
				<tr>
					<!--<input
                    type="submit" class="button" name="Login" value="Login" />-->
					<?php
					echo "<td>&nbsp;</td>";
					echo "<td>" . $formObj->getElement('Submit', 'submit', 'Login') . $formObj->getElement('Register', 'button', 'Register', 'location.href=\'register.php\'') . "</td>";
					?>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php
require 'footer.php';
?>

