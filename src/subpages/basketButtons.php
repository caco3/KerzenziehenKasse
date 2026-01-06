<? 
$root="..";
require_once("$root/framework/credentials_check.php");

require_once("$root/config/config.php");
require_once("$root/framework/functions.php");
require_once("$root/framework/db.php");
db_connect();

?>

<!-- <button type=submit id=createreceiptButtonView class=createreceiptButtonView><img src="images/receipt.png" width=50px><br>Beleg</button>         -->


<table id=basketButtonsTable>
    <tr>
        <td>
            <button type=button id=cancelButton class=cancelButton onclick="cancelClicked();"><img src="images/clear_basket.png" height=45px><br>Abbrechen</button>
        </td>
		<? 
			$bookingId = getDbBookingId();
			if($bookingId == "new" ) { // basket filled with articles for a new booking
		?>
			<td>
					<button type=button id=cashButton class=cashButton onclick="cashClicked();"><img src="images/cash.png" height=45px><br>Bar bezahlt</button>
			</td>
			<td>
					<button type=button id=twintButton class=twintButton onclick="twintClicked();"><img src="images/twint.png" height=40px><br>Mit Twint bez.</button>
			</td>
			<td>
					<button type=button id=invoiceButton class=invoiceButton onclick="invoiceClicked();"><img src="images/invoice.png" height=40px><br>Rechnung</button>
			</td>
			
		<? } else { /* update existing booking */ ?>
			<td>
				<button type=button id=updateButton class=updateButton onclick="updateClicked();"><img src="images/update_basket.png" height=45px><br>Aktualisieren</button>
				
				&nbsp;&nbsp;&nbsp;Zahlungsart: 
				<?
				$booking = getDbBooking($bookingId);
				$paymentMethod = $booking['paymentMethod'];
				
				if ($paymentMethod == 'cash') { ?>
					<img src="images/cash.png" height=40px>
				<? } else if ($paymentMethod == 'twint') { ?>
					<img src="images/twint.png" height=40px>
				<? } else { /* invoice */ ?>
					<img src="images/invoice.png" height=40px>
				<? }				
				?>
			</td>
		<? } ?>
    </tr>
	</table>
   
