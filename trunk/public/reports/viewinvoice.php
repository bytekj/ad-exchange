<?php
require_once '../../Classes/UserInfo.php';
require_once '../../Classes/Session.php';
require_once '../../Classes/Content.php';
require_once '../../Classes/UIForm.php';
require_once '../../Classes/Adspot.php';
require_once '../../Classes/Campaign.php';
require_once '../../Classes/Advertiser.php';
require_once '../../Db/DataHandler.php';
require_once '../../include/global.php';
require_once '../../Classes/Reports.php';


$id = $_GET['id'];
$session = new Session();

$objUser = new UserInfo();
$objUser->getCurrentUserInfo();

$sql = "SELECT
		`id`,
		`type`,
		`item_serial`,
		`item_id`,
		`item_name`,
		`start_date`,
		`end_date`,
		`item_data`,
		`amount`,
		`currency`,
		`status`,
		`userid`,
		`date_of_generation`
		FROM `invoice`
		WHERE item_id=".$id." AND userid='".$objUser->userid."'";

$objData = new DataHandler();
$invoice =  $objData->GetQuery($sql);
if($invoice != -1){
	$invoice = $invoice[0];
}
if($_GET['debug'] == 1){
	echo $sql;
	echo "<pre>";
	print_r($invoice);
	echo "</pre>";
}
function getAmountDue($advertiserId, $amountDue, $Impressions, $CPM){
	if($advertiserId == 'zenga_adv'){
		
	}
}
?>
<html>
<body>
	<?php if($invoice != -1){ ?>
	<div style="text-align: right; width: 800px;">
		<font size="2pt"><a href="JavaScript:window.print();">Print this page</a>
		</font>
	</div>
	<div style="width: 800px; border: 1px solid; padding: 20px;">
		<label>INVOICE #<?php echo $invoice['item_serial']; ?>
		</label>
		<p>
			NOVIX Media Technologies Private Ltd<br> 201, Jinja, Opp Damani Post
			Office,<br> LBS Marg, Thane (West). Pin: 400602<br> <br> Date:<?php echo $invoice['date_of_generation']; ?>
			<br> <br> Bill To<br>
			<?php echo $objUser->firstname." ".$objUser->lastname."<br>".$objUser->company_address; ?>
			<br> <br>
		
		
		<table style="width: 100%">
			<tr>
				<th align="left" style="width: 200px;">Campaign Name</th>
				<th align="left" style="width: 200px;">Date of execution</th>
				<th align="right" style="width: 100px;">Impressions</th>
				<th align="right" style="width: 100px;">CPM</th>
				<th align="right">Amount Due(<?php echo $invoice['currency'] ?>)
				</th>
			</tr>
			<tr>
				<td align="left"><?php echo $invoice['item_name']; ?></td>
				<td align="left"><?php echo $invoice['start_date']." to ".$invoice['end_date']; ?>
				</td>
				<td align="right"><?php echo $invoice['item_data'] ?></td>
				<td align="right"><?php echo $invoice['amount'] ?></td>
			</tr>
		</table>
		<br> <br> Total Amount Due:
		<?php echo $invoice['currency'].$invoice['amount'] ?>
		<br>
		<p>
			Thank you for your business <br> <br>
		
		
		<p>
			Note:<br> 1. Amount payable within 15 days of invoice date<br> 2.
			Mode of Payment: Cheque payable to NOVIX Media Technologies Private
			Ltd addressed to our registered office address<br> Alternatively the
			amount can also be wire transfered to our bank account:<br> NOVIX
			Media Technologies Private Ltd<br> ICICI Bank, Thane branch<br> A/c
			number: 003505012313<br> IFSC Code: ICICI0000035<br>
	
	</div>
	<?php
}
else{
echo "Invoice will be generated either on campaign end date or on last day of the month";
} 
 ?>

</body>
</html>
