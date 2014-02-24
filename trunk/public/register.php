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
$message = '';
//    echo "<pre>";
//    print_r($_POST);
//    echo "</pre>";
//    echo "<pre>";
//    print_r($_FILES);
//    echo "</pre>";

if ($_POST['submit']) {


	$objUserInfo = new UserInfo();
	$objUserInfo->firstname = $_POST['name'];
	$objUserInfo->userid = $_POST['username'];
	$objUserInfo->password = $_POST['password'];
	$objUserInfo->usertype = $_POST['usertype'];
	$objUserInfo->brand_name = $_POST['brand_name'];
	$objUserInfo->lastname = $_POST['lastname'];
	$objUserInfo->email = $_POST['email'];
	$objUserInfo->company_name = $_POST['company_name'];
	$objUserInfo->company_address = $_POST['company_address'];


	if ($objUserInfo->firstname == '') {
		$message .= "'Name' can't be empty!<br>";
	} else if ($objUserInfo->userid == '') {
		$message .="'Username' can't be empty!<br>";
	} else if ($objUserInfo->password == '') {
		$message .="'Password' can't be empty!<br>";
	} else if ($objUserInfo->password != $_POST['repassword']) {
		$message .="Passwords are not matching";
	} else if ($objUserInfo->email == '') {
		$message .="'Email' can't be empty!<br>";
	} else if ($objUserInfo->lastname == '') {
		$message .="'Lastname' can't be empty!<br>";
	} else if ($objUserInfo->brand_name == '') {
		$message .="'Brand name' can't be empty!<br>";
	} else if ($objUserInfo->company_name == '') {
		$message .="'Company name' can't be empty!<br>";
	} else if ($objUserInfo->company_address == '') {
		$message .="'Company address' can't be empty!<br>";
	} else {

		$result = $objUserInfo->addUser();

		if ($result == false) {
			$message .= "Error in registration";
		} else {
			if($result['error']){
				$message .= "Userid already exists";
			}
			else{
				new Session();


				$_SESSION['auth'] = $objUserInfo->userid;
				$_SESSION['utype'] = $objUserInfo->usertype;
				$_SESSION['regmsg'] = "Thank you for registering on adsparx!";


				header('Location: ' . PATH);
			}
		}
	}
}

require 'header.php';
?>


<html>
<title>ADEX</title>
<head>
	<link rel="stylesheet" href="../CSS/main.css" type="text/css" />
</head>
<body>
	<div class="register">
		<div class="registerform">
			<h6 style="color: red;">
				<?php echo $message; ?>
			</h6>
			<form action="" method="POST" enctype="multipart/form-data">
				<table class="registertable">

					<?php
					$formObj = new UIForm();
					?>
					<tr>
						
						<?php
						echo "<td>" . $formObj->getElement('Role', 'label') . "</td>";
						?>
						<td><select name="usertype">
							<option value="advertiser">Advertiser</option>
							<option value="publisher">Publisher</option> 
						</select>
					</td>
				</tr>

				<tr>

					<?php
					echo "<td>" . $formObj->getElement('Name', 'label') . "</td>";
					echo "<td>" . $formObj->getElement('name', 'text') . "</td>    ";
					?>

				</tr>
				<tr>

					<?php
					echo "<td>" . $formObj->getElement('Last name', 'label') . "</td>";
					echo "<td>" . $formObj->getElement('lastname', 'text') . "</td>    ";
					?>

				</tr>
				<tr>

					<?php
					echo "<td>" . $formObj->getElement('Email', 'label') . "</td>";
					echo "<td>" . $formObj->getElement('email', 'text') . "</td>    ";
					?>

				</tr>
				<tr>

					<?php
					echo "<td>" . $formObj->getElement('Userid', 'label') . "</td>";
					echo "<td>" . $formObj->getElement('username', 'text') . "</td>";
					?>

				</tr>
				<tr>

					<?php
					echo "<td>" . $formObj->getElement('Password', 'label') . "</td>";
					echo "<td>" . $formObj->getElement('password', 'password') . "</td>";
					?>

				</tr>
				<tr>

					<?php
					echo "<td>" . $formObj->getElement('Retype password', 'label') . "</td>";
					echo "<td>" . $formObj->getElement('repassword', 'password') . "</td>";
					?>

				</tr>
				<tr>

					<?php
					echo "<td>" . $formObj->getElement('Brand name', 'label') . "</td>";
					echo "<td>" . $formObj->getElement('brand_name', 'text') . "</td>    ";
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
			</tr>
			<tr>

				<?php
				echo "<td>" . $formObj->getElement('Company name', 'label') . "</td>";
				echo "<td>" . $formObj->getElement('company_name', 'text') . "</td>    ";
				?>

			</tr>
			<tr>

				<?php
				echo "<td>" . $formObj->getElement('Company address', 'label') . "</td>";
				echo "<td>" . $formObj->getElement('company_address', 'textarea') . "</td>    ";
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
<?php
require 'footer.php';
?>